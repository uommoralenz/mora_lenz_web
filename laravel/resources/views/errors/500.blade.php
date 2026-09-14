@extends('layouts.app')

@section('title', 'Something went wrong | Mora Lenz')

@section('content')
    <div class="container">
        <div class="page-head" style="padding-bottom: 80px;">
            <h1 class="page-head__title">Something went wrong</h1>
            <p class="page-head__lede">
                We hit an unexpected error. Please try again in a moment.
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
