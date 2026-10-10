# Emerald Hair

A luxury Emerald Hair storefront and ordering platform built on **Laravel 13**.

Emerald Hair is a hair and beauty brand. This application powers the customer
storefront (catalogue, cart, checkout, WhatsApp ordering, customer account,
wishlist) and a secure administration dashboard (products, categories, orders,
enquiries).

> Tagline: **Beauty. Quality. Confidence.** · Brand descriptor: **Luxury Hair & Beauty.**

---

## Stack

| Layer | Choice |
| --- | --- |
| Framework | Laravel 13 (`laravel/framework ^13.0`) |
| PHP | 8.3+ (developed against 8.3, image uses 8.4) |
| Database | SQLite (local default), MySQL, or PostgreSQL (production) |
| Frontend | Blade templates with hand-written CSS (no build step required) |
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

The suite covers the storefront, product filtering, cart behaviour, checkout
(with stock transactions), customer authentication, admin authorization and
enquiries/newsletter signup.

---

## Implemented features

**Storefront**
- Editorial homepage with featured products and brand story
- Shop with keyword search, category filter, and price sorting
- Product detail pages with availability and "Price on request" handling
- Session-based shopping bag with quantity controls
- Wishlist for signed-in customers
- Customer registration, login, account and order history
- Checkout that creates orders inside a database transaction and decrements
  stock (prevents overselling)
- WhatsApp order handoff (does **not** mark an order as paid)
- Contact/enquiry form and newsletter signup (rate limited)

**Administration** (protected by authentication + an `is_admin` gate)
- Dashboard with real counts, paid revenue and low-stock alerts
- Product CRUD with image upload validation
- Category management
- Order status / payment status updates
- Customer enquiry management

**Security**
- Server-side authorization for every admin route
- Form request validation and CSRF protection on all state-changing requests
- `is_admin` is not mass-assignable (cannot be self-granted via registration)
- Rate limiting on login, registration, contact and newsletter endpoints
- Passwords hashed with the `hashed` cast

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

## Business data still required

The following must be supplied/verified by the business before accepting real
orders (placeholder data is intentionally avoided):

- Verified product prices (currently shown as "Price on request")
- Product photography and real inventory levels
- Delivery fees and delivery regions
- Final legal policy text (delivery, returns, privacy, terms)
- Payment provider credentials (no payment gateway is wired up yet)

---

## Known gaps / next phases

- **Product variants** (length, lace type, colour, density) are not yet modelled.
- **Paystack (or other) payment integration** is not implemented — checkout
  currently creates an unpaid order for WhatsApp confirmation only.
- **Store settings, delivery-address, and payment-transaction tables** from the
  extended specification are not yet added.
- **Sitemap / canonical SEO metadata** beyond titles and descriptions.
- Emails are written to the log driver by default; configure a real mailer for
  order notifications.
