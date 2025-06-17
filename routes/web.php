<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::get("/", fn(): Response => inertia("Welcome"));
Route::get("/reset-password/{token}", fn($token) => response()->json([
    "message" => "Temporary password reset.",
    "token" => $token,
]))->name("password.reset");
