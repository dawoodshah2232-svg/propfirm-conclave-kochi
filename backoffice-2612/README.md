# PropFirm Conclave Kochi 2026 — Admin Portal

Full CMS + event operations backend (PHP + MySQL, no frameworks).

## What's inside

| Area | URL | Does |
|---|---|---|
| Dashboard | `dashboard.php` | Tickets sold, revenue, exhibitors, booths, P&L net, subscribers |
| Pages & Banners | `pages.php` | Edit any text/banner/image per page + **live preview** |
| Gallery | `gallery.php` | Add/remove/reorder images, captions |
| Media Library | `media.php` | All uploaded images in one place |
| Speakers | `speakers.php` | Add/edit photos, **drag to reorder** |
| Sponsors | `sponsors.php` | Logo upload, tier assignment, **drag to reorder** |
| Sponsor Tiers | `sponsor_tiers.php` | Title/Platinum/Gold… — add, recolour, reorder |
| Agenda | `agenda.php` | Days + sessions |
| Ticket Types | `tickets.php` | Name, price, description, visibility |
| Orders | `orders.php` | Attendees, mark paid, **check-in**, manual orders |
| Exhibitors | `exhibitors.php` | Companies, booth assignment, packages |
| Booths | `booths.php` | Booth map, sizes, prices, availability |
| P&L | `pnl.php` | Income/expense ledger + net report |
| Blog | `blog.php` | Posts with covers, draft/published |
| FAQs | `faq.php` | Add/edit/**reorder** |
| Subscribers | `subscribers.php` | Newsletter list + CSV export |
| Enquiries | `enquiries.php` | Contact inbox |
| Admin Users | `users.php` | Roles: super / editor |
| Settings | `settings.php` | Event facts, contact emails, social links |

Admin lives at **`/backoffice-2612/`** — deliberately NOT linked anywhere on the public site.

## Deploy (cPanel)

1. Create a MySQL database + user in cPanel.
2. Import `db/schema.sql`, then run `php db/seed_content.php <host> <user> <pass> <dbname>` (migrates current site content).
3. Upload all files to `public_html/`.
4. Edit DB credentials in `backoffice-2612/_config.php` and `includes/site.php`.
5. Ensure `assets/uploads/` is writable (0755).
6. Open `https://yourdomain.com/backoffice-2612/` and sign in.

On PHP hosting the `.php` pages take over automatically; the `.html` files remain as a static fallback.

## Notes

- Uploads: JPG/PNG/WebP, max 5 MB, stored in `assets/uploads/`.
- Drag-to-reorder lists save automatically on drop.
- The newsletter form posts to `subscribe.php`; the contact form writes to `enquiries`.
- Ticket bookings on `tickets.php` create `pending` orders for the admin to confirm.
