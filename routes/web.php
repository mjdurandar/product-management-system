<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\CheckAdmin;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', CheckAdmin::class . ':admin,user'])->group(function () {
    // USER CONTROLLER
    Route::get('/user', [App\Http\Controllers\UserController::class, 'index'])->name('user.index');
    Route::patch('/user/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');

    // BUILT IN PROFILE CONTROLLER FROM LARAVEL BREEZE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', CheckAdmin::class . ':admin'])->group(function () {
  // PRODUCT CONTROLLER
  Route::get('/product', [ProductController::class, 'index'])->name('product.index');
});

require __DIR__.'/auth.php';
