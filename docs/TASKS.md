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

- [x] 20-point SEO sweep (owner's 2026-10-08 standard) — DONE 2026-10-08 (commit
  "seo: apply 20-fix sweep"): 9 titles shortened to ≤60 chars, 12 descriptions
  rewritten to 50–160 chars, heading hierarchy fixed (footer h4→p, agenda/tickets/
  blog h3→h2), BreadcrumbList JSON-LD on all 18 public pages, OG/Twitter tags added
  to 404/privacy/terms, 68 JPGs converted to WebP (16.7MB→9.0MB, originals kept),
  width/height on all imgs, hero WebP preload + fetchpriority, skip-link +
  :focus-visible + reduced-motion CSS, tap targets ≥44px (nav/lang/burger/btn-sm/
  footer links), removed duplicate mobile Gallery link. Verified: 0 broken refs,
  0 missing alt, 1 H1/page, sitemap 18/18 valid, ?v= bumped to 20261008a (HTML+PHP).
- [ ] TODO(owner-UI) — Google Search Console: (1) add property for
  https://dawoodshah2232-svg.github.io/propfirm-conclave-kochi/, (2) paste the
  verification meta tag at the marked TODO comment in index.html `<head>`,
  (3) submit sitemap.xml URL in GSC, (4) request indexing for /, tickets.html,
  agenda.html. Repo-side done: robots.txt references sitemap; placeholder comment
  in index.html head.
- [ ] TODO(owner-UI) — Bing Webmaster Tools: add site + submit sitemap.xml
  (same URL as above). Optional but cheap.
- [ ] Backlink strategy (earn via content only — never buy/spam links):
  (1) publish the 5 blog articles' key takeaways as LinkedIn/X threads linking
  back to the article; (2) list the event on free event directories (AllEvents,
  Townscript/Meetup-style listings, Kerala startup/trading communities);
  (3) ask confirmed speakers/sponsors to link the event from their own
  sites/social bios once announced; (4) Finfluenze Instagram bio link → site;
  (5) post-event: publish recap + winner photos (linkable asset for trading
  press). No link farms, no paid placements, no reciprocal-link schemes.
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
