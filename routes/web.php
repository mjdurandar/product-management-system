<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/add-product', function () {
    return view('add-product');
})->name('products.add');

Route::post('/add-product', [ProductController::class, 'addProduct']);

Route::middleware('auth')->group(function () {
    // USER CONTROLLER
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('user.index');
    Route::patch('/users/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');

    // BUILT IN PROFILE CONTROLLER FROM LARAVEL BREEZE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
