<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Models\TeamGroup;
use App\Models\TeamMember;
use App\Models\TeamSubgroup;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Group -> Subgroup -> Member management.
 *
 * One controller for the whole tree, because the three levels share validation
 * and the panel edits them together on one screen.
 */
class TeamController extends Controller
{
    use ReordersRecords;

    /** The full tree in one request — what the panel's Team tab renders from. */
    public function tree(): JsonResponse
    {
        $groups = TeamGroup::with([
            'directMembers',
            'subgroups',
            'subgroups.members',
        ])->orderBy('sort_order')->get();

        return response()->json([
            'data' => $groups->map(fn (TeamGroup $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'sort_order' => (int) $group->sort_order,
                'is_active' => (bool) $group->is_active,
                'direct_members' => $group->directMembers->map(fn ($m) => $this->presentMember($m))->values(),
                'subgroups' => $group->subgroups->map(fn (TeamSubgroup $sub) => [
                    'id' => $sub->id,
                    'group_id' => $sub->group_id,
                    'name' => $sub->name,
                    'description' => $sub->description,
                    'sort_order' => (int) $sub->sort_order,
                    'is_active' => (bool) $sub->is_active,
                    'members' => $sub->members->map(fn ($m) => $this->presentMember($m))->values(),
                ])->values(),
            ]),
        ]);
    }

    // ---------------------------------------------------------------- groups

