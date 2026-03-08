<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Emergency & Database Setup Routes
|--------------------------------------------------------------------------
| المسار المسؤول عن إعداد قاعدة البيانات وتجاوز أخطاء البناء الأولية
*/

Route::get('/final-fix', function () {
    try {
        // 1. إجبار الاتصال على PostgreSQL لهذا الطلب
        config(['database.default' => 'pgsql']);

        // 2. تنظيف شامل وعميق للقاعدة (مسح الجداول والعمليات العالقة تماماً)
        // هذا السطر يحل مشكلة "In failed sql transaction" نهائياً
        DB::statement('DROP SCHEMA public CASCADE; CREATE SCHEMA public;');
        
        // 3. بناء الجداول من الصفر
        Artisan::call('migrate', ['--force' => true]);
        
        // 4. إنشاء مستخدم الإدارة
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
        
        return "✅ مبروك يا مهند! تم تنظيف القاعدة وبناء الجداول بنجاح. المتجر يعمل الآن!";
    } catch (\Exception $e) {
        return "❌ فشل الإعداد: " . $e->getMessage();
    }
})->withoutMiddleware([\App\Http\Middleware\HandleInertiaRequests::class]);

/*
|--------------------------------------------------------------------------
| Customer Routes (Public)
|--------------------------------------------------------------------------
*/

Route::get('/', [CustomerController::class, 'index'])->name('home');

// Checkout routes
Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/finish', [CheckoutController::class, 'paymentFinish'])->name('checkout.finish');
    Route::get('/unfinish', [CheckoutController::class, 'paymentUnfinish'])->name('checkout.unfinish');
    Route::get('/error', [CheckoutController::class, 'paymentError'])->name('checkout.error');
    Route::post('/notification', [CheckoutController::class, 'paymentNotification'])->name('checkout.notification');
});

// Order status check
Route::prefix('order')->group(function () {
    Route::get('/{orderId}/status', [CheckoutController::class, 'orderStatus'])->name('order.status');
    Route::get('/{orderId}/check', [CheckoutController::class, 'checkOrderStatus'])->name('order.check');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/

Route::POST('/print', [PrintController::class, 'index'])->name('print.index');

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('admin/dashboard/index');
    })->name('admin.dashboard');

    // الإدارة الأساسية: الفئات والمنتجات
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);

    // إدارة الطلبات
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('orders/{order}/print', [OrderController::class, 'printReceipt'])->name('orders.print');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';