<?php

use App\Http\Controllers\ApiCategoriesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AvailableProductController;
use App\Http\Controllers\UserProductController;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', CheckAdmin::class . ':admin,user'])->group(function () {
    // USER CONTROLLER
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::patch('/user/{id}', [UserController::class, 'update'])->name('user.update');

    // AVAILABLE PRODUCTS
    Route::get('/available-products', [AvailableProductController::class, 'index'])->name('available-product.index');
    Route::post('/available-products/{id}/claim', [AvailableProductController::class, 'claimProduct'])->name('available-product.claim');

    // USER PRODUCTS
    Route::get('/your-products', [UserProductController::class, 'index'])->name('user-product.index');

    // BUILT IN PROFILE CONTROLLER FROM LARAVEL BREEZE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', CheckAdmin::class . ':admin'])->group(function () {
  // PRODUCT CONTROLLER
  Route::get('/product', [ProductController::class, 'index'])->name('product.index');
  Route::post('/product', [ProductController::class, 'store'])->name('product.store');
  Route::put('/product/{id}', [ProductController::class, 'update'])->name('product.update');
  Route::post('/product/{id}', [ProductController::class, 'update'])->name('product.update');
  Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

  // CATEGORY CONTROLLER
  Route::get('/categories', [ApiCategoriesController::class, 'index'])->name('categories');
});

Route::post('/switch-api', [ProductController::class, 'switchApi'])->name('switch-api');

require __DIR__.'/auth.php';
