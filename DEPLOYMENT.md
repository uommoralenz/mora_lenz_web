# Mora Lenz — deployment guide

Two separate applications, two separate hosts:

| App          | What it is                        | Where it goes                            | Who can reach it            |
| ------------ | --------------------------------- | ---------------------------------------- | --------------------------- |
| `laravel/` | Public website**+** the admin API | University server (`vh25.uom.lk`, CWP) | Everyone                    |
| `admin/`   | Admin panel                       | Vercel                                   | Only people with an account |

They share one MySQL database, but **only the Laravel app ever touches MySQL**. The Vercel panel talks to `https://your-site/api/admin/*` over HTTPS. MySQL stays closed to the internet.

---

## 1. How the pieces fit together

```
Visitor ──────────► Laravel (public_html)  ──► MySQL  (moralenz_web1@localhost)
                         ▲
                         │  HTTPS + bearer token
                         │
Admin ──► Vercel (Next.js server) ──────────┘
   ▲           │
   └───────────┘
   httpOnly cookie — the browser never sees the API token or the API URL
```

**Why this shape:** the admin's token lives in an httpOnly cookie on Vercel and every call to Laravel is made by Vercel's *server*, not the browser. If the admin panel is ever compromised in the browser, there is still no token to steal. And because the panel never connects to MySQL, port 3306 never has to be opened to the internet.

**About your two MySQL users:**

- `moralenz_web1@localhost` — this is what the site uses. Put it in `.env`.
- `moralenz_admin@%` — remote access, for phpMyAdmin. **Nothing in this project uses it.** Consider tightening it from `%` to your own IP, or leaving it disabled until you need it.

---

## 2. Deploying the public site (university server)

### 2.1 On your own computer, once

