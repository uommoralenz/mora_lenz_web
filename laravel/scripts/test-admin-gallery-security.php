<?php

// Local, isolated regression tests. Never connects to the production database.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config([
    'database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
    'cache.default' => 'array', 'session.driver' => 'array',
    'app.key' => 'base64:'.base64_encode(str_repeat('t', 32)),
    'app.url' => 'https://example.test',
]);
Illuminate\Support\Facades\DB::purge('sqlite');
foreach (glob(dirname(__DIR__).'/database/migrations/*.php') as $migration) {
    (require $migration)->up();
}

function verify(bool $ok, string $message): void {
    if (! $ok) throw new RuntimeException($message);
    echo "PASS: {$message}\n";
}

$admin = App\Models\Admin::create(['name' => 'Test', 'username' => 'test', 'password' => 'test-password', 'is_active' => true]);
$token = App\Support\AdminAuth::issueToken($admin)['token'];
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
Illuminate\Http\Request::enableHttpMethodParameterOverride();
function requestApi(string $method, string $path, array $data = [], ?string $token = null) {
    global $kernel;
    $request = Illuminate\Http\Request::create($path, $method, $data, [], [], ['HTTP_ACCEPT' => 'application/json']);
    if ($token) $request->headers->set('X-Admin-Token', $token);
    return $kernel->handle($request);
}

$event = App\Models\Event::create(['title' => 'Readable event', 'slug' => 'readable-event', 'event_date' => now(), 'is_active' => true]);
$response = requestApi('GET', '/api/admin/events/'.$event->id, [], $token);
verify($response->getStatusCode() === 200, 'admin event lookup uses numeric ID despite the public slug');
$response = requestApi('POST', '/api/admin/events/'.$event->id, ['_method' => 'DELETE']);
verify($response->getStatusCode() === 401 && $event->fresh() !== null, 'unauthenticated deletion is blocked');
verify(requestApi('GET', '/api/admin/events/999999')->getStatusCode() === 401, 'authentication runs before resource binding');
$response = requestApi('POST', '/api/admin/events/'.$event->id, ['_method' => 'DELETE'], $token);
verify($response->getStatusCode() === 200 && $event->fresh() === null, 'POST method override deletes the intended numeric event ID');
verify(str_contains($response->headers->get('Cache-Control'), 'no-store'), 'authenticated responses cannot be stored in caches');

for ($i = 0; $i < 5; $i++) {
    App\Models\FeaturedGallery::create(['title' => 'Featured '.$i, 'image_url' => '/test.png', 'is_active' => true, 'show_on_homepage' => true, 'sort_order' => $i]);
}
$unfeatured = App\Models\FeaturedGallery::create(['title' => 'Gallery only', 'image_url' => '/test.png', 'is_active' => true, 'show_on_homepage' => false]);
App\Models\FeaturedGallery::create(['title' => 'Hidden', 'image_url' => '/test.png', 'is_active' => false, 'show_on_homepage' => true]);
$home = (new App\Http\Controllers\Public_\HomeController)->index()->getData()['galleries'];
verify($home->count() === 3 && $home->every(fn ($g) => $g->show_on_homepage && $g->is_active), 'homepage contains at most three selected published entries');
$galleries = (new App\Http\Controllers\Public_\GalleryController)->index()->getData()['galleries'];
verify($galleries->total() === 6 && $galleries->contains('id', $unfeatured->id), 'gallery page includes unfeatured entries but excludes hidden entries');

$testRoot = sys_get_temp_dir().'/moralenz-upload-test-'.bin2hex(random_bytes(6));
mkdir($testRoot);
$app->usePublicPath($testRoot);
$input = $testRoot.'/input';
file_put_contents($input, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
try {
    $url = App\Support\ImageStore::put(new Illuminate\Http\UploadedFile($input, 'payload.php', null, null, true), 'gallery');
    verify(str_ends_with($url, '.png'), 'image named payload.php is saved with a detected PNG extension');
    $stored = $testRoot.parse_url($url, PHP_URL_PATH);
    App\Support\ImageStore::delete(str_replace('example.test', 'external.test', $url));
    verify(is_file($stored), 'external image URL cannot delete a local upload');
    App\Support\ImageStore::delete($url);
    verify(!is_file($stored), 'owned upload can be deleted');
} finally {
    // Only remove the isolated directory created by this test.
    Illuminate\Support\Facades\File::deleteDirectory($testRoot);
}
