@extends('layouts.app')

@section('title', 'Mora Lenz | Visual Storytelling')
@section('description', 'Mora Lenz — the official Photography & Videography Club of the University of Moratuwa. Event coverage, portraits, films and a community of creators.')

@section('content')

    {{-- ------------------------------------------------------------ hero --}}
    <section class="hero" id="hero">
        <div class="hero__bg">
            @if (file_exists(public_path('img/hero-campus-night.png')))
                <img src="{{ asset('img/hero-campus-night.png') }}" alt="" fetchpriority="high">
            @elseif (file_exists(public_path('img/landing-bg.jpeg')))
                <img src="{{ asset('img/landing-bg.jpeg') }}" alt="" fetchpriority="high">
            @endif
        </div>

        <div class="hero__wordmark" aria-hidden="true">
            <h2>Mora<br>Lenz</h2>
        </div>

        <div class="hero__inner">
            <div class="hero__content">
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
                    Mora Lenz: The Official Photography &amp; Videography Club of the University of
                    Moratuwa. Capturing the essence of university life and beyond.
                </p>

                <div class="hero__actions">
                    <a href="{{ \App\Support\Links::gallery() }}" class="cta">
                        <span class="cta__label">Explore Gallery</span>
                        @include('partials.icons', ['icon' => 'arrow-down'])
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
                            <span class="badge">
                                <span class="badge__dot" aria-hidden="true"></span>
                                Featured Event
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

    {{-- ----------------------------------------------- featured galleries --}}
    @if ($galleries->isNotEmpty())
        <section id="gallery" class="showcase">
            <div class="showcase__glow" aria-hidden="true"></div>

            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">Featured Galleries</h2>
                    <a class="cta cta--ghost" href="{{ \App\Support\Links::gallery() }}">View all galleries</a>
                </div>

                <div class="showcase__list">
                    @foreach ($galleries as $gallery)
                        <article class="showcase__row" data-reveal>
                            <a class="showcase__media gallery-album__link" href="{{ $gallery->facebook_album_url ?: \App\Support\Links::gallery() }}" @if($gallery->facebook_album_url) target="_blank" rel="noopener noreferrer" @endif>
                                <div class="gallery-slideshow gallery-slideshow--showcase" data-gallery-slideshow>
                                    @forelse ($gallery->images as $image)
                                        <figure class="gallery-slideshow__slide{{ $loop->first ? ' is-active' : '' }}"><img src="{{ $image->image_url }}" alt="{{ $gallery->title }}" loading="lazy"></figure>
                                    @empty
                                        @if ($gallery->image_url)<img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" loading="lazy">@endif
                                    @endforelse
                                </div>
                            </a>

                            <div class="showcase__text">
                                <h3 class="showcase__title">{{ $gallery->title }}</h3>
                                @if ($gallery->description)
                                    <p class="showcase__desc">{{ $gallery->description }}</p>
                                @endif
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
