@extends('layouts.app')

@section('title', 'Page not found | Mora Lenz')

@section('content')
    <div class="container">
        <div class="page-head" style="padding-bottom: 80px;">
            <h1 class="page-head__title">404</h1>
            <p class="page-head__lede">
                We couldn&rsquo;t find that page. It may have moved, or the link may be out of date.
            </p>
            <p style="margin-top: 32px;">
                <a href="{{ route('home') }}" class="cta">
                    <span class="cta__label">Back to Home</span>
                    @include('partials.icons', ['icon' => 'arrow-right'])
                </a>
            </p>
        </div>
    </div>
@endsection
