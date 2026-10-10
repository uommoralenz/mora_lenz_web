<?php

/*
|--------------------------------------------------------------------------
| Mora Lenz site configuration
|--------------------------------------------------------------------------
|
| Everything here is editable from .env so the site can be re-pointed without
| touching code. The bootstrap super admin below is the ONE hard-coded account:
| it is created by the database seeder and can never be deleted or demoted.
| Every other admin is created from inside the admin panel by that super admin.
|
*/

return [

    // The first (bootstrap) super admin. Change these in .env BEFORE seeding,
    // then sign in and change the password from the admin panel.
    'super_admin' => [
        'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
        'username' => env('SUPER_ADMIN_USERNAME', 'superadmin'),
        'email' => env('SUPER_ADMIN_EMAIL', 'uommediaunit@gmail.com'),
        'password' => env('SUPER_ADMIN_PASSWORD', ''),
    ],

    // How long an admin panel login stays valid before it must sign in again.
    'token_lifetime_hours' => (int) env('ADMIN_TOKEN_LIFETIME_HOURS', 12),

    /*
     * One-time installer key, used by /setup/<key> to create the database
     * tables without shell access. Leave BLANK to disable the route entirely.
     * Blank it out again the moment the install finishes.
     */
    'setup_key' => env('SETUP_KEY', ''),

    /*
     * Compatibility mode for servers whose nginx cannot rewrite unknown paths
     * to index.php. When true, links are generated in forms that the routing
     * shims in public_html/ can serve (see app/Support/Links.php).
     *
     * Set this to false — and delete the shim folders — once nginx has:
     *     location / { try_files $uri $uri/ /index.php?$query_string; }
     */
    'compat_urls' => filter_var(env('COMPAT_URLS', false), FILTER_VALIDATE_BOOL),

    // Where uploaded images are written, relative to the public/ directory.
    'upload_dir' => 'uploads',

    'upload' => [
        'max_kb' => (int) env('UPLOAD_MAX_KB', 8192),
        'mimes' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        // Sub-folders, one per kind of image. Keys are the "type" sent by the
        // admin panel; anything not listed here is rejected.
        'types' => [
            'events' => 'events',
            'gallery' => 'gallery',
            'services' => 'services',
            'service-images' => 'service-images',
            'team' => 'team',
        ],
    ],

    // Contact form destination. Leave CONTACT_MAIL_ENABLED=false to only store
    // messages in the database (readable from the admin panel) and skip email.
    'contact' => [
        'mail_enabled' => filter_var(env('CONTACT_MAIL_ENABLED', false), FILTER_VALIDATE_BOOL),
        'to' => env('CONTACT_MAIL_TO', 'uommediaunit@gmail.com'),
    ],

    // Shown in the footer / contact section of the public site.
    'social' => [
        'facebook' => env('SOCIAL_FACEBOOK', 'https://www.facebook.com/moralenzuom'),
        'instagram' => env('SOCIAL_INSTAGRAM', 'https://www.instagram.com/moralenzuom'),
        'linkedin' => env('SOCIAL_LINKEDIN', 'https://lk.linkedin.com/company/moralenzuom'),
    ],

    'contact_email' => env('CONTACT_EMAIL', 'uommediaunit@gmail.com'),

    /*
     * Where the club is. "map_url" is the link people open in the Maps app;
     * "map_embed_url" is the iframe behind the contact section's Find Us block.
     *
     * The embed is built from a plain ?q= place query on purpose. Google's other
     * embed form — the long "?pb=!1m18!1m12…" string copied out of the Share
     * dialog — pins an internal feature ID that goes stale, and once it does the
     * iframe quietly falls back to an unlabelled patch of map with no marker,
     * which is exactly what it had started doing here. A ?q= query is resolved
     * fresh on every load, needs no API key, and always drops a pin labelled
     * with the place.
     */
    'map_url' => env('MAP_URL', 'https://maps.app.goo.gl/JAh566G7oQiKNXUp9'),

    'map_place' => env('MAP_PLACE', 'University of Moratuwa'),

    'map_address' => env('MAP_ADDRESS', 'Bandaranayake Mawatha, Katubedda, Moratuwa 10400, Sri Lanka'),

    'map_embed_url' => env(
        'MAP_EMBED_URL',
        'https://www.google.com/maps?q='
            .rawurlencode('University of Moratuwa, Katubedda, Moratuwa, Sri Lanka')
            .'&z=16&hl=en&output=embed'
    ),

];
