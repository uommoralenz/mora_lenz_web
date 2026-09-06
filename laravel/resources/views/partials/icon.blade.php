@php
    $class = $class ?? 'h-6 w-6';
@endphp

@switch($name)
    @case('camera')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M14.5 4l1.5 2h3a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h3l1.5-2h5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="13" r="4" stroke="currentColor" stroke-width="2"/>
        </svg>
        @break

    @case('film')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M7 4v16M17 4v16M3 9h4M17 9h4M3 15h4M17 15h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        @break

    @case('video')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="6" width="13" height="12" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M16 10l5-3v10l-5-3v-4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('users')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('award')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="8" r="6" stroke="currentColor" stroke-width="2"/>
            <path d="M8.5 13L7 22l5-3 5 3-1.5-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('dollar-sign')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7H14a3.5 3.5 0 010 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('user-check')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM16 11l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('message-circle')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5A8.48 8.48 0 0121 11v.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('file-text')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('pen-tool')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 19l7-7 3 3-7 7-3-3zM18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5zM2 2l7.6 7.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="11" cy="11" r="2" stroke="currentColor" stroke-width="2"/>
        </svg>
        @break

    @case('mic')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3zM19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('calendar')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        @break

    @case('trophy')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4zM7 6H4a2 2 0 002 5h1M17 6h3a2 2 0 01-2 5h-1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('book-open')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2V3zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7V3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('mail')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('map-pin')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
        </svg>
        @break

    @case('phone')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.8 19.8 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.35 1.9.65 2.8a2 2 0 01-.45 2.11L8.04 9.91a16 16 0 006.05 6.05l1.27-1.27a2 2 0 012.11-.45c.9.3 1.84.52 2.8.65A2 2 0 0122 16.92z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('instagram')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor" stroke-width="2"/>
            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/>
            <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
        </svg>
        @break

    @case('facebook')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3V2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('linkedin')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="4" cy="4" r="2" stroke="currentColor" stroke-width="2"/>
        </svg>
        @break

    @case('youtube')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M22.5 6.5a3 3 0 00-2.1-2.1C18.5 4 12 4 12 4s-6.5 0-8.4.4a3 3 0 00-2.1 2.1A31.8 31.8 0 001 12a31.8 31.8 0 00.5 5.5 3 3 0 002.1 2.1c1.9.4 8.4.4 8.4.4s6.5 0 8.4-.4a3 3 0 002.1-2.1A31.8 31.8 0 0023 12a31.8 31.8 0 00-.5-5.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 15l5-3-5-3v6z" fill="currentColor"/>
        </svg>
        @break

    @case('lock')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        @break

    @case('x')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        @break

    @case('arrow-right')
        <svg class="{{ $class }}" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8h10M13 8L9 4M13 8l-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('chevron-down')
        <svg class="{{ $class }}" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('chevron-left')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('chevron-right')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('sun')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="4" fill="currentColor"/>
            <path d="M12 2v2M12 20v2M4 12H2M6.31 6.31L4.9 4.9M17.69 6.31l1.41-1.41M6.31 17.69l-1.41 1.41M17.69 17.69l1.41 1.41M22 12h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        @break

    @case('moon')
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" fill="currentColor"/>
        </svg>
        @break

    @default
        <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
        </svg>
@endswitch
