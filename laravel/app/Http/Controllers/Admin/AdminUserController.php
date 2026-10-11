<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Support\AdminAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Support\AdminMail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Admin account management. Every route here is behind the 'admin.super'
 * middleware, so only a super admin can reach it.
 *
 * There is no self-service sign-up or reset by design: the system emails a
 * generated temporary password, and the admin must replace it at first sign-in.
 */
class AdminUserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Admin::orderByDesc('is_super_admin')
                ->orderBy('name')
                ->get()
                ->map(fn (Admin $a) => $this->present($a)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:60', 'alpha_dash', 'unique:admins,username'],
            // Required: the temporary password is sent here.
            'email' => ['required', 'email:rfc', 'max:180'],
            'is_super_admin' => ['boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(Admin::PERMISSIONS)],
            'is_active' => ['boolean'],
        ]);

        $plain = AdminMail::generatePassword();

        $admin = Admin::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $plain,
            'must_change_password' => true,
            'is_super_admin' => (bool) ($data['is_super_admin'] ?? false),
            'permissions' => array_values(array_unique($data['permissions'] ?? [])),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return response()->json($this->withMailResult($admin, $plain, false), 201);
    }

    /** Generate a new temporary password, email it, and force a change at next sign-in. */
    public function resetPassword(Admin $admin): JsonResponse
    {
        if (blank($admin->email)) {
            throw ValidationException::withMessages([
                'email' => 'This admin has no email address saved. Add one first.',
            ]);
        }

        $plain = AdminMail::generatePassword();

        $admin->forceFill([
            'password' => $plain,
            'must_change_password' => true,
        ])->save();

        AdminAuth::revokeAllFor($admin);

        return response()->json($this->withMailResult($admin, $plain, true));
    }

    /**
     * If the email could not be sent, the temporary password is returned once
     * so the super admin can hand it over by other means. It is never stored
     * in plain text and is not returned when the email went out.
     */
    protected function withMailResult(Admin $admin, string $plain, bool $isReset): array
    {
        $sent = AdminMail::sendPassword($admin, $plain, $isReset);

        return [
            'data' => $this->present($admin),
            'mail_sent' => $sent,
            'temporary_password' => $sent ? null : $plain,
        ];
    }

    public function update(Request $request, Admin $admin): JsonResponse
    {
        $actor = AdminAuth::current();

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'username' => [
                'sometimes', 'required', 'string', 'min:3', 'max:60', 'alpha_dash',
                Rule::unique('admins', 'username')->ignore($admin->id),
            ],
            'email' => ['nullable', 'email:rfc', 'max:180'],
            'is_super_admin' => ['boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(Admin::PERMISSIONS)],
            'is_active' => ['boolean'],
        ]);

        $wouldDemote = array_key_exists('is_super_admin', $data) && ! $data['is_super_admin'];
        $wouldDeactivate = array_key_exists('is_active', $data) && ! $data['is_active'];

        // Never let the last remaining super admin be demoted or switched off:
        // that would lock everyone out of admin management permanently.
        if (($wouldDemote || $wouldDeactivate) && $admin->isLastSuperAdmin()) {
            throw ValidationException::withMessages([
                'is_super_admin' => 'This is the only super admin. Promote another admin first.',
            ]);
        }

        // You also cannot strip your own super admin rights by accident.
        if ($actor && $actor->id === $admin->id && ($wouldDemote || $wouldDeactivate)) {
            throw ValidationException::withMessages([
                'is_super_admin' => 'You cannot remove your own access.',
            ]);
        }

        $admin->fill(array_filter([
            'name' => $data['name'] ?? null,
            'username' => $data['username'] ?? null,
        ], fn ($v) => $v !== null));

        if (array_key_exists('email', $data)) {
            $admin->email = $data['email'];
        }

        if (array_key_exists('is_super_admin', $data)) {
            $admin->is_super_admin = (bool) $data['is_super_admin'];
        }

        if (array_key_exists('permissions', $data)) {
            $admin->permissions = array_values(array_unique($data['permissions'] ?? []));
        }

        if (array_key_exists('is_active', $data)) {
            $admin->is_active = (bool) $data['is_active'];
        }

        $admin->save();

        // A deactivation must end that admin's sessions immediately, not
        // whenever their token happens to expire.
        if (! $admin->is_active) {
            AdminAuth::revokeAllFor($admin);
        }

        return response()->json(['data' => $this->present($admin)]);
    }

    public function destroy(Admin $admin): JsonResponse
    {
        $actor = AdminAuth::current();

        if ($actor && $actor->id === $admin->id) {
            throw ValidationException::withMessages([
                'id' => 'You cannot delete your own account.',
            ]);
        }

        if ($admin->isLastSuperAdmin()) {
            throw ValidationException::withMessages([
                'id' => 'This is the only super admin and cannot be deleted.',
            ]);
        }

        AdminAuth::revokeAllFor($admin);
        $admin->delete();

        return response()->json(['message' => 'Admin deleted.']);
    }

    protected function present(Admin $admin): array
    {
        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'username' => $admin->username,
            'email' => $admin->email,
            'is_super_admin' => $admin->isSuperAdmin(),
            'permissions' => $admin->effectivePermissions(),
            'must_change_password' => (bool) $admin->must_change_password,
            'is_active' => (bool) $admin->is_active,
            'last_login_at' => $admin->last_login_at?->toIso8601String(),
            'created_at' => $admin->created_at?->toIso8601String(),
        ];
    }
}
