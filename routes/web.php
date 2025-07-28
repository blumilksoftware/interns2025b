<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get("/", fn(): Response => inertia("HomePage"));
Route::get("/login", fn() => Inertia::render("Auth/LoginPage"))->name("login");

Route::get("/register", fn(): Response => inertia("Auth/RegisterPage"));
Route::get("/forgot-password", fn(): Response => inertia("Auth/ForgotPasswordPage"));

Route::get("/event/{id}", fn(int $id): Response => inertia("EventPage", [
    "eventId" => $id,
]));


Route::get("/EventList", fn() => inertia("EventList"));

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', fn() => Inertia::render('ProfilePage'))->name('profile');
    Route::get('/profile/{userId}', fn(int $userId) => Inertia::render('ProfilePage', ['userId' => $userId]))->name('profile.show');
    Route::get('/settings', function () {return Inertia::render('SettingsPage');})->name('settings');
});
