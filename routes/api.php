<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Interns2025b\Http\Controllers\EmailVerificationController;
use Interns2025b\Http\Controllers\EventController;
use Interns2025b\Http\Controllers\FacebookController;
use Interns2025b\Http\Controllers\LoginController;
use Interns2025b\Http\Controllers\LogoutController;
use Interns2025b\Http\Controllers\OrganizationController;
use Interns2025b\Http\Controllers\RegisterController;
use Interns2025b\Http\Controllers\ResetPasswordController;

Route::middleware("auth:sanctum")->group(function (): void {
    Route::get("/user", fn(Request $request): JsonResponse => $request->user())->name("user.profile");
    Route::post("/auth/logout", LogoutController::class)->name("logout");
    Route::get("/link/facebook/callback", [FacebookController::class, "linkCallback"]);
});

Route::prefix("auth")->group(function (): void {
    Route::post("/login", [LoginController::class, "login"])->name("login");
    Route::post("/register", [RegisterController::class, "register"])->name("register");

    Route::get("/facebook/redirect", [FacebookController::class, "redirect"]);
    Route::get("/facebook/callback", [FacebookController::class, "loginCallback"]);

    Route::post("/forgot-password", [ResetPasswordController::class, "sendResetLinkEmail"])->name("forgot.password");
    Route::post("/reset-password", [ResetPasswordController::class, "resetPassword"]);

    Route::get("/auth/verify-email/{id}/{hash}", [EmailVerificationController::class, "verify"])->middleware("signed")->name("verification.verify");
});

Route::post("/auth/reset-password", [ResetPasswordController::class, "resetPassword"]);
Route::get("/reset-password/{token}", fn(string $token): JsonResponse => response()->json([
    "message" => "Temporary password reset.",
    "token" => $token,
]))->name("password.reset");

Route::resource("events", EventController::class)->only(["index", "show"]);

Route::prefix("admin")
    ->middleware(["auth:sanctum", "role:administrator|superAdministrator"])
    ->group(function (): void {
        Route::resource("organizations", OrganizationController::class)->only(["index", "show"]);
    });
