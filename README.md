# Mora Lenz — website + admin panel

The Photography & Videography Club of the University of Moratuwa.

Split into two applications so the admin panel is nowhere near the public server:

```
mora_lenz_web/
├── laravel/     Public website + admin API   → university server (CWP, PHP + MySQL)
├── admin/       Admin panel                  → Vercel
├── mora-lenz/   The original Next.js/Supabase app (kept for reference)
└── DEPLOYMENT.md
```

**Start with [DEPLOYMENT.md](DEPLOYMENT.md).** It covers both deployments end to end.

---

## laravel/ — public site and admin API

Laravel 12, PHP 8.2+, MySQL. No Node, no build step: the CSS and JS are hand-written plain files, so deployment is "upload and go".

**Public pages**

| Route | Page |
|---|---|
| `/` | Home — hero, featured event with countdown, featured galleries, contact form |
| `/events` | All events |
| `/events/{slug}` | One event |
| `/team` | Group → Subgroup → Member hierarchy |
| `/services/photography` `/services/videography` | Carousel, feature list, packages |
| `POST /contact` | Contact form (saved to the database; email optional) |

**Admin API** — `/api/admin/*`, bearer-token authenticated, CORS-restricted to the Vercel origin. See `routes/api.php`.

There is **no admin UI on this server**. No `/admin` route, no login page, no user accounts.

**Layout**

```
app/
├── Http/Controllers/Public_/   The website
├── Http/Controllers/Admin/     The API the panel calls
├── Http/Middleware/            AuthenticateAdmin, EnsureSuperAdmin
├── Models/
└── Support/                    AdminAuth (tokens), ImageStore (uploads)
database/migrations/            10 tables
resources/views/                Blade templates
public/css/site.css             The entire design, hand-written
public/js/site.js               Navbar, countdown, carousel, reveal-on-scroll
public/uploads/                 Where admin uploads land
```

## admin/ — admin panel

Next.js 15 (App Router), React 19, Tailwind, TypeScript. Deployed to Vercel.

| Screen | What it manages |
|---|---|
| Dashboard | Counts, and which event is in the homepage hero |
| Events | Full CRUD, ordering, featured flag, countdown toggle |
| Featured gallery | The homepage showcase rows |
| Services | Packages and carousel images, per service type |
| Team | Groups, subgroups, members, with ordering |
| Messages | Contact form inbox, read/unread |
| Admin accounts | Super admin only |
| Your account | Change your own password |

Every request to Laravel is made by Vercel's server, never the browser. The token sits in an httpOnly cookie; the browser never sees it, and never learns the API URL either.

---

## Authentication, in short

- **One hard-coded super admin**, created from `.env` by the seeder. Everyone else is created by that account inside the panel.
- **No sign-up. No password reset. No public user accounts.** A super admin sets passwords and hands them over directly.
- Tokens expire after 12 hours (configurable) and are stored only as SHA-256 hashes.
- The last active super admin cannot be deleted, demoted or deactivated.
- Changing a password or deactivating an account ends that person's sessions immediately.

---

## Database

| Table | Holds |
|---|---|
| `admins`, `admin_tokens` | Admin accounts and their sessions |
| `events` | Events, with slug, featured flag, countdown settings |
| `featured_galleries` | Homepage showcase rows |
| `service_packages`, `service_images` | Service page packages and carousel |
| `team_groups`, `team_subgroups`, `team_members` | The team hierarchy |
| `contact_messages` | Contact form submissions |

The site connects as `moralenz_web1@localhost`. `moralenz_admin@%` is not used by any code here.

---

## What changed from the original

The original `mora-lenz/` was a Next.js app on Supabase (Postgres + Supabase Auth + Supabase Storage + Resend). None of those are available on the university server, so:

| Was | Now |
|---|---|
| Supabase Postgres | MySQL |
| Supabase Auth, public user accounts | Admin-only accounts, hard-coded super admin |
| Supabase Storage buckets | `public/uploads/` on the server |
| Resend email | Messages saved to the database, email optional |
| `/admin` route in the same app | A separate deployment on Vercel |
| Tailwind + GSAP + Framer Motion | Hand-written CSS and ~200 lines of plain JS |

All the visible features were kept: the hero word-slider, the breathing background, the featured event countdown, the alternating gallery rows, the team hierarchy, the service carousels and package cards, and the contact form with its map.
