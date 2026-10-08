# TASKS — PropFirm Conclave Kochi 2026

Derived from real repo state. Status as of 2026-10-08. Unknowns are TODO, not guesses.

## Owner confirmations (blocked on him)

- [ ] TODO — Confirm event facts: dates (12–13 Dec 2026), venue (Adlux International
  Convention Centre, Angamaly, Kochi), ticket prices (₹999/₹1,999/₹4,999 + student
  ₹499 + group 15% off), emails (events@ / sponsors@finfluenze.com). These are
  initial values in the HTML until he confirms.
- [ ] TODO — Speaker lineup: `speakers.html` shows 6 named cards; owner direction is
  "announcing soon". Confirm names or replace with announcing-soon treatment.
- [ ] TODO — Replace temporary sponsor sample logos (Exness, FundingPips, MultiBank,
  Vantage, Valetax, GTC, honor, opo in `assets/img/sponsors/`) before go-live.
- [ ] TODO — cPanel deploy needs his login: create MySQL DB, import `db/schema.sql`,
  run `php db/seed_content.php`, set credentials in `backoffice-2612/_config.php` and
  `includes/site.php`, make `assets/uploads/` writable.

## In progress

- [~] Mobile polish — owner was polishing mobile view (2026-10-07) before sending the
  client preview: hero centering/no-wrap KOCHI 2026, mobile menu CTA + scroll fix,
  speaker pill clipping. Verify on his phone screen.
- [~] Client demo — preview link ready at
  https://dawoodshah2232-svg.github.io/propfirm-conclave-kochi/; send once mobile
  polish verified.

## Ready to do (unblocked, real repo gaps)

- [ ] 20-point SEO sweep (owner's 2026-10-08 standard): audit baseline first
  (crawlability, meta, H1 hierarchy, alt text, schema, internal links, WebP
  compression, Core Web Vitals, OG tags, accessibility ≥95), then fix, verify,
  commit, push. Note: some blog images are JPG — compress/convert where sensible.
- [ ] Keep `?v=` cache-bust params in sync: any CSS/JS change must bump both
  the stylesheet and `main.js` query params (HTML + PHP copies).
- [ ] Admin smoke test on cPanel once deployed: login, one content edit, one test
  order + check-in, subscribers CSV export — per the manual backend review rule
  before anything is called live.

## Done (2026-10-07, from git log)

- Full v1 site: dark premium theme, 18 pages, blog, SEO, 13-language switcher
  (38b0d9e).
- Full PHP+MySQL admin portal at `/backoffice-2612/` (19 modules) + `db/schema.sql`
  + `db/seed_content.php` (3fa9816); tested locally on MariaDB (200, login works).
- Real FinFuenZe Awards 2026 photography replaces AI/stock imagery site-wide;
  gallery page + 24-winner hall of fame; web-optimized versions in repo, originals
  in Drive + gitignored (0dcfba1, 16ee399, 7ed0559, b469cc5).
- Playfair Display headings; golden-ticket CTA bands; hero particle canvas +
  gold motes; mobile hero/menu fixes (bd868b9 … 1afeed6).
