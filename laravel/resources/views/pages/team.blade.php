@extends('layouts.app')

@section('title', 'Our Team | Mora Lenz')
@section('description', 'Meet the photographers, videographers and organisers behind Mora Lenz at the University of Moratuwa.')

@section('content')

    <section class="section team" style="padding-top: calc(var(--nav-h) + 56px);">
        <div class="container">
            <div class="section__head">
                <h1 class="section__title">Our Team</h1>
                <p class="section__lede">
                    Meet the talented individuals who make the Mora Lenz Mass Media Club a thriving
                    community of creative professionals.
                </p>
            </div>

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
