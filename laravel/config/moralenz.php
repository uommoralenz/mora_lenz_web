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

    'map_url' => env('MAP_URL', 'https://maps.app.goo.gl/JAh566G7oQiKNXUp9'),

];
