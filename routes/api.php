<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Interns2025b\Http\Controllers\EmailVerificationController;
use Interns2025b\Http\Controllers\EventController;
use Interns2025b\Http\Controllers\EventParticipationController;
use Interns2025b\Http\Controllers\FollowController;
use Interns2025b\Http\Controllers\LoginController;
use Interns2025b\Http\Controllers\LogoutController;
use Interns2025b\Http\Controllers\OrganizationController;
use Interns2025b\Http\Controllers\RegisterController;
use Interns2025b\Http\Controllers\ResetPasswordController;

Route::middleware("auth:sanctum")->group(function (): void {
    Route::post("/auth/logout", LogoutController::class);
    Route::post("/events/{event}/participate", EventParticipationController::class)->name("participate");
    Route::post("/follow/{type}/{id}", FollowController::class)->name("follow");
    Route::get("/followings", [FollowController::class, "followings"])->name("followings");
    Route::get("/followers", [FollowController::class, "followers"])->name("followers");
});

Route::get("/auth/verify-email/{id}/{hash}", [EmailVerificationController::class, "verify"])
    ->middleware("signed")
    ->name("verification.verify");

Route::post("/auth/login", [LoginController::class, "login"])->name("login");
Route::post("/auth/register", [RegisterController::class, "register"]);

Route::post("/auth/forgot-password", [ResetPasswordController::class, "sendResetLinkEmail"]);
Route::post("/auth/reset-password", [ResetPasswordController::class, "resetPassword"]);

Route::get("/reset-password/{token}", fn(string $token): JsonResponse => response()->json([
    "message" => "Temporary password reset.",
    "token" => $token,
]))->name("password.reset");

Route::group(["prefix" => "admin",  "middleware" => ["auth:sanctum", "role:administrator|superAdministrator"]], function (): void {
    Route::get("/events", [EventController::class, "index"]);
    Route::get("/organizations", [OrganizationController::class, "index"]);
    Route::get("/organizations/{id}", [OrganizationController::class, "show"]);
});
