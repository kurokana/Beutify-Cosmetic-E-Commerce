<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ShippingController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\VoucherController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->middleware(['auth', 'verified', 'redirect.dashboard.by.role'])->name('dashboard');

// Breeze default profile routes (kept for backward compatibility)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Customer profile routes (Requirements 1.8, 1.9)
Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    // Addresses: allow creating addresses via AJAX from checkout/profile
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
});

// Catalog & Search routes (Requirements 2.1, 2.8, 2.9, 2.10, 13.5)
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{slug}', [ProductController::class, 'show'])->name('catalog.show');
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Cart routes — protected by auth (Requirements 3.1–3.8)
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
});

// Checkout routes — protected by auth + verified (Requirements 4.1, 4.2, 4.7, 4.8)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

// Voucher validation AJAX endpoint (Requirements 4.5, 4.6, 10.5, 10.6)
Route::middleware(['auth'])->group(function () {
    Route::post('/voucher/validate', [VoucherController::class, 'validate'])->name('voucher.validate');
});

// Shipping cost calculation AJAX endpoint (Requirements 4.3, 4.4, 6.1, 6.2)
Route::middleware(['auth'])->group(function () {
    Route::get('/shipping/cost', [ShippingController::class, 'calculateCost'])->name('shipping.cost');
});

// Payment routes — protected by auth + verified (Requirements 5.1, 5.2)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/create/{order}', [PaymentController::class, 'create'])->name('payment.create');
});

// Order history & detail routes — protected by auth + verified (Requirements 5.9)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
});

// Review routes — protected by auth (Requirements 7.1, 7.2, 7.3, 7.4)
Route::middleware(['auth'])->group(function () {
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// Wishlist routes — protected by auth (Requirements 8.1, 8.2, 8.3, 8.4, 8.5)
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/{product}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.moveToCart');
});

// Admin Panel routes — protected by auth + admin role (Requirement 14.5)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products (Requirements 9.1, 9.2, 9.3, 9.4, 9.7)
    Route::resource('products', AdminProductController::class);
    Route::patch('/products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('products.toggle-active');
    Route::delete('/product-images/{image}', [AdminProductController::class, 'deleteImage'])->name('product-images.destroy');

    // Brands (Requirements 9.5, 9.6, 9.7, 9.8)
    Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
    Route::patch('/brands/{brand}/toggle-active', [\App\Http\Controllers\Admin\BrandController::class, 'toggleActive'])->name('brands.toggle-active');
    Route::delete('/brands/{brand}/delete-logo', [\App\Http\Controllers\Admin\BrandController::class, 'deleteLogo'])->name('brands.delete-logo');

    // Categories (Requirements 10.1, 10.2)
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

    // Vouchers (Requirements 10.3, 10.4, 10.5, 10.6)
    Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class);
    Route::patch('/vouchers/{voucher}/toggle-active', [\App\Http\Controllers\Admin\VoucherController::class, 'toggleActive'])->name('vouchers.toggle-active');

    // Reviews (Requirement 7.6)
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Orders (Requirements 11.1, 11.2, 11.3, 11.4, 6.3)
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/tracking', [\App\Http\Controllers\Admin\OrderController::class, 'updateTracking'])->name('orders.update-tracking');

    // Users (Requirements 12.1, 12.2, 12.3)
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Admin Activity Logs (Requirement 12.4)
    Route::get('/logs', [\App\Http\Controllers\Admin\AdminLogController::class, 'index'])->name('logs.index');
    Route::get('/logs/{log}', [\App\Http\Controllers\Admin\AdminLogController::class, 'show'])->name('logs.show');

    // Reports (Requirements 11.5)
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';
