<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

/**
 * Creates the one hard-coded super admin.
 *
 * Values come from .env (SUPER_ADMIN_USERNAME / SUPER_ADMIN_PASSWORD etc), so
 * the credentials are never committed to the repository. Every other admin is
 * created from inside the admin panel by this account.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $config = config('moralenz.super_admin');

        $existing = Admin::where('username', $config['username'])->first();

        if ($existing) {
            // Make sure the bootstrap account can always get back in, but never
            // clobber a password that has since been changed from the panel.
            $existing->forceFill([
                'is_super_admin' => true,
                'is_active' => true,
            ])->save();

            $this->command?->info("Super admin [{$config['username']}] already exists — left untouched.");

            return;
        }

        if (strlen((string) $config['password']) < 10) {
            throw new \RuntimeException('Set SUPER_ADMIN_PASSWORD to at least 10 characters before creating the bootstrap admin.');
        }

        Admin::create([
            'name' => $config['name'],
            'username' => $config['username'],
            'email' => $config['email'],
            'password' => $config['password'],
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        $this->command?->warn("Super admin created: {$config['username']}");
        $this->command?->warn('Sign in to the admin panel and change this password now.');
    }
}
