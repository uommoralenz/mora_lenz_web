@extends('layouts.app')

@section('title', $event->title.' | Mora Lenz')
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $event->description), 160) ?: 'An event hosted by Mora Lenz.')
@if ($event->image_url)
    @section('og_image', $event->image_url)
@endif

@if (! empty($preview))
    @push('head')
        <meta name="robots" content="noindex, nofollow">
    @endpush
@endif

@php
    // The page-wide markup the admin wrote in the panel: <style> rules that
    // theme the blocks below, a web font, a <script> an html block needs. It
    // goes last in <head> on purpose, so it can override the site stylesheet.
    // Printed verbatim — see the note in App\Support\EventBlocks.
    $customCode = $event->pageOptions()['custom_code'] ?? '';
@endphp

@if ($customCode !== '')
    @push('head_end')
        {!! $customCode !!}
    @endpush
@endif

@section('content')

    @if (! empty($preview))
        <div class="preview-banner" role="status">
            Preview &mdash; this is how the page will look. It is not published until you save.
        </div>
    @endif

    @php
        $countdownTarget = $event->countdownTarget();

        // The layout choices the admin made in the panel, always fully filled in.
        $page = $event->pageOptions();

        // "photo" is the full-bleed cover, "compact" a short banner, "plain"
        // drops the image and lets the title carry the top of the page.
        $showHeroImage = $page['hero'] !== 'plain' && $event->image_url;
    @endphp

    <section class="event-hero event-hero--{{ $page['hero'] }}">
        <div class="event-hero__bg">
            @if ($showHeroImage)
                <img src="{{ $event->image_url }}" alt="" fetchpriority="high">
            @endif
        </div>

        <div class="event-hero__inner">
            <a href="{{ $event->exists && $event->isPast() ? \App\Support\Links::pastEvents() : \App\Support\Links::events() }}" class="back-link">
                @include('partials.icons', ['icon' => 'arrow-left'])
                {{ $event->exists && $event->isPast() ? 'Back to Past Events' : 'Back to Events' }}
            </a>

            <h1 class="event-hero__title">{{ $event->title }}</h1>

            @if ($page['show_meta'])
                <div class="meta-list">
                    <span class="meta-pill">
                        @include('partials.icons', ['icon' => 'calendar'])
                        {{ $event->event_date->format('l, F j, Y') }}
                    </span>
                    <span class="meta-pill">
                        @include('partials.icons', ['icon' => 'clock'])
                        {{ $event->event_date->format('g:i A') }}
                    </span>
                    @if ($event->location)
                        <span class="meta-pill">
                            @include('partials.icons', ['icon' => 'map-pin'])
                            {{ $event->location }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <section class="section event-body event-body--{{ $page['width'] }}" style="padding-top: 48px;">
        <div class="container">
            @if (! empty($event->content))
                @include('partials.event-blocks', ['blocks' => $event->content])
            @endif

            @php
                $photos = $event->photos->map(fn ($p) => ['src' => $p->image_url, 'caption' => $p->caption])->values();
                $fbLinks = $event->facebookLinks();
            @endphp

            @if ($photos->isNotEmpty() || $fbLinks !== [])
                <div class="event-photos" id="photos">
                    <div class="event-photos__head">
                        <h2>Photos</h2>
                        @if ($photos->isNotEmpty())
                            <span>{{ $photos->count() }} {{ $photos->count() === 1 ? 'photo' : 'photos' }}</span>
                        @endif
                    </div>

                    @if ($photos->isNotEmpty())
                        <div class="event-photos__grid">
                            @foreach ($photos as $index => $photo)
                                <button type="button" class="event-photos__item"
                                        data-lightbox-open
                                        data-lightbox-index="{{ $index }}"
                                        data-title="{{ $event->title }}"
                                        data-photos="{{ json_encode($photos) }}"
                                        aria-label="Open photo {{ $index + 1 }} of {{ $photos->count() }}">
                                    <img src="{{ $photo['src'] }}" alt="{{ $photo['caption'] ?: $event->title }}" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @if ($fbLinks !== [])
                        <div class="event-blocks__buttons">
                            @foreach ($fbLinks as $fbUrl)
                                <a class="cta {{ $loop->first ? '' : 'cta--ghost' }} event-blocks__btn" href="{{ $fbUrl }}" target="_blank" rel="noopener noreferrer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                    <span class="cta__label">{{ count($fbLinks) > 1 ? 'Facebook album '.$loop->iteration : 'See the full album on Facebook' }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($photos->isNotEmpty())
                    @include('partials.lightbox')
                @endif
            @endif

            @if ($countdownTarget && $countdownTarget->isFuture())
                <div class="countdown-block">
                    <h2>Event Starts In</h2>
                    <div style="display:flex;justify-content:center;">
                        @include('partials.countdown', ['target' => $countdownTarget, 'size' => 'lg'])
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection
