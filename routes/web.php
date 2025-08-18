<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Interns2025b\Enums\EventStatus;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;

Route::get('/', fn(): Response => Inertia::render('HomePage'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', fn(): Response => Inertia::render('Auth/LoginPage'))->name('login');
    Route::get('/register', fn(): Response => Inertia::render('Auth/RegisterPage'));
    Route::get('/forgot-password', fn(): Response => Inertia::render('Auth/ForgotPasswordPage'));
});

Route::get('/events/{id}', fn(int $id) => Inertia::render('EventPage', ['eventId' => $id]));
Route::get('/event', fn(): Response => Inertia::render('EventList'));

Route::middleware(['auth:sanctum'])->group(function (): void {

    Route::middleware(['role:administrator|superAdministrator'])->group(function (): void {
        Route::get('/users/create', fn(): Response => Inertia::render('CreateUserPage'))->name('users.create');
    });

    Route::get('/users/{user}/edit', fn(User $user) => Inertia::render('EditUserPage', ['user' => $user]))
        ->name('users.edit');

    Route::get('/profile', fn(): Response => Inertia::render('ProfilePage'))->name('profile');
    Route::get('/profile/{userId}', fn(int $userId) => Inertia::render('ProfilePage', ['userId' => $userId]))->name('profile.show');
    Route::get('/settings', fn(): Response => Inertia::render('SettingsPage'))->name('settings');
    Route::get('/event/create', fn(): Response => Inertia::render('CreateEventPage'));
    Route::get('/organizations/create', fn(): Response => Inertia::render('CreateOrganizationPage'));

    Route::get('/event/{event}/edit', function (Event $event): Response {
        $user = Auth::user();
        $isAdmin = $user->hasRole('administrator') || $user->hasRole('superAdministrator');
        $isOwner = $event->owner_type === get_class($user) && $event->owner_id === $user->id;

        abort_unless($isAdmin || $isOwner, 403);

        return Inertia::render('EditEventPage', [
            'event' => $event,
            'statusOptions' => EventStatus::cases(),
        ]);
    });
});
