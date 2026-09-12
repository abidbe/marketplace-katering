<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Customer\CateringController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Merchant\MenuController;
use App\Http\Controllers\Merchant\OrderController as MerchantOrderController;
use App\Http\Controllers\Merchant\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('file/{file}/preview', [FileController::class, 'preview'])->name('file.preview');
    Route::get('file/{file}/download', [FileController::class, 'download'])->name('file.download');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('invoice/{order}/cetak', [OrderController::class, 'cetak'])->name('invoice.print');
    Route::resource('users', UserController::class)->middleware(['role:admin']);

    Route::middleware('role:merchant')->group(function () {
        Route::get('profile', [ProfileController::class, 'form'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'save'])->name('profile.update');

        Route::resource('menus', MenuController::class)->except(['show']);

        Route::get('order-masuk', [MerchantOrderController::class, 'index'])->name('merchant.orders.index');
        Route::get('order-masuk/{order}', [MerchantOrderController::class, 'show'])->name('merchant.orders.show');
        Route::patch('order-masuk/{order}/status', [MerchantOrderController::class, 'updateStatus'])->name('merchant.orders.status');
    });

    Route::middleware('role:customer')->group(function () {
        Route::get('caterings', [CateringController::class, 'index'])->name('caterings.index');

        Route::get('pesanan', [OrderController::class, 'index'])->name('orders.index');
        Route::get('pesanan/buat', [OrderController::class, 'form'])->name('orders.create');
        Route::post('pesanan', [OrderController::class, 'save'])->name('orders.store');
        Route::get('pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('pesanan/{order}/batalkan', [OrderController::class, 'cancel'])->name('orders.cancel');
    });
});
