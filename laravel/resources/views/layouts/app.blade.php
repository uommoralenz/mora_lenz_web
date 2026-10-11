<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mora Lenz | Visual Storytelling')</title>
    <meta name="description" content="@yield('description', 'Mora Lenz is the Mass Media Club of the University of Moratuwa. Photography, videography and visual storytelling.')">
    <meta name="theme-color" content="#030508">
    <meta name="application-name" content="Mora Lenz">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Mora Lenz">

    <meta property="og:site_name" content="Mora Lenz">
    <meta property="og:title" content="@yield('title', 'Mora Lenz | Visual Storytelling')">
    <meta property="og:description" content="@yield('description', 'Mora Lenz is the Mass Media Club of the University of Moratuwa. Photography, videography and visual storytelling.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    @stack('head')
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ config('app.asset_version', '1') }}-{{ @filemtime(public_path('css/site.css')) ?: '' }}">

    {{-- After the stylesheet, so a page's own custom CSS wins over the site's. --}}
    @stack('head_end')
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    @include('partials.navbar')

    <main id="main" class="site-main">
        @yield('content')
    </main>

    @include('partials.footer')

    <aside class="install-banner" data-install-banner hidden
           role="dialog" aria-labelledby="install-app-title" aria-describedby="install-app-description">
        <img class="install-banner__icon" src="{{ asset('img/pwa-icon-192.png') }}" alt="" width="56" height="56">
        <div class="install-banner__copy">
            <strong id="install-app-title">Install Mora Lenz</strong>
            <span id="install-app-description" data-install-description>
                Add the site to your device for quick, app-like access.
            </span>
        </div>
        <button type="button" class="install-banner__action" data-install-confirm>Install</button>
        <button type="button" class="install-banner__dismiss" data-install-dismiss aria-label="Dismiss install suggestion">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12"></path>
            </svg>
        </button>
    </aside>

    <script src="{{ asset('js/site.js') }}?v={{ config('app.asset_version', '1') }}-{{ @filemtime(public_path('js/site.js')) ?: '' }}" defer></script>
    @stack('scripts')
</body>
</html>
