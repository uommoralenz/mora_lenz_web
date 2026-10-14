{{--
    A grid of team-year cards, each linking to that year's team page.

    @param \Illuminate\Support\Collection $terms  TeamTerm rows, with member_count
    @param int|null $activeId  The term on screen, drawn as selected
--}}
<ul class="about-teams__grid">
    @foreach ($terms as $item)
        <li>
            <a class="about-teams__card{{ ($activeId ?? null) === $item->id ? ' is-active' : '' }}" data-reveal
               href="{{ $item->is_current ? \App\Support\Links::team() : \App\Support\Links::teamTerm($item) }}"
               @if (($activeId ?? null) === $item->id) aria-current="page" @endif>
                <span class="about-teams__year">{{ $item->name }}</span>
                @if ($item->is_current)
                    <span class="about-teams__tag">Current team</span>
                @endif
                @if ($item->member_count)
                    <span class="about-teams__count">{{ $item->member_count }} {{ $item->member_count === 1 ? 'member' : 'members' }}</span>
                @endif
                <span class="about-teams__more">
                    View team
                    @include('partials.icons', ['icon' => 'arrow-right'])
                </span>
            </a>
        </li>
    @endforeach
</ul>
