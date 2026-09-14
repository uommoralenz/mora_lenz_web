<?php

// Run locally: php scripts/check-admin-login.php. Never upload to public_html.
require dirname(__DIR__).'/vendor/autoload.php';

$env = Dotenv\Dotenv::createArrayBacked(dirname(__DIR__))->load();
$base = 'https://moralenz.uom.lk/api/admin/';
$token = null;

function callApi(string $base, string $path, string $method, array $headers = [], ?array $body = null): array
{
    $handle = curl_init($base.'?'.http_build_query(['__path' => $path]));
    curl_setopt_array($handle, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array_merge(['Accept: application/json', 'Content-Type: application/json'], $headers),
    ]);
    if ($body !== null) {
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $raw = curl_exec($handle);
    $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
    curl_close($handle);
    return [$status, json_decode($raw ?: '', true)];
}

try {
    [$status, $body] = callApi($base, '/auth/login', 'POST', [], [
        'username' => $env['SUPER_ADMIN_USERNAME'] ?? '',
        'password' => $env['SUPER_ADMIN_PASSWORD'] ?? '',
    ]);
    echo "login HTTP {$status}\n";
    if ($status !== 200 || empty($body['token'])) {
        exit(1);
    }
    $token = $body['token'];
    foreach ([
        'bearer' => ['Authorization: Bearer '.$token],
        'fallback' => ['X-Admin-Token: '.$token],
        'both' => ['Authorization: Bearer '.$token, 'X-Admin-Token: '.$token],
        'both-repeat' => ['Authorization: Bearer '.$token, 'X-Admin-Token: '.$token],
    ] as $label => $headers) {
        [$status, $me] = callApi($base, '/auth/me', 'GET', $headers);
        echo "{$label} /auth/me HTTP {$status}; admin_present=".(isset($me['admin']['id']) ? 'yes' : 'no')."\n";
    }
} finally {
    if ($token) {
        [$status] = callApi($base, '/auth/logout', 'POST', ['Authorization: Bearer '.$token, 'X-Admin-Token: '.$token]);
        echo "test-session logout HTTP {$status}\n";
    }
}
