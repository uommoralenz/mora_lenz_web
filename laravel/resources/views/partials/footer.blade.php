@php
    $social = config('moralenz.social');
    $onHome = request()->routeIs('home');
    $anchor = fn (string $hash) => $onHome ? $hash : url('/').$hash;
@endphp

<footer class="footer">
    <div class="container">
        <div class="footer__grid">
            <div class="footer__brand">
                @if (file_exists(public_path('img/logo.png')))
                    <img src="{{ asset('img/logo.png') }}" alt="Mora Lenz">
                @else
                    <span class="navbar__logo-text">Mora Lenz</span>
                @endif

                <p>
                    Capturing moments, creating stories, and bringing visions to life through the
                    lens of creativity. Join our community of passionate media creators.
                </p>

                <div class="socials">
                    <a href="{{ $social['facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.45 2.89h-2.33v6.99A10 10 0 0 0 22 12Z"/>
                        </svg>
                    </a>
                    <a href="{{ $social['instagram'] }}" target="_blank" rel="noopener noreferrer"
                       class="is-instagram" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="{{ $social['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9h4v12H3V9Zm7 0h3.8v1.71h.05c.53-.95 1.83-1.96 3.77-1.96C21.4 8.75 22 11.1 22 14.14V21h-4v-6.07c0-1.45-.03-3.31-2.02-3.31-2.02 0-2.33 1.57-2.33 3.2V21h-3.98V9Z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="footer__col">
                <h2>Quick Links</h2>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ $anchor('#gallery') }}">Gallery</a></li>
                    <li><a href="{{ route('events.index') }}">Events</a></li>
                    <li><a href="{{ $anchor('#contact') }}">Contact</a></li>
                </ul>
            </div>

            <div class="footer__col">
                <h2>Club</h2>
                <ul>
                    <li><a href="{{ route('team') }}">Our Team</a></li>
                    <li><a href="{{ route('services.show', 'photography') }}">Photography</a></li>
                    <li><a href="{{ route('services.show', 'videography') }}">Videography</a></li>
                </ul>
            </div>

            <div class="footer__col">
                <h2>Reach Us</h2>
                <ul>
                    <li><a href="mailto:{{ config('moralenz.contact_email') }}">{{ config('moralenz.contact_email') }}</a></li>
                    <li><a href="{{ config('moralenz.map_url') }}" target="_blank" rel="noopener noreferrer">University of Moratuwa</a></li>
                </ul>
            </div>
        </div>

        <div class="footer__bottom">
            <p>&copy; {{ now()->year }} Mora Lenz Media Club. All rights reserved.</p>
            <p>Mora Lenz Mass Media Club</p>
            <p>University of Moratuwa, Katubedda, Sri Lanka</p>
        </div>
    </div>
</footer>
