<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FlashSaleController as AdminFlashSaleController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\OrderHistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Digital Store & Game Top-Up
|--------------------------------------------------------------------------
*/

// 1. Authentication & Profile Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// 2. Public Product Catalog & Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product:slug}', [OrderController::class, 'showCheckoutForm'])->name('products.checkout');

// 3. Order Processing & Invoicing
Route::post('/order/checkout', [OrderController::class, 'store'])->name('orders.store');
Route::get('/invoice/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{orderNumber}/status', [OrderController::class, 'checkStatus'])->name('orders.check-status');
Route::post('/invoice/{orderNumber}/upload-proof', [OrderController::class, 'uploadPaymentProof'])->name('orders.upload-proof');
Route::post('/vouchers/check', [VoucherController::class, 'check'])->name('vouchers.check');

// 4. Payment Gateway Webhook (Reserved if needed)
Route::post('/payments/webhook', [PaymentCallbackController::class, 'handle'])->name('payments.webhook');

// 5. Customer Protected Area (Transaction History & Credentials)
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrderHistoryController::class, 'show'])->name('orders.show');
});

// 6. Admin Protected Area (Management & Statistics)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Orders Management & Payment Verification
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/confirm', [AdminOrderController::class, 'confirmPayment'])->name('orders.confirm');
    Route::post('orders/{order}/reject', [AdminOrderController::class, 'rejectPayment'])->name('orders.reject');

    // Product Management
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/variants', [AdminProductController::class, 'storeVariant'])->name('products.variants.store');
    Route::post('variants/{variant}/add-stock', [AdminProductController::class, 'addStock'])->name('variants.add-stock');
    Route::delete('variants/{variant}', [AdminProductController::class, 'destroyVariant'])->name('variants.destroy');

    // Manual Payment Methods / Bank Accounts Management
    Route::resource('payment-methods', AdminPaymentMethodController::class);
    Route::patch('payment-methods/{paymentMethod}/toggle', [AdminPaymentMethodController::class, 'toggle'])->name('payment-methods.toggle');

    // Promo Banners & Sliders Management
    Route::resource('banners', AdminBannerController::class)->except(['show', 'create', 'edit']);
    Route::patch('banners/{banner}/toggle', [AdminBannerController::class, 'toggle'])->name('banners.toggle');

    // Flash Sale Event Management
    Route::resource('flash-sales', AdminFlashSaleController::class)->except(['show', 'create', 'edit']);
    Route::patch('flash-sales/{flashSale}/toggle', [AdminFlashSaleController::class, 'toggle'])->name('flash-sales.toggle');

    // Discount Vouchers Management
    Route::resource('vouchers', AdminVoucherController::class)->except(['show', 'create', 'edit']);
    Route::patch('vouchers/{voucher}/toggle', [AdminVoucherController::class, 'toggle'])->name('vouchers.toggle');

    // Users & Admin Management
    Route::resource('users', AdminUserController::class);
});

// 7. Public Media & File Routing (Ensures uploaded images are always visible, even without public/storage symlinks)
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/'.$path);
    if (! file_exists($fullPath)) {
        abort(404);
    }

    $mime = mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.file');

Route::get('/products/{path}', function (string $path) {
    $fullPath = storage_path('app/public/products/'.$path);
    if (file_exists($fullPath)) {
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
    abort(404);
})->where('path', '.*')->name('products.file');

