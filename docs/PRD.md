# PRD — PropFirm Conclave Kochi 2026

## What it is

Marketing website + operations backend for **PropFirm Conclave Kochi 2026**, a
proprietary-trading event organised by **Finfluenze**.
Tagline: **Connect · Trade · Get Funded**.

Live preview: https://dawoodshah2232-svg.github.io/propfirm-conclave-kochi/
Repo: `dawoodshah2232-svg/propfirm-conclave-kochi` (public, GitHub Pages on `main`).

## Users

- Attendees: traders interested in prop-firm funding (tickets, agenda, speakers, venue, FAQ).
- Exhibitors / sponsors: prop firms and brokers (sponsor tiers, booths, contact).
- Event admins (Finfluenze team): manage content, tickets, orders, check-in, P&L via the
  private admin portal at `/backoffice-2612/` (not linked anywhere on the public site).

## Features (public site, per repo inspection)

- 13 static pages + blog + 404: Home, About, Speakers, Agenda (2-day), Tickets, Sponsors,
  Venue, Blog index + 5 articles, Gallery, FAQ, Contact, Privacy, Terms, 404.
- Hero with animated gold-dust particle canvas, drifting gradient orbs, scroll reveals,
  count-up stats, marquee ticker, countdown to 2026-12-12T09:00:00+05:30.
- 13-language switcher via Google Translate (`google.translate.TranslateElement`),
  English default: EN, HI, ML, TA, TE, KN, AR, UR, FR, ES, PT, RU, TR.
- SEO: per-page meta/OG/Twitter, JSON-LD (Event, FAQPage, BlogPosting, ItemList),
  `sitemap.xml`, `robots.txt`, semantic HTML.
- Newsletter form → `subscribe.php` (subscribers table); contact form → enquiries table.

## Features (admin portal `/backoffice-2612/`, PHP + MySQL, no frameworks)

19 modules: Dashboard, Pages & Banners (with live preview), Gallery, Media Library,
Speakers (drag-to-reorder), Sponsors (logo upload + tier assignment + reorder),
Sponsor Tiers, Agenda (days + sessions), Ticket Types, Orders (attendees, paid flag,
check-in, manual orders), Exhibitors, Booths, P&L ledger, Blog, FAQs (reorder),
Subscribers (CSV export), Enquiries, Admin Users (super / editor roles), Settings.

## Facts NOT confirmed with the organiser (initial values only)

- Dates **12–13 Dec 2026**, venue **Adlux International Convention Centre, Angamaly, Kochi**.
- Ticket prices **₹999 / ₹1,999 / ₹4,999** (+ student ₹499, group 15% off).
- Contact emails **events@finfluenze.com**, **sponsors@finfluenze.com**.
- Speaker lineup — `speakers.html` currently shows 6 named cards; owner direction is
  "announcing soon", so these names are **unverified** and need his confirm/replace.
- Sponsor logos on the wall (Exness, FundingPips, MultiBank, Vantage, Valetax, GTC,
  honor, opo) are **temporary samples — replace before go-live**.
