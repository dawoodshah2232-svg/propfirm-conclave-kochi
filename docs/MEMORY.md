# MEMORY — progress log: PropFirm Conclave Kochi 2026

Repo: `dawoodshah2232-svg/propfirm-conclave-kochi` · main · 31 commits (all 2026-10-07).
Live: https://dawoodshah2232-svg.github.io/propfirm-conclave-kochi/

## Done

- **2026-10-07 — full v1 site** (38b0d9e): dark premium mobile-first static site,
  18 pages (Home, About, Speakers, Agenda, Tickets, Sponsors, Venue, Blog, FAQ,
  Contact, Privacy, Terms, 404), 13-language Google-Translate switcher, full SEO
  (meta/OG/Twitter, JSON-LD Event+FAQPage+BlogPosting+ItemList, sitemap, robots).
- **2026-10-07 — full PHP+MySQL admin** (3fa9816): `/backoffice-2612/` portal,
  19 modules (dashboard, pages&banners, gallery, media, speakers, sponsors + tiers,
  agenda, tickets, orders + check-in, exhibitors, booths, P&L, blog, FAQ,
  subscribers, enquiries, users/roles, settings); `db/schema.sql` (18 tables);
  `db/seed_content.php`; dynamic PHP public pages with static fallbacks;
  tested locally on MariaDB (200, login works).
- **2026-10-07 — real photography**: FinFuenZe Awards 2026 event photos replace
  AI/stock imagery site-wide; gallery page + 24-winner hall of fame; 171MB originals
  removed from repo (web versions kept, originals in Drive, gitignored).
- **2026-10-07 — design upgrades**: Playfair Display headings; cinematic dark-gold
  hero; golden-ticket CTA bands; photo strips; sponsor logo wall with temporary
  sample logos (Exness, FundingPips, MultiBank, Vantage, Valetax, GTC).
- **2026-10-07 — mobile polish**: hero safe-centering + no-wrap KOCHI 2026, mobile
  menu overlay scroll + full-width CTA, speaker pill clipping fix, winner photo
  path fix.
- **2026-10-07 — brand**: header uses his PROPFIRM wordmark (`logo-header.png`) with
  no text beside it and no card behind; footer full lockup at 88px; Finfluenze
  Instagram `@finfluenzeofficial` in all footers.
- **2026-10-08 — AI context files**: `docs/` (PRD/ARCHITECTURE/RULES/DESIGN/TASKS/MEMORY)
  added per owner's standing repo rule.

## In progress

- Owner polishing mobile view before sending the client preview (2026-10-07).
- Client demo of the preview link — pending his go-ahead.

## Next (from TASKS.md)

- Owner confirms: event facts (dates/venue/prices/emails), speaker lineup
  (currently 6 named cards vs "announcing soon"), replace sample sponsor logos,
  cPanel login for DB deploy.
- 20-point SEO sweep (owner's 2026-10-08 standard): audit → fix → verify → commit → push.
- Admin smoke test on cPanel after deploy (manual backend review rule).
