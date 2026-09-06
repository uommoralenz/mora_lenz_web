@extends('layouts.app')

@section('title', 'Mora Lenz')
@section('description', 'Mora Lenz Official Website')

@section('content')
    <main class="min-h-screen bg-gray-50 transition-colors duration-300 dark:bg-black">
        @include('sections.hero')
        @include('sections.about')
        @include('sections.gallery')
        @include('sections.events')
        @include('sections.team')
        @include('sections.panels-pillars')
        @include('sections.ads')
        @include('sections.contact')
        @include('sections.footer')
    </main>
@endsection
