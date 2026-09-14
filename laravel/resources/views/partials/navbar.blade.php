@php
    // "Gallery", "Stories" and "Contact" are sections of the homepage, so from
    // any other page they need to link back to "/" before the anchor.
    $onHome = request()->routeIs('home');
    $anchor = fn (string $hash) => $onHome ? $hash : url('/').$hash;
@endphp

<header class="navbar" data-navbar>
    <div class="navbar__inner">
        <a href="{{ route('home') }}" class="navbar__logo" aria-label="Mora Lenz — home">
            @if (file_exists(public_path('img/logo.png')))
                <img src="{{ asset('img/logo.png') }}" alt="Mora Lenz">
            @else
                @include('partials.logo-fallback')
            @endif
        </a>

        <nav class="navbar__nav" aria-label="Main">
            <ul class="navbar__list">
                <li class="navbar__item">
                    <a class="navbar__link" href="{{ route('home') }}"
                       @if ($onHome) aria-current="page" @endif>Home</a>
                </li>

                <li class="navbar__item navbar__dropdown" data-dropdown data-open="false">
                    <button type="button" class="navbar__dropdown-trigger"
                            data-dropdown-trigger aria-expanded="false" aria-haspopup="true">
                        <span>Services</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <ul class="navbar__dropdown-menu">
                        <li><a href="{{ \App\Support\Links::service('photography') }}">Photography Services</a></li>
                        <li><a href="{{ \App\Support\Links::service('videography') }}">Videography Services</a></li>
                    </ul>
                </li>

                <li class="navbar__item">
                    <a class="navbar__link" href="{{ \App\Support\Links::events() }}"
                       @if (request()->routeIs('events.*')) aria-current="page" @endif>Events</a>
                </li>
                <li class="navbar__item">
                    <a class="navbar__link" href="{{ \App\Support\Links::gallery() }}" @if(request()->routeIs('gallery')) aria-current="page" @endif>Gallery</a>
                </li>
                <li class="navbar__item">
                    <a class="navbar__link" href="{{ \App\Support\Links::team() }}"
                       @if (request()->routeIs('team')) aria-current="page" @endif>Our Team</a>
                </li>
                <li class="navbar__item">
                    <a class="navbar__link" href="{{ $anchor('#contact') }}">Contact</a>
                </li>
            </ul>
        </nav>

        <div class="navbar__actions">
            <button type="button" class="navbar__toggle" data-navbar-toggle
                    aria-expanded="false" aria-controls="mobile-menu" aria-label="Toggle menu">
                <svg class="icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" aria-hidden="true">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
                <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <div class="navbar__mobile" id="mobile-menu" data-navbar-mobile data-open="false">
        <a href="{{ route('home') }}">Home</a>
        <span class="navbar__mobile-label">Services</span>
        <div class="navbar__mobile-sub">
            <a href="{{ \App\Support\Links::service('photography') }}">Photography Services</a>
            <a href="{{ \App\Support\Links::service('videography') }}">Videography Services</a>
        </div>
        <a href="{{ \App\Support\Links::events() }}">Events</a>
        <a href="{{ \App\Support\Links::gallery() }}">Gallery</a>
        <a href="{{ \App\Support\Links::team() }}">Our Team</a>
        <a href="{{ $anchor('#contact') }}">Contact</a>
    </div>
</header>
