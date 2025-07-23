<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('HomePage'))
     ->name('home');

Route::get('/login', fn() => Inertia::render('Auth/LoginPage', [
    'notification' => request('notification'),
]))->name('login');

Route::get('/register', fn() => Inertia::render('Auth/RegisterPage'))
     ->name('register');

Route::get('/forgot-password', fn() => Inertia::render('Auth/ForgotPasswordPage'))
     ->name('forgot-password');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', fn() => Inertia::render('ProfilePage'))
         ->name('profile');
});
