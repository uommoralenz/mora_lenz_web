{{--
    Renders one collection of team members as a hierarchy rather than a single
    flat grid.

    Members carry a tier (1 = the top role, 2 = the office-bearers under it,
    3 = everyone else). Each tier that has anyone in it becomes its own row,
    drawn at its own size, so a body like the Executive Committee reads as a
    structure: the President above the Secretary and Treasurer, and those above
    the general members.

    A collection where nobody has been ranked is all tier 3, which renders as
    exactly the one grid this page always showed.

    @param \Illuminate\Support\Collection $members
--}}
@php
    $members = collect($members ?? []);

    // Ordered 1, 2, 3 and keeping only the tiers that actually have members, so
    // an unused rank leaves no empty row behind.
    $tiers = $members
        ->groupBy(fn ($member) => in_array((int) $member->tier, [1, 2, 3], true) ? (int) $member->tier : 3)
        ->sortKeys();

    $tierClass = [1 => 'is-lead', 2 => 'is-senior', 3 => 'is-member'];
@endphp

@if ($members->isNotEmpty())
    <div class="member-tiers">
        @foreach ($tiers as $tier => $tierMembers)
            <div class="member-grid member-grid--{{ $tierClass[$tier] }}">
                @foreach ($tierMembers as $member)
                    @include('partials.member-card', ['member' => $member, 'tier' => $tier])
                @endforeach
            </div>
        @endforeach
    </div>
@endif
