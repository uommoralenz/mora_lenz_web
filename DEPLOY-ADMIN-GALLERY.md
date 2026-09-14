# Event deletion, gallery page, and security update

These changes are local until uploaded/deployed. No production data was deleted during tests.

## 1. Add the gallery setting using phpMyAdmin

Open the site's database, select SQL, and first check:

```sql
SHOW COLUMNS FROM featured_galleries LIKE 'show_on_homepage';
```

Only if the column is absent, run:

```sql
ALTER TABLE featured_galleries
ADD COLUMN show_on_homepage TINYINT(1) NOT NULL DEFAULT 1;
```

Existing published entries remain eligible; the homepage displays the first three by gallery order.
The public gallery page shows all published entries, 12 per page. Hidden entries appear nowhere publicly.
In the admin Gallery form, use “Published on the gallery page” and “Show on homepage”.
The new Laravel migration also handles this column and skips it if already added manually.

## 2. Upload Laravel files using CWP File Manager

Back up the corresponding server files first. The actual application directory is
`/home/moralenz/laravel/`; the served web directory is `/home/moralenz/public_html/`.

Copy these local files under `laravel/` to the same relative paths under `/home/moralenz/laravel/`:

- `routes/api.php` — fixes numeric event ID binding (view, edit and delete).
- `routes/web.php`
- `bootstrap/app.php` — authenticates admin requests before model binding.
- `app/Http/Middleware/AuthenticateAdmin.php`
- `app/Http/Controllers/Admin/AuthController.php`
- `app/Http/Controllers/Admin/EventController.php`
- `app/Http/Controllers/Admin/GalleryController.php`
- `app/Http/Controllers/Public_/GalleryController.php` (new)
- `app/Http/Controllers/Public_/HomeController.php`
- `app/Models/AdminToken.php`
- `app/Models/FeaturedGallery.php`
- `app/Support/AdminAuth.php` — keeps the expiry fix; removes temporary token logging.
- `app/Support/ImageStore.php`
- `app/Support/Links.php`
- `config/moralenz.php`
- `database/seeders/SuperAdminSeeder.php`
- `database/migrations/2026_09_14_000002_add_gallery_homepage_flag.php`
- `resources/views/pages/gallery.blade.php` (new)
- `resources/views/pages/home.blade.php`
- `resources/views/partials/navbar.blade.php`
- `resources/views/partials/footer.blade.php`

Copy these public assets to the separate served directory:

- `laravel/public/gallery/index.php` → `/home/moralenz/public_html/gallery/index.php` (create the gallery folder)
- `laravel/public/css/site.css` → `/home/moralenz/public_html/css/site.css`

Do not overwrite the server's `.env` or `public_html/index.php` with local versions.
Retain `PUBLIC_PATH=/home/moralenz/public_html` and `COMPAT_URLS=true` on this host.
Bump `ASSET_VERSION` in the server `.env` so browsers request the updated stylesheet.
If cached routes/config are present, move `bootstrap/cache/config.php` and files named
`routes-*.php` into a backup folder outside `bootstrap/cache` within the private app directory.
Do not remove `packages.php` or `services.php`. No terminal is required.

## 3. Deploy admin through Vercel

Commit and push the updated admin source, package.json and package-lock.json.
Let Vercel build it with root directory `admin`. Keep `LARAVEL_API_COMPAT=1`.
The POST delete override remains enabled for this nginx host. Delete failures now appear
beside the event button instead of crashing the page.

## Security changes and limits

- Removed token fingerprints and authentication diagnostics from application logs.
  Previous diagnostics contained short SHA-256 fingerprints, not usable bearer tokens.
  Removing logging does not erase existing Vercel or CWP log history.
- Token hashes are hidden from model serialization; tokens are still stored hashed in the database.
- Admin API responses and requests are not cached; authentication precedes model binding.
- Uploaded file extensions come from detected image MIME type, never the original filename.
- External image URLs cannot delete local uploads. Event image URLs must be HTTP(S).
- Removed the built-in bootstrap password; new setup requires an explicit password of at least 10 characters.
- Server errors returned to the admin UI do not forward backend exception details.
- Patched PostCSS to 8.5.28 via an override, preserving Next.js 15 compatibility.

In production use `APP_DEBUG=false`, `SETUP_KEY=` (empty), and `ALLOW_INSECURE_COOKIE=0`.
The nginx host should deny script execution in `/uploads/`: Apache `.htaccess` rules do not
configure nginx. Ask the hosting administrator to enforce this; this local patch cannot change nginx.
Existing uploads were not scanned or deleted. An infrastructure/credentials audit is still needed
to claim complete security; dependency audits only cover known advisories.

## Verified locally

- Production admin build and TypeScript checks pass.
- npm and Composer audits report no known vulnerabilities at time of check.
- Isolated SQLite regression checks: numeric event lookup/delete, unauthenticated delete denial,
  session expiry, gallery selection, uploaded-extension detection and external URL deletion refusal.
- Laravel Blade templates compile. Production CWP changes still require upload and verification.
