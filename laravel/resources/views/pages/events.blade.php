@extends('layouts.app')

@section('title', 'Events | Mora Lenz')
@section('description', 'Workshops, exhibitions and photo walks hosted by Mora Lenz at the University of Moratuwa.')

@section('content')

    <div class="container">
        <div class="page-head">
            <h1 class="page-head__title">Upcoming Events</h1>
            <p class="page-head__lede">
                Join us for workshops, exhibitions, and photo walks. Capture moments with the
                Mora Lenz community.
            </p>
        </div>
    </div>

    <section class="section" style="padding-top: 40px;">
        <div class="container">
            <div class="card-grid">
                @forelse ($events as $event)
                    <a href="{{ route('events.show', $event->slug) }}" class="event-card" data-reveal>
                        <div class="event-card__media">
                            @if ($event->image_url)
                                <img src="{{ $event->image_url }}" alt="{{ $event->title }}" loading="lazy">
                            @else
                                <div class="placeholder-media">
                                    @include('partials.icons', ['icon' => 'image'])
                                </div>
                            @endif

                            <span class="event-card__date">
                                @include('partials.icons', ['icon' => 'calendar'])
                                {{ $event->event_date->format('M j') }}
                            </span>
                        </div>

                        <div class="event-card__body">
                            <h2 class="event-card__title">{{ $event->title }}</h2>

                            <span class="event-card__place">
                                @include('partials.icons', ['icon' => 'map-pin'])
                                {{ $event->location ?: 'Location TBD' }}
                            </span>

                            <p class="event-card__desc">
                                {{ $event->description ?: 'No description available.' }}
                            </p>

                            <span class="event-card__more">
                                View Details
                                @include('partials.icons', ['icon' => 'arrow-right'])
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">
                        <p>No upcoming events scheduled yet.</p>
                        <p>Check back soon.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

@endsection
