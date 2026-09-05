# Project phases

Status inventory for Diamond Steam Car Wash (`dscw`).

## Phase 1 — Complete

- Laravel + Tailwind + Alpine foundation
- Admin authentication and grouped sidebar
- Roles, permissions, and audit logging
- Centralized business settings

## Phase 2 — Complete

- Services and categories admin CRUD
- Multi-location + working hours
- Booking engine with live availability
- Dual payment (Razorpay or pay at location)
- Quill WYSIWYG (description fields) with image uploads
- Service cover images on public disk
- Collapsible admin sidebar + deploy notes

## Phase 3 — Complete

- [x] Content CMS admin: FAQs, pages, blog posts, gallery, testimonials
- [x] Contact enquiry inbox
- [x] Email notifications (booking + contact)
- [x] Booking calendar UI (admin month view)
- [x] Coupons (admin + booking apply)

## Phase 4 — Complete (ops slice)

- [x] Audit log UI
- [x] Users & admins UI
- [x] Cache & settings system page (SEO / email / analytics)
- [x] Sitemap + robots.txt (+ `sitemap:generate` scheduled daily)
- [x] Booking reports + revenue reports (CSV export)

### Phase 4b — Complete

- [x] Service add-ons (admin CRUD + booking pricing/duration/slots)
- [x] Media library + configurable disk (`MEDIA_DISK` / S3-ready)
- [x] SMS confirmations & reminders (MSG91 / Twilio + hourly schedule)

See also [DEPLOYMENT.md](DEPLOYMENT.md) for production/shared-hosting steps.
