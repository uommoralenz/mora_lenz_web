@extends('layouts.app')

@section('title', $page->title.' | Mora Lenz')
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $page->description), 160) ?: 'About Mora Lenz, the Mass Media Club of the University of Moratuwa.')
@if ($page->image_url)
    @section('og_image', $page->image_url)
@endif

@php
    // The same layout options and page-wide code an event page has; see
    // event-show.blade.php and App\Support\EventBlocks.
    $options = $page->pageOptions();
    $customCode = $options['custom_code'] ?? '';
    $showHeroImage = $options['hero'] !== 'plain' && $page->image_url;
@endphp

@if ($customCode !== '')
    @push('head_end')
        {!! $customCode !!}
    @endpush
@endif

@section('content')

    <section class="event-hero event-hero--{{ $options['hero'] }}">
        <div class="event-hero__bg">
            @if ($showHeroImage)
                <img src="{{ $page->image_url }}" alt="" fetchpriority="high">
            @endif
        </div>

        <div class="event-hero__inner">
            <h1 class="event-hero__title">{{ $page->title }}</h1>
        </div>
    </section>

    <section class="section event-body event-body--{{ $options['width'] }}" style="padding-top: 48px;">
        <div class="container">
            @if (filled($page->description))
                <div class="prose event-blocks__text is-lead">{!! \App\Support\EventBlocks::rich($page->description) !!}</div>
            @endif

            @if (! empty($page->content))
                @include('partials.event-blocks', ['blocks' => $page->content])
            @endif
        </div>
    </section>

    @if ($terms->isNotEmpty())
        <section class="section about-teams" id="teams">
            <div class="container">
                <div class="section__head">
                    <h2 class="section__title">Our Teams Through the Years</h2>
                    <p class="section__lede">Every year a new committee carries Mora Lenz forward. Meet each one.</p>
                </div>

                @include('partials.team-years', ['terms' => $terms])
            </div>
        </section>
    @endif

@endsection
