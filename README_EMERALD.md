# Emerald Hair Website

A luxury, mobile-first Emerald Hair storefront built inside the supplied Laravel project.

## Included
- Luxury emerald/gold responsive homepage
- Product collection cards and product grid
- Category filters (Wigs, Bundles, Gadgets)
- Search overlay
- Quick-view product modal
- Persistent browser cart using localStorage
- Cart drawer with quantity controls and subtotal
- WhatsApp checkout handoff
- Mobile navigation
- Newsletter and contact interactions
- FAQ accordion
- Kampala delivery/payment messaging from the supplied specification

## Preview
Open `preview.html` in a browser for a static preview. It uses the same CSS/JS as the Laravel page. Product/editorial images are remote Unsplash images used as temporary visual placeholders; replace them with Emerald Hair photography before launch.

## Laravel
The main page is `resources/views/welcome.blade.php`. Styles and JavaScript are also copied to `public/emerald.css` and `public/emerald.js` so the page can run without a Vite build.

For normal Laravel development, install the project's Node dependencies and use the standard Vite workflow. Shopify's current theme workflow is separate; this package is a Laravel implementation of the provided Emerald Hair specification.

## Before launch
1. Replace placeholder product images with real Emerald Hair photography.
2. Replace placeholder reviews with genuine verified customer reviews.
3. Confirm product prices, inventory and variants.
4. Connect the real payment gateway and shipping logic.
5. Add final legal policy text.
6. Add official social links/email/domain.
