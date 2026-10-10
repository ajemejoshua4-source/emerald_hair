# Emerald Hair — Laravel + Render

A Laravel 12 full-stack luxury e-commerce foundation for Emerald Hair.

## Included
- Premium responsive storefront
- Product categories, search and filtering
- Product detail pages
- Session shopping cart
- Customer registration/login/logout
- Wishlist for signed-in customers
- Checkout and order creation
- WhatsApp order handoff
- Customer account + order history
- Admin dashboard with product/category/order/enquiry management
- Inventory and product availability fields
- Contact/enquiry storage
- Newsletter subscription storage
- PostgreSQL migrations and seed data
- CSRF-protected forms and authorization middleware
- Docker + Nginx + PHP-FPM for Render

## Local setup
1. Install PHP 8.2+ and Composer.
2. Start MySQL (XAMPP) and create a database named `emerald_hair`.
3. Copy `.env.example` to `.env` and set `DB_DATABASE=emerald_hair`.
4. Run `composer install`.
5. Run `php artisan key:generate`.
6. Run `php artisan migrate --seed`.
7. Run `php artisan serve` and open http://localhost:8000.

## VS Code
Open the project folder in VS Code and use the integrated terminal for the artisan commands above, or run the **"Serve: Laravel"** task from **Terminal → Run Task**.

## Git / GitHub
```bash
git init
git add .
git commit -m "Initial commit: Emerald Hair Laravel storefront"
git branch -M main
git remote add origin https://github.com/ajemejoshua4-source/emerald_hair.git
git push -u origin main
```

To push later changes:
```bash
git add .
git commit -m "Describe your change"
git push
```

Notes:
- `.env` and `vendor/` are git-ignored and are **not** pushed. After cloning on another machine, run `composer install`, copy `.env.example` to `.env`, then `php artisan key:generate`.
- Authenticate `git push` with a Personal Access Token (or GitHub CLI/SSH) if Git asks for credentials.

Seeded admin:
Email: admin@emeraldhair.local
Password: ChangeMe123!

Change this immediately for any real deployment.

## Render
Render's current Laravel guidance uses Docker + PostgreSQL. The included `render.yaml` can provision both services, but the Free PostgreSQL database is temporary and expires after 30 days. Use a paid database for persistent client data.

Set `APP_KEY` in Render to the output of `php artisan key:generate --show`.
