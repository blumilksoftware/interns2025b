<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Interns2025b\Models\Event;
use Illuminate\Support\Facades\Auth;

Route::get("/", fn(): Response => inertia("HomePage"));
Route::get("/login", fn(): Response => Inertia::render("Auth/LoginPage"))->name("login");
Route::get("/register", fn(): Response => inertia("Auth/RegisterPage"));
Route::get("/forgot-password", fn(): Response => inertia("Auth/ForgotPasswordPage"));

Route::get('/event', fn(): Response => Inertia::render('EventPage'));

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', fn(): Response => Inertia::render('ProfilePage'))->name('profile');
    Route::get('/event/create', fn(): Response => Inertia::render('CreateEventPage'));
    Route::get('/organizations/create', fn(): Response => Inertia::render('CreateOrganizationPage'));

    Route::get('/event/{event}/edit', function (Event $event): Response {
        $user = Auth::user();

        $isAdmin = $user->hasRole('administrator') || $user->hasRole('superAdministrator');
        $isOwner = $event->owner_type === get_class($user) && $event->owner_id === $user->id;

        abort_unless($isAdmin || $isOwner, 403);

        return Inertia::render('EditEventPage', [
            'event' => $event,
            'statusOptions' => \Interns2025b\Enums\EventStatus::cases(),
        ]);
    });
});
