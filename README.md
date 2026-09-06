### [Postman Team Link](https://app.getpostman.com/join-team?invite_code=a1eab34325104f22419a4a7f8d881964b787d191ea2ae4acd7e54375c1e3298d)

## Run locally

From the repository root, run `npm run dev` to build the public assets and start the website at http://localhost:8000. Keep that terminal running.

The startup command uses `--no-reload` to preserve Windows temporary-directory settings. Restart it after changing Laravel `.env`. Admin image uploads go directly to Cloudinary and do not use PHP's upload limits.

In another terminal, run `npm run dev:admin` for the admin app at http://localhost:3000. For live CSS/JavaScript updates, run `npm run dev:assets` in a third terminal.

The public website now lives in `laravel/`; the old `frontend/` Next.js application has been removed. Port 3000 serves the separate admin app.

Run `npm run build` to build both apps and `npm test` to run Laravel tests and the admin TypeScript check. Install dependencies and configure each app first using the setup instructions below.

## Laravel Conversion

The public PHP/Laravel + MySQL version is in `laravel/`.

See `laravel/README.md` for setup, migration, seed, and build instructions.

## Vercel Admin

The separate Node.js admin website is in `admin/`.

Deploy it to Vercel with root directory set to `admin`, then configure `DATABASE_URL`, `SESSION_SECRET`, `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, and `CLOUDINARY_API_SECRET` in Vercel.

The admin panel manages events, members, panels, pillars, gallery images, contact messages, and admin users.

