@extends('layouts.app')

@php
    // A past year's page names its year; the current team is just "Our Team".
    $isPastTerm = $term && ! $term->is_current && request()->routeIs('team.show');
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

            @if ($terms->count() > 1)
                <nav class="team-years" aria-label="Teams by year">
                    <span class="team-years__label">Teams by year</span>
                    <ul>
                        @foreach ($terms as $item)
                            <li>
                                <a href="{{ $item->is_current ? \App\Support\Links::team() : \App\Support\Links::teamTerm($item) }}"
                                   class="team-years__link{{ $term && $item->id === $term->id ? ' is-active' : '' }}"
                                   @if ($term && $item->id === $term->id) aria-current="page" @endif>
                                    {{ $item->name }}
                                    @if ($item->is_current)
                                        <small>Current</small>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
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

@endsection
