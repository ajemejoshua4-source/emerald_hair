<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop');
Route::get('/products/{product:slug}', [StorefrontController::class, 'product'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Contact & newsletter (rate limited against abuse)
|--------------------------------------------------------------------------
*/
Route::get('/contact', [ContactController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'enquiry'])->middleware('throttle:10,1')->name('contact.send');
Route::post('/newsletter', [ContactController::class, 'newsletter'])->middleware('throttle:10,1')->name('newsletter');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Shopping bag
|--------------------------------------------------------------------------
*/
Route::get('/bag', [CartController::class, 'index'])->name('cart');
Route::post('/bag/{product}/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/bag', [CartController::class, 'update'])->name('cart.update');
Route::delete('/bag/{key}', [CartController::class, 'remove'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| Customer account, wishlist & checkout
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/checkout/{order}/pay', [PaymentController::class, 'pay'])->name('payments.pay');
    Route::get('/payments/paystack/callback', [PaymentController::class, 'callback'])->name('payments.callback');
});

/*
|--------------------------------------------------------------------------
| Payment webhooks (unauthenticated, CSRF-exempt, signature verified)
|--------------------------------------------------------------------------
*/
Route::post('/payments/paystack/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'productCreate'])->name('products.create');
    Route::post('/products', [AdminController::class, 'productStore'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'productEdit'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'productUpdate'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'productDelete'])->name('products.delete');

    Route::get('/products/{product}/variants', [AdminController::class, 'variants'])->name('variants');
    Route::post('/products/{product}/variants', [AdminController::class, 'variantStore'])->name('variants.store');
    Route::put('/variants/{variant}', [AdminController::class, 'variantUpdate'])->name('variants.update');
    Route::delete('/variants/{variant}', [AdminController::class, 'variantDelete'])->name('variants.delete');

    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'categoryStore'])->name('categories.store');
    Route::delete('/categories/{category}', [AdminController::class, 'categoryDelete'])->name('categories.delete');

    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::patch('/orders/{order}', [AdminController::class, 'orderUpdate'])->name('orders.update');

    Route::get('/enquiries', [AdminController::class, 'enquiries'])->name('enquiries');
    Route::patch('/enquiries/{enquiry}', [AdminController::class, 'enquiryUpdate'])->name('enquiries.update');

    Route::get('/delivery-zones', [AdminController::class, 'deliveryZones'])->name('delivery-zones');
    Route::post('/delivery-zones', [AdminController::class, 'deliveryZoneStore'])->name('delivery-zones.store');
    Route::delete('/delivery-zones/{zone}', [AdminController::class, 'deliveryZoneDelete'])->name('delivery-zones.delete');

    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'settingsUpdate'])->name('settings.update');
});
