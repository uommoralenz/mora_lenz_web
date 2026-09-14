<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mora Lenz | Visual Storytelling')</title>
    <meta name="description" content="@yield('description', 'The Photography & Visual Media Club of the University of Moratuwa.')">

    <meta property="og:site_name" content="Mora Lenz">
    <meta property="og:title" content="@yield('title', 'Mora Lenz | Visual Storytelling')">
    <meta property="og:description" content="@yield('description', 'The Photography & Visual Media Club of the University of Moratuwa.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ config('app.asset_version', '1') }}">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    @include('partials.navbar')

    <main id="main" class="site-main">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('js/site.js') }}?v={{ config('app.asset_version', '1') }}" defer></script>
    @stack('scripts')
</body>
</html>
