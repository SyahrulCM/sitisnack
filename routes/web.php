<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderManageController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentManageController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => view('dashboard.admin'),
            'pjpu' => view('dashboard.pjpu'),
            'produksi' => view('dashboard.produksi'),
            'penjualan' => view('dashboard.penjualan'),
            'distribusi' => view('dashboard.distribusi'),
            default => view('dashboard.reseller'),
        };
    })->name('dashboard');
});

Route::get('/register', fn () => redirect()->route('login'))->middleware('guest');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

Route::middleware(['auth', 'role:reseller'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin,penjualan'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::patch('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
});

Route::middleware(['auth', 'role:admin,penjualan'])->group(function () {
    Route::get('/manage/orders', [OrderManageController::class, 'index'])->name('manage.orders.index');
    Route::get('/manage/orders/{order}', [OrderManageController::class, 'show'])->name('manage.orders.show');

    Route::patch('/manage/orders/{order}/approve', [OrderManageController::class, 'approve'])->name('manage.orders.approve');
    Route::patch('/manage/orders/{order}/reject', [OrderManageController::class, 'reject'])->name('manage.orders.reject');
});

Route::middleware(['auth', 'role:distribusi'])->group(function () {
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update');

    Route::patch('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.status');
});

Route::middleware(['auth', 'role:reseller'])->group(function () {
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create/{order}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{order}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
});

Route::middleware(['auth', 'role:admin,penjualan'])->group(function () {
    Route::get('/manage/payments', [PaymentManageController::class, 'index'])->name('manage.payments.index');
    Route::get('/manage/payments/{payment}', [PaymentManageController::class, 'show'])->name('manage.payments.show');
    Route::patch('/manage/payments/{payment}/validate', [PaymentManageController::class, 'validatePayment'])->name('manage.payments.validate');
});

Route::middleware(['auth','role:reseller'])->group(function () {
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create/{order}', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns/{order}', [ReturnController::class, 'store'])->name('returns.store');

    Route::get('/returns/{returnRequest}/edit', [ReturnController::class, 'edit'])->name('returns.edit');
    Route::put('/returns/{returnRequest}', [ReturnController::class, 'update'])->name('returns.update');
});

Route::middleware(['auth','role:admin,penjualan'])->group(function () {
    Route::get('/returns-validasi', [ReturnController::class, 'indexValidasi'])->name('returns.validasi.index');
    Route::get('/returns-validasi/{retur}', [ReturnController::class, 'showValidasi'])->name('returns.validasi.show');
    Route::patch('/returns-validasi/{retur}/status', [ReturnController::class, 'updateStatus'])->name('returns.validasi.status');

});








require __DIR__.'/auth.php';

