@extends('layouts.app')

@section('title', $event->title.' | Mora Lenz')
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $event->description), 160) ?: 'An event hosted by Mora Lenz.')
@if ($event->image_url)
    @section('og_image', $event->image_url)
@endif

@section('content')

    @php $countdownTarget = $event->countdownTarget(); @endphp

    <section class="event-hero">
        <div class="event-hero__bg">
            @if ($event->image_url)
                <img src="{{ $event->image_url }}" alt="" fetchpriority="high">
            @endif
        </div>

        <div class="event-hero__inner">
            <a href="{{ \App\Support\Links::events() }}" class="back-link">
                @include('partials.icons', ['icon' => 'arrow-left'])
                Back to Events
            </a>

            <h1 class="event-hero__title">{{ $event->title }}</h1>

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
        </div>
    </section>

    <section class="section" style="padding-top: 48px;">
        <div class="container">
            @if ($event->description)
                <div class="prose">
                    {{-- Preserve the paragraph breaks the admin typed. --}}
                    @foreach (preg_split('/\R{2,}/', trim($event->description)) as $paragraph)
                        <p>{!! nl2br(e($paragraph)) !!}</p>
                    @endforeach
                </div>
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