You need PHP 8.2+ and [Composer](https://getcomposer.org) locally, only to download Laravel's libraries.

```bash
cd mora_lenz_web/laravel
composer install --no-dev --optimize-autoloader
```

This creates a `vendor/` folder. You do **not** need Node or npm — the site's CSS and JS are plain files, already written, with nothing to compile.

### 2.2 Prepare `.env`

```bash
cp .env.example .env
```

Open `.env` and fill in at minimum:

| Setting                  | Value                                                                   |
| ------------------------ | ----------------------------------------------------------------------- |
| `APP_URL`              | Your exact public address, no trailing slash                            |
| `DB_DATABASE`          | `moralenz_web`                                                        |
| `DB_USERNAME`          | `moralenz_web1`                                                       |
| `DB_PASSWORD`          | That user's password                                                    |
| `ADMIN_PANEL_ORIGINS`  | Your Vercel URL (you'll get this in step 3 — come back and fill it in) |
| `SUPER_ADMIN_USERNAME` | Pick one                                                                |
| `SUPER_ADMIN_PASSWORD` | A long one.**There is no password reset.**                        |
| `SETUP_KEY`            | A long random string, temporarily                                       |

Leave `APP_KEY=` empty — the installer generates it.

### 2.3 Upload

CWP normally will not let you point the document root at Laravel's `public/` folder, so use this layout:

```
/home/moralenz/
├── moralenz_app/          ← everything EXCEPT the public folder
│   ├── app/  bootstrap/  config/  database/
│   ├── resources/  routes/  storage/  vendor/
│   ├── artisan  composer.json  .env
└── public_html/           ← the CONTENTS of laravel/public/
    ├── index.php  .htaccess  robots.txt
    ├── css/  js/  img/  uploads/
```

Steps:

1. Zip the `laravel` folder locally and upload the zip through CWP's **File Manager** (much faster than FTPing thousands of `vendor/` files one at a time), then extract it.
2. Rename the extracted folder to `moralenz_app`.
3. Move the **contents** of `moralenz_app/public/` into `public_html/`, then delete the now-empty `moralenz_app/public/`.
4. Open `public_html/index.php` and change one line near the top:

   ```php
   $appBase = __DIR__.'/..';            // before
   $appBase = __DIR__.'/../moralenz_app';   // after
   ```
5. Set permissions (File Manager → right click → Permissions, tick *Recurse into subdirectories*):

   | Path                             | Permission |
   | -------------------------------- | ---------- |
   | `moralenz_app/storage`         | `775`    |
   | `moralenz_app/bootstrap/cache` | `775`    |
   | `public_html/uploads`          | `775`    |
   | `moralenz_app/.env`            | `600`    |

   If `775` gives "Permission denied" errors later, try `777` on `storage` only.

### 2.4 Run the installer

Visit, once:

```
https://your-site/setup/THE-SETUP_KEY-YOU-CHOSE
```

You should see a plain-text log ending in `SETUP COMPLETE.` That one request generated `APP_KEY`, created every table, and created your super admin.

**Then immediately:** edit `.env`, set `SETUP_KEY=` (empty), and re-upload it. While it is filled in, anyone who guesses that URL can re-run setup.

> If `/setup/...` gives 404: `SETUP_KEY` is empty or does not match.
> If it gives 500: `storage/` is not writable — recheck permissions.
> If you *do* have SSH, skip all of this and run `php artisan migrate --seed` instead.

### 2.5 Check it

Open `https://your-site`. You should get the dark homepage with sample events and team members. `/events`, `/team`, `/services/photography` and the contact form should all work.

---

## 3. Deploying the admin panel (Vercel)

1. Push the `admin/` folder to its own GitHub repository (or the whole project, and set the Root Directory to `admin` in Vercel).
2. In Vercel: **Add New → Project → Import** that repo. Framework preset is detected as Next.js; leave the build settings alone.
3. Before the first deploy, add these **Environment Variables** (Production, and Preview if you use it):

   | Name                | Value                           |
   | ------------------- | ------------------------------- |
   | `LARAVEL_API_URL` | `https://your-site/api/admin` |
   | `SESSION_COOKIE`  | `moralenz_admin_session`      |

   None of these are `NEXT_PUBLIC_*`, on purpose — that is what keeps the API URL out of the browser.
4. Deploy. Note the URL Vercel gives you, e.g. `https://moralenz-admin.vercel.app`.
5. **Go back to the Laravel `.env`** and set:

   ```
   ADMIN_PANEL_ORIGINS=https://moralenz-admin.vercel.app
   ```

   Re-upload `.env`. (This is a CORS allow-list. The server-to-server calls work without it, but it closes the door on any other site trying to call your API from a browser.)
6. Open the Vercel URL, sign in with the super admin username and password from `.env`.
7. Go to **Your account → Change password** and set a new one. The `.env` password has been sitting in a file; retire it.

---

## 4. Day-to-day use

| I want to…                   | Where                                                         |
| ----------------------------- | ------------------------------------------------------------- |
| Add an event                  | Admin → Events → New event                                  |
| Change the big homepage event | Tick**Featured** on an event (only one can be featured) |
| Add photos to the homepage    | Admin → Featured gallery                                     |
| Change prices                 | Admin → Services                                             |
| Add a committee member        | Admin → Team                                                 |
| Read contact form messages    | Admin → Messages                                             |
| Add another admin             | Admin → Admin accounts (super admin only)                    |

Everything saves straight to the live site — there is no publish step.

### Admin accounts

- Only a **super admin** sees the *Admin accounts* page.
- To add someone: fill in name, username and a password, then tell them those two things directly. There is no email invite and no "forgot password" link — by design.
- If someone forgets their password, a super admin sets a new one on that page.
- The last remaining super admin cannot be deleted, demoted or deactivated. That guard rail exists so nobody can accidentally lock the whole club out.
- Changing a password or deactivating an account signs that person out everywhere, immediately.

**If the super admin password itself is lost**, there is no recovery through the app. You would set a new bcrypt hash directly on the `admins` table in phpMyAdmin.

---

## 5. Making changes later

### Public site (text, layout, colours)

Edit the files and re-upload just those:

| What                                | File                                                                |
| ----------------------------------- | ------------------------------------------------------------------- |
| Colours, spacing, all styling       | `public_html/css/site.css`                                        |
| Countdown, menu, carousel behaviour | `public_html/js/site.js`                                          |
| Homepage sections                   | `moralenz_app/resources/views/pages/home.blade.php`               |
| Menu links                          | `moralenz_app/resources/views/partials/navbar.blade.php`          |
| Footer                              | `moralenz_app/resources/views/partials/footer.blade.php`          |
| Service page copy and feature lists | `moralenz_app/app/Http/Controllers/Public_/ServiceController.php` |

After editing CSS or JS, bump `ASSET_VERSION` in `.env` (e.g. `ASSET_VERSION=2`) so browsers fetch the new file instead of a cached copy.

### Admin panel

Push to GitHub — Vercel redeploys automatically.

### Site images

Put `logo.png`, `favicon.png` and `landing-bg.jpeg` in `public_html/img/`. The templates use them if present and fall back to a text logo / plain background if not.

---

## 6. Troubleshooting

| Symptom                                           | Cause                                          | Fix                                                                                                      |
| ------------------------------------------------- | ---------------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| Blank white page                                  | `storage/` not writable, or a PHP error      | Set`APP_DEBUG=true` in `.env` briefly, reload, read the message, **then set it back to false** |
| 500 on every page                                 | `APP_KEY` missing                            | Re-run the installer, or set`APP_KEY` from `php artisan key:generate --show`                         |
| Panel says "Could not reach the Mora Lenz server" | `LARAVEL_API_URL` wrong, or the site is down | Open`LARAVEL_API_URL` in a browser — it should return JSON, not HTML                                  |
| Panel bounces you to login immediately            | Token expired (12h by default)                 | Sign in again; raise`ADMIN_TOKEN_LIFETIME_HOURS` if you like                                           |
| Login says "credentials do not match"             | Wrong username/password, or setup never ran    | Check the`admins` table exists in phpMyAdmin                                                           |
| Images upload but show broken                     | `APP_URL` does not match the real address    | Fix`APP_URL`, then bump `ASSET_VERSION`                                                              |
| Uploads fail over ~2 MB                           | PHP limits, not Laravel                        | CWP → PHP settings: raise`upload_max_filesize` and `post_max_size` to 10M                           |
| Contact form always succeeds but no email         | `CONTACT_MAIL_ENABLED=false`                 | That is the default. Messages are still saved — read them in Admin → Messages                          |

---

## 7. Security notes

What this setup does deliberately:

- **No public user accounts.** There is no registration, no login, no user table on the public site. The only accounts that exist are admins.
- **No password reset flow.** No reset emails means no reset-token to steal and no mail server to secure. A super admin sets passwords by hand.
- **Admin code is not on the public server.** The panel is a separate deployment; there is no `/admin` route on the university server to find or attack.
- **Tokens are hashed at rest.** `admin_tokens` stores only a SHA-256 hash, so a database dump cannot be replayed as a login.
- **Login is rate limited.** Five failed attempts per username+IP triggers a 15-minute lockout.
- **Uploads cannot execute.** `public_html/uploads/.htaccess` disables the PHP engine in that folder, so even a file that sneaks past validation cannot run.
- **The contact form is rate limited and honeypotted** — five submissions per IP per hour, plus a hidden field bots fill in and humans do not.

Things worth doing yourself:

- Force HTTPS in CWP and make sure the certificate covers your domain.
- Restrict `moralenz_admin@%` to a specific IP, or disable it when not in use.
- Keep `APP_DEBUG=false` in production. It leaks file paths and config when true.
