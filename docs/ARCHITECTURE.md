# ARCHITECTURE — PropFirm Conclave Kochi 2026

## Stack

- Public site: pure HTML + CSS + JS. **No build step, no frameworks, no npm.**
  - `assets/css/style.css` — single stylesheet, CSS custom properties.
  - `assets/js/main.js` — single script (countdown, particles, reveals, counters,
    marquee, mobile menu, Google Translate init).
- Backend (cPanel hosting): **PHP + MySQL only** (owner's standing stack rule).
  - `includes/site.php` — public data layer (PDO + MySQLi helpers, `site_db()`,
    `site_setting()`, `block()`, `speakers_list()`, `sponsors_by_tier()`).
  - `includes/head.php`, `includes/header.php`, `includes/footer.php` — shared partials.
  - `backoffice-2612/` — admin portal, plain PHP, no frameworks
    (`_config.php`, `_db.php`, `_auth.php`, `_layout.php` + 19 module pages).
- DB: `db/schema.sql` (18 tables, `CREATE TABLE IF NOT EXISTS`), `db/seed_content.php`
  (CLI seeder that migrates current static content into the DB).

## Dual public pages: `.html` vs `.php`

Every public page exists twice (e.g. `index.html` / `index.php`, `tickets.html` /
`tickets.php`). PHP pages read the admin database and render dynamically;
**if the DB is unreachable, every helper falls back to baked-in static defaults**,
so pages never break. On GitHub Pages the `.html` file serves (static fallback);
on cPanel PHP hosting the `.php` pages take over automatically.

## Data flow

- Admin edits content/orders → stored in MySQL (18 tables: `settings`,
  `content_blocks`, `speakers`, `sponsor_tiers`, `sponsors`, `gallery`,
  `ticket_types`, `ticket_orders`, `booths`, `exhibitors`, `transactions`,
  `agenda_days`, `agenda_sessions`, `posts`, `faqs`, `subscribers`, `enquiries`,
  `admin_users`).
- Public PHP pages pull from DB via `includes/site.php`; static HTML copies carry
  the same content as fallback.
- Ticket booking on `tickets.php` creates `pending` orders for the admin to confirm
  (orders module: mark paid, check-in).
- Newsletter → `subscribe.php` → `subscribers`. Contact form → `enquiries`.
- Uploads (JPG/PNG/WebP, max 5 MB) stored in `assets/uploads/`; must be 0755.

## Deploy

- GitHub Pages: `main` branch, root `/`. Any static host works.
- cPanel: create MySQL DB + user → import `db/schema.sql` → run
  `php db/seed_content.php <host> <user> <pass> <dbname>` →
  upload files → set DB credentials in `backoffice-2612/_config.php` and
  `includes/site.php` → sign in at `/backoffice-2612/`.

## Assets & cache discipline

- Real event photography in `assets/img/real/web/` (FinFuenZe Awards 2026 shots);
  originals live in Drive and are gitignored (`assets/img/real/` originals excluded).
- Asset cache-busting is manual: CSS/JS URLs carry `?v=YYYYMMDDx` query params
  (e.g. `?v=20261007e` on `main.js`); **bump on every release**.
