{{-- @param \App\Models\TeamMember $member --}}
<article class="member-card" data-reveal>
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
