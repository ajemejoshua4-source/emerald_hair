# Emerald Hair

A luxury Emerald Hair storefront and ordering platform built on **Laravel 13**.

Emerald Hair is a hair and beauty brand. This application powers the customer
storefront (catalogue, variants, cart, checkout, delivery, online payment,
WhatsApp ordering, customer account, wishlist) and a secure administration
dashboard (products, variants, categories, orders, delivery zones, enquiries,
store settings).

> Tagline: **Beauty. Quality. Confidence.** · Brand descriptor: **Luxury Hair & Beauty.**

---

## Stack

| Layer | Choice |
| --- | --- |
| Framework | Laravel 13 (`laravel/framework ^13.0`) |
| PHP | 8.3+ (developed against 8.3, image uses 8.4) |
| Database | SQLite (local default), MySQL, or PostgreSQL (production) |
| Frontend | Blade templates with hand-written CSS (no build step required) |
| Payments | Paystack (optional; disabled without keys) |
| Tests | PHPUnit 12 (`php artisan test`) |
| Tooling | Laravel Pint, Laravel Pail |

No Node.js/NPM build step is required — the storefront ships its own styles, so
the app runs without Vite.

---

## Requirements

- PHP 8.3 or newer with `pdo_sqlite` (local) and/or `pdo_pgsql` (production)
- Composer 2
- (Optional) Node 18+ only if you choose to add a Vite pipeline

---

## Local setup

```sh
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Then visit http://localhost:8000.

Local development uses **SQLite** by default, so no database server is needed.
To use MySQL (e.g. XAMPP) or PostgreSQL, edit `DB_*` in `.env` — the commented
examples are in `.env.example`.

### Seeded administrator

After `migrate --seed` an administrator is created:

- **Email:** `admin@emeraldhair.local`
- **Password:** the value of `EMERALD_ADMIN_PASSWORD` (default `ChangeMe123!`)

Change this password before going live.

---

## Testing

```sh
php artisan test
```

The suite (54 tests) covers the storefront, search/filtering, product variants,
cart behaviour, checkout with stock transactions and delivery fees, customer
authentication, admin authorization, settings/delivery-zone/variant management
and Paystack payment verification (including webhook signature checks and
idempotency).

---

## Implemented features

**Storefront**
- Editorial homepage with featured products and brand story
- Shop with keyword search, category filter and price sorting
- Product detail pages with variant (length/lace/colour) selection
- Session-based shopping bag with quantity controls
- Wishlist for signed-in customers
- Customer registration, login, account and order history
- Checkout that runs inside a database transaction, decrements stock (product or
  variant) and applies delivery-zone fees — prevents overselling
- Optional **Paystack** online payment
- WhatsApp order handoff (does **not** mark an order as paid)
- Contact/enquiry form and newsletter signup (rate limited)

**Administration** (authentication + `is_admin` gate)
- Dashboard with counts, paid revenue and low-stock alerts
- Product CRUD with image upload validation and search
- **Product variant** management (label, SKU, price, stock, active)
- Category management
- Order list with search and status / payment updates
- **Delivery zone** management (name + fee)
- **Store settings** (email, phone, WhatsApp, Instagram, announcement)
- Customer enquiry management

**Payments (Paystack)**
- Server-side transaction initialisation (`/checkout/{order}/pay`)
- Callback that **verifies** the transaction before settling an order
- Webhook endpoint authenticated by HMAC SHA-512 signature and CSRF-exempt
- Idempotent settlement: an order can only be marked paid once
- Amount and currency are validated against the order before marking paid
- A redirect or webhook alone never marks an order paid until verified

**Security**
- Server-side authorization for every admin route
- Validation and CSRF protection on all state-changing requests
- `is_admin` is not mass-assignable (cannot be self-granted at registration)
- Rate limiting on login, registration, contact and newsletter endpoints
- Passwords hashed with the `hashed` cast
- Webhook signature verification with constant-time comparison

---

## Deployment (Render)

The repository includes a `Dockerfile`, `.dockerignore` and `render.yaml`.

1. Push this repository to GitHub.
2. In Render, create a **Blueprint** from the repo (it reads `render.yaml`), or
   create a Docker web service manually.
3. Set the following environment variables in Render:
   - `APP_KEY` — generate with `php artisan key:generate --show`
   - `APP_URL` — your Render URL (e.g. `https://emerald-hair.onrender.com`)
   - `EMERALD_ADMIN_PASSWORD` — a strong password
   - `APP_ENV=production`, `APP_DEBUG=false`
   - `DB_CONNECTION=pgsql` and `DATABASE_URL` (provided by the Render database)
   - `PAYSTACK_SECRET_KEY`, `PAYSTACK_PUBLIC_KEY`, `PAYSTACK_CALLBACK_URL`
     (optional — omit to keep WhatsApp-only checkout)
4. Deploy. The container entrypoint caches config/routes/views and runs
   `php artisan migrate --force` on boot.
5. After the first deploy, seed the database once (Render Shell, paid plans) with
   `php artisan db:seed`, or run it as a one-off job.

### Important deployment notes

- **Free Render PostgreSQL databases expire** (currently after ~30 days). Use a
  paid plan or your own database for real orders, and configure backups.
- **Render's filesystem is ephemeral.** Product images uploaded through the admin
  are stored in `public/uploads` and will be lost on redeploy. For production,
  move uploads to object storage (e.g. S3) via a filesystem disk.
- Keep `.env`, credentials and `APP_KEY` out of Git (see `.gitignore`).

---

## Payments setup (Paystack)

1. Create keys at https://dashboard.paystack.com/#/settings/developers.
2. Set `PAYSTACK_SECRET_KEY` and `PAYSTACK_PUBLIC_KEY` in `.env`.
3. Set `PAYSTACK_CALLBACK_URL` to `https://your-domain/payments/paystack/callback`.
4. In the Paystack dashboard, add the webhook URL
   `https://your-domain/payments/paystack/webhook`.

Without a secret key, online payment is disabled and the storefront falls back
to WhatsApp order confirmation.

---

## Business data still required

The following must be supplied/verified by the business before accepting real
orders (placeholder data is intentionally avoided):

- Verified product prices (currently shown as "Price on request")
- Product photography and real inventory levels
- Delivery fees and delivery regions
- Final legal policy text (delivery, returns, privacy, terms)
- Production Paystack credentials (test integration is ready)

---

## Known gaps / next phases

- **Order confirmation emails** are written to the log driver by default;
  configure a real mailer and enable order notifications.
- **Object storage** for admin-uploaded product images (S3/R2) for production.
- **Sitemap / canonical SEO metadata** beyond titles and descriptions.
- **Automated database backups** and uptime monitoring for production.
