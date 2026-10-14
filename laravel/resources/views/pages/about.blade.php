@extends('layouts.app')

{{--
    A fully custom page: nothing is drawn but the admin's own blocks, between
    the site's navbar and footer. The title and intro are only used for the
    browser tab, search results and link previews.
--}}

@section('title', $page->title.' | Mora Lenz')
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $page->description), 160) ?: 'About Mora Lenz, the Mass Media Club of the University of Moratuwa.')
@if ($page->image_url)
    @section('og_image', $page->image_url)
@endif

@php
    $options = $page->pageOptions();
    $customCode = $options['custom_code'] ?? '';
@endphp

@if ($customCode !== '')
    @push('head_end')
        {!! $customCode !!}
    @endpush
@endif

@section('content')

    <div class="custom-page custom-page--{{ $options['width'] }}">
        <h1 class="visually-hidden">{{ $page->title }}</h1>

        @if (! empty($page->content))
            @include('partials.event-blocks', ['blocks' => $page->content])
        @endif
    </div>

    @if ($options['show_teams'] && $terms->isNotEmpty())
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
