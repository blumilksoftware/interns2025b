<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Interns2025b\Http\Controllers\EmailVerificationController;
use Interns2025b\Http\Controllers\EventController;
use Interns2025b\Http\Controllers\LoginController;
use Interns2025b\Http\Controllers\LogoutController;
use Interns2025b\Http\Controllers\OrganizationController;
use Interns2025b\Http\Controllers\RegisterController;
use Interns2025b\Http\Controllers\ResetPasswordController;

Route::middleware("auth:sanctum")
    ->group(function (): void {
        Route::get("/user", fn(Request $request): JsonResponse => $request->user())->name("user.profile");
        Route::post("/auth/logout", LogoutController::class)->name("logout");
    });

Route::get("/auth/verify-email/{id}/{hash}", [EmailVerificationController::class, "verify"])
    ->middleware("signed")
    ->name("verification.verify");

Route::post("/auth/login", [LoginController::class, "login"])->name("login");
Route::post("/auth/register", [RegisterController::class, "register"])->name("register");
Route::post("/auth/forgot-password", [ResetPasswordController::class, "sendResetLinkEmail"])->name("forgot-password");
Route::post("/auth/reset-password", [ResetPasswordController::class, "resetPassword"])->name("reset-password");

Route::get("/reset-password/{token}", fn(string $token): JsonResponse => response()->json([
    "message" => "Temporary password reset.",
    "token" => $token,
]))->name("password.reset");

Route::get("/events", [EventController::class, "index"])->name("events.index");
Route::get("/events/{id}", [EventController::class, "show"])->name("events.show");

Route::prefix("admin")
    ->middleware(["auth:sanctum", "role:administrator|superAdministrator"])
    ->group(function (): void {
        Route::get("/organizations", [OrganizationController::class, "index"])->name("organizations.index");
        Route::get("/organizations/{id}", [OrganizationController::class, "show"])->name("organizations.show");
    });
