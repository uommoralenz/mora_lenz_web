@extends('layouts.app')

@php
    // A past year's page names its year; the current team is just "Our Team".
    $isPastTerm = $term && ! $term->is_current && request()->routeIs('team.show');

    // Every year other than the current one, newest first.
    $pastTerms = $terms->reject(fn ($t) => $t->is_current)->values();
@endphp

@section('title', ($isPastTerm ? 'Team '.$term->name : 'Our Team').' | Mora Lenz')
@section('description', 'Meet the photographers, videographers and organisers behind Mora Lenz at the University of Moratuwa.')

@section('content')

    <section class="section team" style="padding-top: calc(var(--nav-h) + 56px);">
        <div class="container">
            <div class="section__head">
                @if ($isPastTerm)
                    <span class="gallery-hero__eyebrow">Past team</span>
                    <h1 class="section__title">The {{ $term->name }} Team</h1>
                @else
                    <h1 class="section__title">Our Team</h1>
                    @if ($term)
                        <span class="gallery-hero__eyebrow">{{ $term->name }}</span>
                    @endif
                @endif
                <p class="section__lede">
                    {{ $term?->description ?: 'Meet the talented individuals who make the Mora Lenz Mass Media Club a thriving community of creative professionals.' }}
                </p>
            </div>

            {{-- The current team comes first; earlier years are below it. --}}
            @if ($isPastTerm)
                <p class="team-years">
                    <a class="team-years__link" href="{{ \App\Support\Links::team() }}">
                        @include('partials.icons', ['icon' => 'arrow-left'])
                        Back to the current team
                    </a>
                    <a class="team-years__link" href="#past-teams">All past teams</a>
                </p>
            @elseif ($pastTerms->isNotEmpty())
                <p class="team-years">
                    <a class="team-years__link" href="#past-teams">
                        See past teams ({{ $pastTerms->count() }})
                    </a>
                </p>
            @endif

            @if ($groups->isEmpty())
                <div class="empty-state">
                    <p>The team roster is being updated.</p>
                    <p>Check back soon.</p>
                </div>
            @else
                <div class="team__groups">
                    @foreach ($groups as $group)
                        <div class="team__group">
                            <div class="team__group-head">
                                <h2>{{ $group->name }}</h2>
                                {{-- "General Members" was a placeholder in the original data, not a real caption. --}}
                                @if ($group->description && $group->description !== 'General Members')
                                    <p>{{ $group->description }}</p>
                                @endif
                            </div>

                            {{-- Ranked rows rather than one flat grid — see partials.member-tiers. --}}
                            @include('partials.member-tiers', ['members' => $group->directMembers])

                            @foreach ($group->subgroups as $subgroup)
                                @continue($subgroup->members->isEmpty())

                                <div class="team__subgroup">
                                    <div class="team__subgroup-head">
                                        <h3>{{ $subgroup->name }}</h3>
                                        @if ($subgroup->description)
                                            <p>{{ $subgroup->description }}</p>
                                        @endif
                                    </div>

                                    @include('partials.member-tiers', ['members' => $subgroup->members])
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($pastTerms->isNotEmpty())
        <section class="section about-teams" id="past-teams">
            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">Past Teams</h2>
                    <p class="section__lede">The committees that led Mora Lenz in earlier years.</p>
                </div>

                @include('partials.team-years', [
                    'terms' => $pastTerms,
                    'activeId' => $isPastTerm ? $term->id : null,
                ])
            </div>
        </section>
    @endif

@endsection
