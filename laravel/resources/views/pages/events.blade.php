@extends('layouts.app')

@section('title', 'Events | Mora Lenz')
@section('description', 'Workshops, exhibitions and photo walks hosted by Mora Lenz at the University of Moratuwa, and the photos from every past event.')

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
                    <a href="{{ \App\Support\Links::event($event) }}" class="event-card" data-reveal>
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

    @if ($pastEvents->isNotEmpty())
        <section class="section gallery-section" id="past">
            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">Past Events</h2>
                    <p class="section__lede">Photos and Facebook albums from the events we have already held.</p>
                </div>

                <div class="gallery-album-grid">
                    @foreach ($pastEvents as $past)
                        @php
                            $photos = $past->photos->map(fn ($p) => [
                                'src' => $p->image_url,
                                'caption' => $p->caption,
                            ])->values();

                            // An event without an album still gets its cover on the card.
                            $thumbs = $photos->isNotEmpty()
                                ? $photos->take(3)->pluck('src')
                                : collect(array_filter([$past->image_url]));

                            $fbLinks = $past->facebookLinks();
                        @endphp

                        <article class="gallery-album" data-reveal>
                            @if ($photos->isNotEmpty())
                                <button type="button" class="gallery-album__open"
                                        data-lightbox-open
                                        data-title="{{ $past->title }}"
                                        data-photos="{{ json_encode($photos) }}"
                                        aria-label="Open photos from {{ $past->title }} — {{ $photos->count() }} {{ $photos->count() === 1 ? 'photo' : 'photos' }}">
                                    <span class="gallery-mosaic gallery-mosaic--{{ min($thumbs->count(), 3) }}">
                                        @foreach ($thumbs as $src)
                                            <img src="{{ $src }}" alt="" loading="lazy">
                                        @endforeach
                                    </span>
                                    <span class="gallery-album__badge">
                                        @include('partials.icons', ['icon' => 'image'])
                                        {{ $photos->count() }}
                                    </span>
                                    <span class="gallery-album__view">View photos</span>
                                </button>
                            @elseif ($thumbs->isNotEmpty())
                                <a class="gallery-album__open" href="{{ \App\Support\Links::event($past) }}" aria-label="{{ $past->title }}">
                                    <span class="gallery-mosaic gallery-mosaic--1">
                                        <img src="{{ $thumbs->first() }}" alt="" loading="lazy">
                                    </span>
                                </a>
                            @else
                                <div class="gallery-album__open gallery-album__open--empty">
                                    <span class="gallery-album__view">Photos coming soon</span>
                                </div>
                            @endif

                            <div class="gallery-album__body">
                                <div class="gallery-album__meta">
                                    <span>{{ $past->event_date->format('M j, Y') }}</span>
                                    @if ($photos->isNotEmpty())
                                        <span>{{ $photos->count() }} {{ $photos->count() === 1 ? 'photo' : 'photos' }}</span>
                                    @endif
                                </div>
                                <h3 class="gallery-album__title">
                                    <a href="{{ \App\Support\Links::event($past) }}" style="color: inherit; text-decoration: none;">{{ $past->title }}</a>
                                </h3>
                                @if ($past->description)
                                    <p class="gallery-album__description">{{ $past->description }}</p>
                                @endif
                                @foreach ($fbLinks as $fbUrl)
                                    <a class="gallery-album__facebook" href="{{ $fbUrl }}" target="_blank" rel="noopener noreferrer">
                                        Open Facebook album{{ count($fbLinks) > 1 ? ' '.$loop->iteration : '' }} <span aria-hidden="true">↗</span>
                                    </a>
                                @endforeach
                                <a class="gallery-album__facebook" href="{{ \App\Support\Links::event($past) }}">
                                    Event details <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        @include('partials.lightbox')
    @endif

@endsection
