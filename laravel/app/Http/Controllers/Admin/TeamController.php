<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Models\TeamGroup;
use App\Models\TeamMember;
use App\Models\TeamSubgroup;
use App\Models\TeamTerm;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Term -> Group -> Subgroup -> Member management.
 *
 * A term is one year's team. Groups belong to a term, so last year's
 * committee stays exactly as it was when this year's is entered.
 *
 * One controller for the whole tree, because the three levels share validation
 * and the panel edits them together on one screen.
 */
class TeamController extends Controller
{
    use ReordersRecords;

    /**
     * One term's full tree in one request — what the panel's Team tab renders
     * from. ?term=<id> picks the term; without it, the current one.
     */
    public function tree(Request $request): JsonResponse
    {
        $terms = TeamTerm::withCount([
            'groups',
            'groups as member_count' => fn ($q) => $q->join('team_members', 'team_members.group_id', '=', 'team_groups.id'),
        ])->newest()->get();

        $term = $terms->firstWhere('id', (int) $request->query('term'))
            ?? $terms->firstWhere('is_current', true)
            ?? $terms->first();

        $groups = $term
            ? TeamGroup::with([
                'directMembers',
                'subgroups',
                'subgroups.members',
            ])->where('term_id', $term->id)->orderBy('sort_order')->get()
            : collect();

        return response()->json([
            'terms' => $terms->map(fn (TeamTerm $t) => $this->presentTerm($t))->values(),
            'term' => $term ? $this->presentTerm($term) : null,
            'data' => $groups->map(fn (TeamGroup $group) => [
                'id' => $group->id,
                'term_id' => $group->term_id,
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

    // ----------------------------------------------------------------- terms

    /**
     * Start a new year's team. copy_from copies that term's groups and
     * subgroups (not its members, who change every year) so the new
     * committee can be filled in without rebuilding the structure.
     */
    public function storeTerm(Request $request): JsonResponse
    {
        $data = $this->validateTerm($request);
        $extra = $request->validate([
            'copy_from' => ['nullable', 'integer', 'exists:team_terms,id'],
        ]);

        $term = DB::transaction(function () use ($data, $extra) {
            $term = new TeamTerm;
            $this->fillTerm($term, $data);
            $term->save();

            if (! empty($extra['copy_from'])) {
                $this->copyStructure(TeamTerm::findOrFail($extra['copy_from']), $term);
            }

            $this->syncCurrent($term);

            return $term;
        });

        return response()->json(['data' => $this->presentTerm($term->loadCount('groups'))], 201);
    }

    public function updateTerm(Request $request, TeamTerm $term): JsonResponse
    {
        $data = $this->validateTerm($request, $term);

        $this->fillTerm($term, $data);
        $term->save();
        $this->syncCurrent($term);

        return response()->json(['data' => $this->presentTerm($term->loadCount('groups'))]);
    }

    /** Deletes the term with every group, subgroup and member in it. */
    public function destroyTerm(TeamTerm $term): JsonResponse
    {
        if ($term->is_current) {
            throw ValidationException::withMessages([
                'term' => 'This is the current team. Mark another term as current before deleting it.',
            ]);
        }

        $photos = TeamMember::whereIn('group_id', $term->groups()->select('id'))->pluck('image_url');

        $term->delete();

        foreach ($photos as $url) {
            ImageStore::delete($url);
        }

        return response()->json(['message' => 'Team deleted.']);
    }

    protected function validateTerm(Request $request, ?TeamTerm $term = null): array
    {
        return $request->validate([
            'name' => [$term ? 'sometimes' : 'required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash', Rule::unique('team_terms', 'slug')->ignore($term?->id)],
            'year' => [$term ? 'sometimes' : 'required', 'integer', 'min:1990', 'max:2100'],
            'description' => ['nullable', 'string', 'max:300'],
            'is_current' => ['boolean'],
            'is_active' => ['boolean'],
        ]);
    }

    protected function fillTerm(TeamTerm $term, array $data): void
    {
        foreach (['name', 'year', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $term->{$field} = $data[$field];
            }
        }

        foreach (['is_current', 'is_active'] as $flag) {
            if (array_key_exists($flag, $data)) {
                $term->{$flag} = (bool) $data[$flag];
            }
        }

        // A blank slug means "build it from the name", e.g. 2025/26 -> 2025-26.
        if (array_key_exists('slug', $data) || ! $term->exists) {
            $term->slug = TeamTerm::uniqueSlug(
                filled($data['slug'] ?? null) ? $data['slug'] : $term->name,
                $term->id
            );
        }
    }

    /** Only one term is the current team, so marking one unmarks the rest. */
    protected function syncCurrent(TeamTerm $term): void
    {
        if ($term->is_current) {
            TeamTerm::where('id', '!=', $term->id)->where('is_current', true)->update(['is_current' => false]);
        }
    }

    protected function copyStructure(TeamTerm $from, TeamTerm $to): void
    {
        foreach ($from->groups()->with('subgroups')->get() as $group) {
            $copy = $to->groups()->create([
                'name' => $group->name,
                'description' => $group->description,
                'sort_order' => $group->sort_order,
                'is_active' => $group->is_active,
            ]);

            foreach ($group->subgroups as $subgroup) {
                $copy->subgroups()->create([
                    'name' => $subgroup->name,
                    'description' => $subgroup->description,
                    'sort_order' => $subgroup->sort_order,
                    'is_active' => $subgroup->is_active,
                ]);
            }
        }
    }

    // ---------------------------------------------------------------- groups

    public function storeGroup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'term_id' => ['required', 'integer', 'exists:team_terms,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:300'],
            'is_active' => ['boolean'],
        ]);

        $group = TeamGroup::create([
            'term_id' => $data['term_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => $this->nextSortOrder(TeamGroup::class, ['term_id' => $data['term_id']]),
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
            'tier' => ['nullable', 'integer', Rule::in(TeamMember::TIERS)],
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

        // Leaving the rank unset means "an ordinary member", the bottom tier.
        if (array_key_exists('tier', $data)) {
            $member->tier = (int) ($data['tier'] ?: TeamMember::TIER_MEMBER);
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

    protected function presentTerm(TeamTerm $term): array
    {
        return [
            'id' => $term->id,
            'name' => $term->name,
            'slug' => $term->slug,
            'year' => (int) $term->year,
            'description' => $term->description,
            'is_current' => (bool) $term->is_current,
            'is_active' => (bool) $term->is_active,
            'group_count' => (int) ($term->groups_count ?? $term->groups()->count()),
            'member_count' => (int) ($term->member_count ?? 0),
        ];
    }

    protected function presentGroup(TeamGroup $group): array
    {
        return [
            'id' => $group->id,
            'term_id' => $group->term_id,
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
            'tier' => (int) ($member->tier ?: TeamMember::TIER_MEMBER),
            'description' => $member->description,
            'image_url' => $member->image_url,
            'sort_order' => (int) $member->sort_order,
            'is_active' => (bool) $member->is_active,
        ];
    }
}
