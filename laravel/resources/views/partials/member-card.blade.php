{{--
    @param \App\Models\TeamMember $member
    @param int|null $tier  The rank row this card sits in (1 lead, 2 senior,
                           3 member). Changes only the card's size; leave it out
                           for the plain one.
--}}
@php
    $cardTier = match ((int) ($tier ?? 0)) {
        1 => ' member-card--lead',
        2 => ' member-card--senior',
        default => '',
    };
@endphp

<article class="member-card{{ $cardTier }}" data-reveal>
    <div class="member-card__avatar">
        @if ($member->image_url)
            <img src="{{ $member->image_url }}" alt="{{ $member->name }}" loading="lazy">
        @else
            <span class="member-card__initial" aria-hidden="true">{{ $member->initial() }}</span>
        @endif
    </div>

    <h4 class="member-card__name">{{ $member->name }}</h4>
    <p class="member-card__role">{{ $member->profession }}</p>

    @if ($member->description)
        <p class="member-card__bio">{{ $member->description }}</p>
    @endif
</article>
