<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;
use Inertia\Inertia;

Route::get("/", fn(): Response => inertia("HomePage"));
Route::get("/login", fn(): Response => inertia("Auth/LoginPage", [
    "notification" => request("notification"),
]));

Route::get("/register", fn(): Response => inertia("Auth/RegisterPage"));
Route::get("/forgot-password", fn(): Response => inertia("Auth/ForgotPasswordPage"));

Route::middleware('auth:sanctum')->get('/profile', function () {
    return Inertia::render('ProfilePage');
});
