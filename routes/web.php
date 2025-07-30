<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Interns2025b\Models\Event;

Route::get("/", fn(): Response => inertia("HomePage"));
Route::get("/login", fn(): Response => Inertia::render("Auth/LoginPage"))->name("login");

Route::get("/register", fn(): Response => inertia("Auth/RegisterPage"));
Route::get("/forgot-password", fn(): Response => inertia("Auth/ForgotPasswordPage"));

Route::get('/event', fn(): Response => Inertia::render('EventPage'));

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', fn(): Response => Inertia::render('ProfilePage'))->name('profile');
    Route::get('/create-event', fn(): Response => Inertia::render('CreateEventPage'));
    Route::get('/event/{event}/edit', function (Event $event) {
        return Inertia::render('EditEventPage', [
            'event' => $event,
            'statusOptions' => \Interns2025b\Enums\EventStatus::cases(),
        ]);
    });
});
