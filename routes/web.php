<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get("/", fn(): Response => inertia("HomePage"));
Route::get("/login", fn(): Response => Inertia::render("Auth/LoginPage"))->name("login");

Route::get("/register", fn(): Response => inertia("Auth/RegisterPage"));
Route::get("/forgot-password", fn(): Response => inertia("Auth/ForgotPasswordPage"));

Route::get("/event", fn(): Response => inertia("EventPage"));

Route::middleware(["auth:sanctum"])->get("/profile", fn(): Response => Inertia::render("ProfilePage"))->name("profile");

Route::get("/create-event", fn(): Response => inertia("CreateEventPage"));
