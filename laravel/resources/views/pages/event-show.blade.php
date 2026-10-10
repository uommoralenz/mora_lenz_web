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
            <a href="{{ \App\Support\Links::events() }}" class="back-link">
                @include('partials.icons', ['icon' => 'arrow-left'])
                Back to Events
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
            @if ($event->description)
                {{-- The intro takes the same inline marks as the content blocks. --}}
                <div class="prose">{!! \App\Support\EventBlocks::rich($event->description) !!}</div>
            @endif

            @if (! empty($event->content))
                @include('partials.event-blocks', ['blocks' => $event->content])
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
