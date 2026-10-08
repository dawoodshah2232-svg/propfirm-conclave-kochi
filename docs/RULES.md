# RULES — coding standards for this repo

## Stack (owner standing rules — non-negotiable)

- Public site: vanilla HTML + CSS + JS, **no build step, no frameworks**.
- Backend: **MySQL + PHP only** (cPanel rule). No Supabase/Postgres, no Node backends.

## Git discipline

- `git fetch origin` + pull latest `main` **before any work**. Never work from a stale copy.
- Never force push. Never `git reset --hard` without his explicit authorization.
- GitHub Pages rule: after each push, check `gh api repos/dawoodshah2232-svg/propfirm-conclave-kochi/pages/builds`
  and wait for status `built` before pushing again. Never stack pushes while a build runs.
- Frontend auto-deploys (Pages) are fine. **Backend is NEVER auto-deployed:**
  every backend batch gets a manual security review before his IT man deploys it
  (owner's standing deploy policy, 2026-10-07).

## Content honesty

- **Never invent facts.** Dates, venue, ticket prices, contact emails, speaker names,
  sponsor logos, and stats are only what the repo shows; anything unconfirmed is a
  TODO, never a guess.
- Event facts to confirm live in `README.md` ("Event facts to confirm with the organiser").
- Speaker lineup: owner direction is "announcing soon" — the 6 named cards in
  `speakers.html` are unverified placeholders until he confirms or replaces them.
- Sponsor wall logos are temporary samples — replace before go-live.
- `/backoffice-2612/` must **never be linked anywhere on the public site**.

## Design & UI

- Body font (always): `-apple-system, BlinkMacSystemFont, "SF Pro Display",
  "SF Pro Text", "Helvetica Neue", Helvetica, Arial, sans-serif`.
  Headings: Playfair Display. Never Roboto/Titillium/Montserrat as primary.
- No emojis in the UI. Icons = Heroicons-style line icons (inline SVG) only.
- Logos used raw, never on a card/box: header `assets/img/logo-header.png`,
  footer `assets/img/logo.png` (88px).
- UI work follows the apple-design + web-animations standards:
  dark premium theme, faded cinematic heroes (never flat banners), press feedback,
  interruptible animations, house easing `cubic-bezier(.22,1,.36,1)`.
- Bump asset `?v=` cache-bust params on every release (CSS and JS).

## Workflow for agents

- Read these docs + `README.md` before writing any code; update TASKS.md/MEMORY.md as work completes.
- Inspect before editing; test after editing; `git diff --check` before committing.
- Never claim success before verifying (visual fixes count as done only when verified).
- Low-risk aligned improvements found while working: fix and document the change.
