<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Interns2025b\Http\Controllers\EmailVerificationController;
use Interns2025b\Http\Controllers\FacebookController;
use Interns2025b\Http\Controllers\LoginController;
use Interns2025b\Http\Controllers\LogoutController;
use Interns2025b\Http\Controllers\RegisterController;
use Interns2025b\Http\Controllers\ResetPasswordController;
use Interns2025b\Http\Controllers\UpdatePasswordController;
use Interns2025b\Http\Controllers\UserProfileController;

Route::middleware("auth:sanctum")->group(function (): void {
    Route::get("/user", fn(Request $request): JsonResponse => $request->user());
    Route::post("/auth/logout", LogoutController::class);
    Route::get("/link/facebook/callback", [FacebookController::class, "linkCallback"]);
    Route::get("/profile", [UserProfileController::class, "show"]);
    Route::put("/profile", [UserProfileController::class, "update"]);
    Route::put("/auth/change-password", [UpdatePasswordController::class, "updatePassword"]);
});

Route::prefix("auth")->group(function (): void {
    Route::post("/login", [LoginController::class, "login"])->name("login");
    Route::post("/register", [RegisterController::class, "register"]);

    Route::get("/facebook/redirect", [FacebookController::class, "redirect"]);
    Route::get("/facebook/callback", [FacebookController::class, "loginCallback"]);

    Route::post("/forgot-password", [ResetPasswordController::class, "sendResetLinkEmail"]);
    Route::post("/reset-password", [ResetPasswordController::class, "resetPassword"]);

    Route::get("/verify-email/{id}/{hash}", [EmailVerificationController::class, "verify"])
        ->middleware("signed")
        ->name("verification.verify");
});

Route::get("/reset-password/{token}", fn(string $token): JsonResponse => response()->json([
    "message" => "Temporary password reset.",
    "token" => $token,
]))->name("password.reset");
