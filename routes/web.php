<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AdminAuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login/do', [AdminAuthController::class, 'doLogin'])->middleware('guest');
Route::get('logout', [AdminAuthController::class, 'logout'])->middleware('auth');

Route::get('/', function () {
    $data = [
        'content' => 'admin.dashboard.index',
    ];

    return view('admin.layouts.wrapper', $data);
})->middleware('auth');

Route::prefix('/admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $data = [
            'content' => 'admin.dashboard.index',
        ];

        return view('admin.layouts.wrapper', $data);

    });
    Route::middleware('admin')->group(function () {
        Route::resource('/user', AdminUserController::class);
        Route::resource('/category', CategoryController::class)->except(['show']);
        Route::resource('/product', ProductController::class)->except(['show']);
    });
    Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
    Route::resource('/transaction', TransactionController::class)->except(['edit', 'update']);
});
