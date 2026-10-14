@extends('layouts.app')

@section('title', 'Mora Lenz | Visual Storytelling')
@section('description', 'Mora Lenz is the Mass Media Club of the University of Moratuwa. Event coverage, photography, films and a community of creators.')

@section('content')

    {{-- ------------------------------------------------------------ hero --}}
    <section class="hero" id="hero">
        <div class="hero__bg">
            @if (file_exists(public_path('img/hero-campus-night.jpg')))
                <img src="{{ asset('img/hero-campus-night.jpg') }}" alt="" fetchpriority="high">
            @elseif (file_exists(public_path('img/hero-campus-night.png')))
                <img src="{{ asset('img/hero-campus-night.png') }}" alt="" fetchpriority="high">
            @elseif (file_exists(public_path('img/landing-bg.jpeg')))
                <img src="{{ asset('img/landing-bg.jpeg') }}" alt="" fetchpriority="high">
            @endif
        </div>

        <div class="hero__wordmark" aria-hidden="true">
            <svg class="hero__wordmark-svg" viewBox="0 0 720 330" focusable="false">
                <text class="hero__wordmark-text" x="700" y="145" text-anchor="end">MORA</text>
                <text class="hero__wordmark-text" x="700" y="310" text-anchor="end">LENZ</text>
            </svg>
        </div>

        <div class="hero__inner">
            <div class="hero__content">
                <span class="hero__eyebrow">
                    <span class="hero__eyebrow-copy">
                        <span>Mass Media Club</span>
                        <span>University of Moratuwa</span>
                    </span>
                </span>

                <h1 class="hero__title">
                    <span class="hero__line">
                        Turning
                        <span class="hero__slider" aria-hidden="true">
                            <span class="hero__slider-track">
                                @foreach (['Moments', 'Stories', 'Emotions', 'Visions', 'Dreams', 'Reality', 'Ideas', 'Life'] as $word)
                                    <span>{{ $word }}</span>
                                @endforeach
                            </span>
                        </span>
                        <span class="visually-hidden">Moments</span>
                    </span>
                    <span class="hero__line">into Timeless</span>
                    <span class="hero__line">Visual Memories</span>
                </h1>

                <p class="hero__lede">
                    Mora Lenz is the Mass Media Club of the University of Moratuwa &mdash; our
                    photographers and videographers capture the essence of university life and beyond.
                </p>

                <div class="hero__actions">
                    <a href="{{ \App\Support\Links::pastEvents() }}" class="cta">
                        <span class="cta__label">Past Events</span>
                        @include('partials.icons', ['icon' => 'arrow-right'])
                    </a>
                    <a href="{{ \App\Support\Links::events() }}" class="cta cta--ghost">
                        <span class="cta__label">Upcoming Events</span>
                        @include('partials.icons', ['icon' => 'arrow-right'])
                    </a>
                </div>
            </div>
        </div>

    </section>

    {{-- -------------------------------------------------- featured event --}}
    @if ($featuredEvent)
        @php $countdownTarget = $featuredEvent->countdownTarget(); @endphp

        <section class="featured">
            <div class="container">
                <article class="featured__card">
                    <div class="featured__bg">
                        @if ($featuredEvent->image_url)
                            <img src="{{ $featuredEvent->image_url }}" alt="" loading="lazy">
                        @endif
                    </div>

                    <div class="featured__body">
                        <div class="featured__text">
                            <span class="featured__label">
                                <span class="featured__label-icon" aria-hidden="true">&#10022;</span>
                                <span>Featured Event</span>
                            </span>

                            <h2 class="featured__title">{{ $featuredEvent->title }}</h2>

                            @if ($featuredEvent->description)
                                <p class="featured__desc">{{ $featuredEvent->description }}</p>
                            @endif

                            <div class="meta-list">
                                <span class="meta-list__item">
                                    @include('partials.icons', ['icon' => 'calendar'])
                                    {{ $featuredEvent->event_date->format('l, F j') }}
                                </span>
                                <span class="meta-list__item">
                                    @include('partials.icons', ['icon' => 'clock'])
                                    {{ $featuredEvent->event_date->format('g:i A') }}
                                </span>
                                @if ($featuredEvent->location)
                                    <span class="meta-list__item">
                                        @include('partials.icons', ['icon' => 'map-pin'])
                                        {{ $featuredEvent->location }}
                                    </span>
                                @endif
                            </div>

                            <div>
                                <a href="{{ \App\Support\Links::event($featuredEvent) }}" class="cta">
                                    <span class="cta__label">View Details</span>
                                    @include('partials.icons', ['icon' => 'arrow-right'])
                                </a>
                            </div>
                        </div>

                        @if ($countdownTarget && $countdownTarget->isFuture())
                            <div class="countdown-panel">
                                <h3 class="countdown-panel__title">Event Starts In</h3>
                                @include('partials.countdown', ['target' => $countdownTarget, 'size' => 'md'])
                            </div>
                        @endif
                    </div>
                </article>
            </div>
        </section>
    @endif

    {{-- ---------------------------------------------------- past events --}}
    @if ($pastEvents->isNotEmpty())
        <section id="gallery" class="showcase">
            <div class="showcase__glow" aria-hidden="true"></div>

            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">From Our Past Events</h2>
                    <a class="cta cta--ghost" href="{{ \App\Support\Links::pastEvents() }}">View all past events</a>
                </div>

                <div class="showcase__list">
                    @foreach ($pastEvents as $past)
                        <article class="showcase__row" data-reveal>
                            <a class="showcase__media gallery-album__link" href="{{ \App\Support\Links::event($past) }}">
                                <div class="gallery-slideshow gallery-slideshow--showcase" data-gallery-slideshow>
                                    @foreach ($past->photos->take(8) as $photo)
                                        <figure class="gallery-slideshow__slide{{ $loop->first ? ' is-active' : '' }}"><img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?: $past->title }}" loading="lazy"></figure>
                                    @endforeach
                                </div>
                            </a>

                            <div class="showcase__text">
                                <h3 class="showcase__title">{{ $past->title }}</h3>
                                <p class="showcase__desc">
                                    {{ $past->event_date->format('F Y') }}
                                    @if ($past->description)
                                        &middot; {{ \Illuminate\Support\Str::limit($past->description, 180) }}
                                    @endif
                                </p>
                                <div class="showcase__rule" aria-hidden="true"></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- --------------------------------------------------------- contact --}}
    @include('partials.contact')

@endsection
