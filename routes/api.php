<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Interns2025b\Http\Controllers\FacebookController;
use Interns2025b\Http\Controllers\LoginController;
use Interns2025b\Http\Controllers\LogoutController;
use Interns2025b\Http\Controllers\RegisterController;

Route::middleware("auth:sanctum")->group(function (): void {
    Route::get("/user", fn(Request $request) => $request->user());
    Route::post("/auth/logout", LogoutController::class);
});

Route::post("/auth/login", [LoginController::class, "login"])->name("login");
Route::post("/auth/register", [RegisterController::class, "register"]);

Route::get("/auth/facebook/redirect", [FacebookController::class, "redirect"]);
Route::get("/auth/facebook/callback", [FacebookController::class, "loginCallback"]);

Route::middleware("auth:sanctum")->get("/link/facebook/callback", [FacebookController::class, "linkCallback"]);
