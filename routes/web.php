<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Interns2025b\Enums\EventStatus;
use Interns2025b\Http\Controllers\FacebookController;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;

Route::get("/", fn(): Response => Inertia::render("HomePage"))->name("home");

Route::middleware("guest")->group(function (): void {
    Route::get("/login", fn(): Response => Inertia::render("Auth/LoginPage"))->name("login");
    Route::get("/register", fn(): Response => Inertia::render("Auth/RegisterPage"));
    Route::get("/forgot-password", fn(): Response => Inertia::render("Auth/ForgotPasswordPage"));
});

Route::get("/events/{id}", fn(int $id) => Inertia::render("EventPage", ["eventId" => $id]));
Route::get("/event", fn(): Response => Inertia::render("EventList"));

Route::middleware(["auth:sanctum"])->group(function (): void {
    Route::middleware(["role:administrator|superAdministrator"])->group(function (): void {
        Route::get("/users/create", fn(): Response => Inertia::render("CreateUserPage"))->name("users.create");

        Route::post("/organizations", function (Request $request) {
            $validated = $request->validate([
                "name" => "required|string|max:255",
                "owner_id" => "nullable|integer|exists:users,id",
                "group_url" => "nullable|string|max:255",
                "avatar_url" => "nullable|string|max:255",
            ]);

            $organization = Organization::create($validated);

            return response()->json(["id" => $organization->id]);
        })->name("organizations.store");
    });

    Route::get("/users/{user}/edit", fn(User $user) => Inertia::render("EditUserPage", ["user" => $user]))
        ->name("users.edit");

    Route::get("/profile", fn(): Response => Inertia::render("ProfilePage"))->name("profile");
    Route::get("/profile/{userId}", fn(int $userId) => Inertia::render("ProfilePage", ["userId" => $userId]))->name("profile.show");
    Route::get("/settings", fn(): Response => Inertia::render("SettingsPage"))->name("settings");
    Route::get("/event/create", fn(): Response => Inertia::render("CreateEventPage"));
    Route::get("/organizations/create", fn(): Response => Inertia::render("CreateOrganizationPage"));

    Route::get("/event/{event}/edit", function (Event $event): Response {
        $user = Auth::user();
        $isAdmin = $user->hasRole("administrator") || $user->hasRole("superAdministrator");
        $isOwner = $event->owner_type === get_class($user) && $event->owner_id === $user->id;
        abort_unless($isAdmin || $isOwner, 403);

        return Inertia::render("EditEventPage", [
            "event" => $event,
            "statusOptions" => EventStatus::cases(),
        ]);
    });

    Route::middleware(["auth:sanctum", "role:administrator|superAdministrator"])->group(function (): void {
        Route::get("/admin", fn(): Response => Inertia::render("AdminPage"))->name("admin.page");
    });

    Route::get("/organizations/{organization}/edit", function (Organization $organization): Response {
        $user = Auth::user();
        $isAdmin = $user->hasRole("administrator") || $user->hasRole("superAdministrator");
        $isOwner = $organization->owner_id === $user->id;
        abort_unless($isAdmin || $isOwner, 403);

        return Inertia::render("EditOrganizationPage", [
            "organization" => $organization,
        ]);
    })->name("organizations.edit");
});
