# Mora Lenz Admin

This is the separate Node.js admin website for Vercel. It edits the same MySQL tables used by the Laravel public website.

## What It Manages

- Events: create, edit, delete upcoming and past events
- Members: create, edit, delete committee members
- Panels & Pillars: create, edit, delete public cards and member counts
- Gallery: create, edit, delete gallery images
- Contact messages: view, mark as read, delete
- Admins: super admins can create, disable, and delete admin accounts

Admins can paste existing image URLs or upload files directly to Cloudinary. Signed uploads fill the URL field immediately; saving the form stores that URL in MySQL. Event uploads append to the URL list; member and gallery uploads replace the current URL. Uploads are limited to 6 MB per file. Images uploaded before canceling a form remain in Cloudinary.

Cloudinary requires an API key with permission to create images. Keep `CLOUDINARY_API_SECRET` server-only (never use a `NEXT_PUBLIC_` prefix). The authenticated signing endpoint follows [Cloudinary's signed upload API](https://cloudinary.com/documentation/authentication_signatures); image bytes go directly from the browser to Cloudinary, avoiding PHP and Vercel request-size limits. URL-only saves do not call Cloudinary.

## Local Setup

```bash
npm install
cp .env.example .env.local
npm run dev
```

Set `.env.local`:

```env
DATABASE_URL="mysql://moralenz_web1:your_password@127.0.0.1:3306/moralenz_web"
DATABASE_SSL=false
SESSION_SECRET="generate-a-long-random-secret-at-least-32-characters"
CLOUDINARY_CLOUD_NAME="your-cloud-name"
CLOUDINARY_API_KEY="your-api-key"
CLOUDINARY_API_SECRET="your-api-secret"
```

## Vercel Deployment

1. Create a new Vercel project.
2. Set the project root directory to `admin`.
3. Add these environment variables in Vercel:

```env
DATABASE_URL
DATABASE_SSL
SESSION_SECRET
CLOUDINARY_CLOUD_NAME
CLOUDINARY_API_KEY
CLOUDINARY_API_SECRET
```

4. Deploy.

Before deploying this version, run `php artisan migrate --force` on the Laravel server. The admin now uses `admin_sessions` for revocable sessions and `admin_rate_limits` for shared login/upload limits. Existing browser sessions will need to sign in again. The database user used by the admin needs access to both tables.

Use a random `SESSION_SECRET` of at least 32 characters; placeholder secrets are rejected. Logout revokes the current session in MySQL. Disabling an account revokes its sessions, and changing its password invalidates existing sessions. Login is limited to five attempts per email per 15 minutes, with a global limit of 100 attempts per minute; upload signatures are limited to 30 per admin per minute.

Before production, rotate the Cloudinary secrets previously shared in chat and use a dedicated key with image-create permission instead of the Root key. Update the deployment environment variables after rotation. Use HTTPS, set Laravel `APP_ENV=production` and `APP_DEBUG=false`, and enable verified TLS for remote MySQL connections (`DATABASE_SSL=true`). The image format restriction is signed and enforced by Cloudinary; the 6 MB browser size check is not a verified provider-side quota.

Admin emails and passwords are not stored in Vercel environment variables. They are stored in MySQL in the `admin_users` table. Passwords are bcrypt hashes.

Create the first super admin directly in MySQL using the Laravel server terminal before signing in:

```bash
cd laravel
php artisan migrate
php artisan admin:create-super
```

The command prompts for the name, email, and password and stores a bcrypt password hash in `admin_users`. Laravel and this admin app must use the same database. Then open `/login` and sign in with that account. There is no browser-based account setup; `/setup` redirects to `/login`. If no admin account exists, the login page displays a message to contact the site administrator.

Important: your MySQL server must allow remote connections from Vercel. If your hosting database only accepts `localhost`, Vercel cannot connect directly. In that case use a remote MySQL provider, enable remote database access in hosting, or build a small API on the PHP hosting server for the admin to call.