    public function storeGroup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:300'],
            'is_active' => ['boolean'],
        ]);

        $group = TeamGroup::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => $this->nextSortOrder(TeamGroup::class),
        ]);

        return response()->json(['data' => $this->presentGroup($group)], 201);
    }

    public function updateGroup(Request $request, TeamGroup $group): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:300'],
            'is_active' => ['boolean'],
        ]);

        $group->fill(array_intersect_key($data, array_flip(['name', 'description'])));

        if (array_key_exists('is_active', $data)) {
            $group->is_active = (bool) $data['is_active'];
        }

        $group->save();

        return response()->json(['data' => $this->presentGroup($group)]);
    }

    /** Deleting a group cascades to its subgroups and members in the database. */
    public function destroyGroup(TeamGroup $group): JsonResponse
    {
        foreach ($group->members()->pluck('image_url') as $url) {
            ImageStore::delete($url);
        }

        $group->delete();

        return response()->json(['message' => 'Group deleted.']);
    }

    public function reorderGroups(Request $request): JsonResponse
    {
        return $this->applyOrder($request, TeamGroup::class);
    }

    // ------------------------------------------------------------- subgroups

    public function storeSubgroup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'group_id' => ['required', 'integer', 'exists:team_groups,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:300'],
            'is_active' => ['boolean'],
        ]);

        $subgroup = TeamSubgroup::create([
            'group_id' => $data['group_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => $this->nextSortOrder(TeamSubgroup::class, ['group_id' => $data['group_id']]),
        ]);

        return response()->json(['data' => $this->presentSubgroup($subgroup)], 201);
    }

    public function updateSubgroup(Request $request, TeamSubgroup $subgroup): JsonResponse
    {
        $data = $request->validate([
            'group_id' => ['sometimes', 'integer', 'exists:team_groups,id'],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:300'],
            'is_active' => ['boolean'],
        ]);

        // Moving a subgroup to another group has to take its members with it,
        // otherwise a member would point at a group that no longer contains them.
        if (array_key_exists('group_id', $data) && (int) $data['group_id'] !== (int) $subgroup->group_id) {
            $subgroup->group_id = $data['group_id'];
            TeamMember::where('subgroup_id', $subgroup->id)
                ->update(['group_id' => $data['group_id']]);
        }

        $subgroup->fill(array_intersect_key($data, array_flip(['name', 'description'])));

        if (array_key_exists('is_active', $data)) {
            $subgroup->is_active = (bool) $data['is_active'];
        }

        $subgroup->save();

        return response()->json(['data' => $this->presentSubgroup($subgroup)]);
    }

    /**
     * Members of a deleted subgroup are kept and moved up to the parent group,
     * so removing a subgroup never silently deletes people.
     */
    public function destroySubgroup(TeamSubgroup $subgroup): JsonResponse
    {
        TeamMember::where('subgroup_id', $subgroup->id)->update([
            'subgroup_id' => null,
            'group_id' => $subgroup->group_id,
        ]);

        $subgroup->delete();

        return response()->json(['message' => 'Subgroup deleted. Its members moved up to the group.']);
    }

    public function reorderSubgroups(Request $request): JsonResponse
    {
        return $this->applyOrder($request, TeamSubgroup::class);
    }

    // --------------------------------------------------------------- members

    public function storeMember(Request $request): JsonResponse
    {
        $data = $this->validateMember($request);

        $member = new TeamMember;
        $this->fillMember($member, $data, $request);
        $member->sort_order = $this->nextSortOrder(TeamMember::class, [
            'group_id' => $member->group_id,
            'subgroup_id' => $member->subgroup_id,
        ]);
        $member->save();

        return response()->json(['data' => $this->presentMember($member)], 201);
    }

    public function updateMember(Request $request, TeamMember $member): JsonResponse
    {
        $data = $this->validateMember($request, $member);

        $this->fillMember($member, $data, $request);
        $member->save();

        return response()->json(['data' => $this->presentMember($member)]);
    }

    public function destroyMember(TeamMember $member): JsonResponse
    {
        ImageStore::delete($member->image_url);
        $member->delete();

        return response()->json(['message' => 'Member deleted.']);
    }

    public function reorderMembers(Request $request): JsonResponse
    {
        return $this->applyOrder($request, TeamMember::class);
    }

    protected function validateMember(Request $request, ?TeamMember $member = null): array
    {
        return $request->validate([
            'group_id' => [$member ? 'sometimes' : 'required', 'integer', 'exists:team_groups,id'],
            'subgroup_id' => ['nullable', 'integer', Rule::exists('team_subgroups', 'id')],
            'name' => [$member ? 'sometimes' : 'required', 'string', 'max:150'],
            'profession' => [$member ? 'sometimes' : 'required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'remove_image' => ['boolean'],
            'is_active' => ['boolean'],
        ]);
    }

    protected function fillMember(TeamMember $member, array $data, Request $request): void
    {
        foreach (['name', 'profession', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $member->{$field} = $data[$field];
            }
        }

        if (array_key_exists('group_id', $data)) {
            $member->group_id = $data['group_id'];
        }

        // Choosing a subgroup decides the group: the two can never disagree.
        if (array_key_exists('subgroup_id', $data)) {
            if (blank($data['subgroup_id'])) {
                $member->subgroup_id = null;
            } else {
                $subgroup = TeamSubgroup::find($data['subgroup_id']);

                if (! $subgroup) {
                    throw ValidationException::withMessages([
                        'subgroup_id' => 'That subgroup no longer exists.',
                    ]);
                }

                $member->subgroup_id = $subgroup->id;
                $member->group_id = $subgroup->group_id;
            }
        }

        if (array_key_exists('is_active', $data)) {
            $member->is_active = (bool) $data['is_active'];
        }

        if ($request->hasFile('image')) {
            $member->image_url = ImageStore::replace($request->file('image'), 'team', $member->image_url);
        } elseif (! empty($data['remove_image'])) {
            ImageStore::delete($member->image_url);
            $member->image_url = null;
        }
    }

    protected function presentGroup(TeamGroup $group): array
    {
        return [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
            'sort_order' => (int) $group->sort_order,
            'is_active' => (bool) $group->is_active,
        ];
    }

    protected function presentSubgroup(TeamSubgroup $subgroup): array
    {
        return [
            'id' => $subgroup->id,
            'group_id' => $subgroup->group_id,
            'name' => $subgroup->name,
            'description' => $subgroup->description,
            'sort_order' => (int) $subgroup->sort_order,
            'is_active' => (bool) $subgroup->is_active,
        ];
    }

    protected function presentMember(TeamMember $member): array
    {
        return [
            'id' => $member->id,
            'group_id' => $member->group_id,
            'subgroup_id' => $member->subgroup_id,
            'name' => $member->name,
            'profession' => $member->profession,
            'description' => $member->description,
            'image_url' => $member->image_url,
            'sort_order' => (int) $member->sort_order,
            'is_active' => (bool) $member->is_active,
        ];
    }
}
