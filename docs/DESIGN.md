# DESIGN — UI system (from real inspection of `assets/css/style.css`)

## Theme

Dark premium, mobile-first. Gold-on-near-black luxury event aesthetic.

## Colors (CSS variables)

| Token | Value | Use |
|---|---|---|
| `--bg` | `#070709` | Page background |
| `--bg-2` | `#0d0d11` | Alt section background |
| `--surface` / `--surface-2` | `#121218` / `#17171f` | Cards, panels |
| `--line` | `rgba(255,255,255,.09)` | Hairline borders |
| `--gold` | `#c9a24b` | Brand accent |
| `--gold-hi` / `--gold-lo` | `#eed9a4` / `#8a6a24` | Gradient range |
| `--silver` | `#e9e6df` | Secondary accent |
| `--red` | `#c8102e` | Alerts only (footer fine-print) |
| `--text` | `#f4f2ec` | Body text |
| `--muted` / `--muted-2` | `#a9a9b8` / `#7c7c8a` | Secondary text |
| `--radius` | `18px` | Card corner radius |

Gold gradient text: `115deg, gold-hi → gold (38%) → gold-lo (62%) → gold-hi`.

## Typography

- Headings (`h1–h3`): `"Playfair Display", Didot, Georgia, serif`, weight 700,
  line-height 1.12, letter-spacing .01em.
  - `.h-display`: `clamp(2.6rem, 8vw, 5.2rem)`; `.h-section`: `clamp(1.9rem, 5vw, 3rem)`.
- Body: Apple font stack (`-apple-system, BlinkMacSystemFont, "SF Pro Display",
  "SF Pro Text", "Helvetica Neue", Helvetica, Arial, sans-serif`), 16px, line-height 1.65.
- Eyebrow kicker: 11px, `.32em` letterspacing, uppercase, gold, with a 34px
  gold gradient rule.

## Buttons (`.btn`)

- Pill `border-radius:999px`, padding `15px 32px`, 15px / 600 weight.
- `.btn-gold`: gold gradient `115deg (gold-hi → gold 55% → gold-lo)`, dark text `#0b0a06`,
  shadow `0 8px 30px rgba(201,162,75,.28)`; hover lifts `-2px` with deeper glow.
- `.btn-ghost`: 1px `--line` border, translucent white bg; hover → gold border/text.
- `.btn-sm`: `11px 22px`, 13.5px. Active state: `scale(.97)` press feedback.
- Golden-ticket CTA bands site-wide (watermark + perforated notches + perks).

## Spacing & layout

- `.wrap`: max-width 1180px, `0 20px` gutters. `section` padding: `72px 0`.
- Fixed header: 72px, transparent → `rgba(7,7,9,.82)` + `blur(18px)` on scroll.
- Brand logo in header: 40px height. Footer logo: 88px.
- Mobile menu: full-screen overlay, staggered link reveal; breakpoints 559px / 960px.

## Brand rules

- Header always `assets/img/logo-header.png` (his silver/gold wordmark, no text beside it).
- Footer `assets/img/logo.png` (full lockup) at 88px.
- Logos used raw — **never a card/box behind them**.
- Icons: Heroicons-style line icons (inline SVG) only. **No emojis anywhere.**
- Photography: real FinFuenZe Awards 2026 event photos (`assets/img/real/web/`);
  faces kept fully visible on headshots.

## Motion

- House easing: `cubic-bezier(.22,1,.36,1)` (apple-design standard).
- Gold-dust particle hero canvas, drifting gradient orbs, gold motes on CTA bands,
  scroll reveals (`.rv` classes), count-up stats, marquee ticker, smooth scroll.
