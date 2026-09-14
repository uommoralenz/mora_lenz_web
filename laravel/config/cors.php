<?php

/*
|--------------------------------------------------------------------------
| Cross-Origin Resource Sharing (CORS)
|--------------------------------------------------------------------------
|
| The admin panel is hosted on Vercel, which is a different origin from this
| Laravel app. Only the admin API is exposed cross-origin, and only to the
| exact origins listed in ADMIN_PANEL_ORIGINS in your .env file.
|
| Example .env value (comma separated, no trailing slash):
|   ADMIN_PANEL_ORIGINS=https://moralenz-admin.vercel.app,http://localhost:3000
|
*/

$origins = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('ADMIN_PANEL_ORIGINS', ''))
)));

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => $origins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Accept', 'Authorization', 'Content-Type', 'X-Requested-With', 'X-Admin-Token'],

    'exposed_headers' => [],

    'max_age' => 86400,

    // Bearer tokens are used instead of cookies, so credentials are not needed.
    'supports_credentials' => false,

];
