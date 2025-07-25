<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::get("/", fn(): Response => inertia("HomePage"));
Route::get("/login", fn(): Response => inertia("Auth/LoginPage", [
    "notification" => request("notification"),
]));

Route::get("/register", fn(): Response => inertia("Auth/RegisterPage"));
Route::get("/forgot-password", fn(): Response => inertia("Auth/ForgotPasswordPage"));

Route::get("/event/{id}", fn(int $id): Response => inertia("EventPage", [
    "eventId" => $id,
]));

Route::post("/logout", function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect("/");
})->middleware("auth");

Route::get("/EventList", fn() => inertia("EventList"));
