<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Interns2025b\Http\Controllers\EmailVerificationController;
use Interns2025b\Http\Controllers\EventController;
use Interns2025b\Http\Controllers\EventParticipationController;
use Interns2025b\Http\Controllers\FacebookController;
use Interns2025b\Http\Controllers\FollowController;
use Interns2025b\Http\Controllers\LoginController;
use Interns2025b\Http\Controllers\LogoutController;
use Interns2025b\Http\Controllers\OrganizationController;
use Interns2025b\Http\Controllers\RegisterController;
use Interns2025b\Http\Controllers\ResetPasswordController;
use Interns2025b\Http\Controllers\UpdatePasswordController;
use Interns2025b\Http\Controllers\UserDeletionController;
use Interns2025b\Http\Controllers\UserProfileController;

Route::middleware("auth:sanctum")->group(function (): void {
    Route::get("/user", fn(Request $request): JsonResponse => $request->user())->name("user.profile");
    Route::post("/auth/logout", LogoutController::class)->name("logout");
    Route::get("/link/facebook/callback", [FacebookController::class, "linkCallback"]);
    Route::get("/profile", [UserProfileController::class, "show"]);
    Route::put("/profile", [UserProfileController::class, "update"]);
    Route::put("/auth/change-password", [UpdatePasswordController::class, "updatePassword"]);
    Route::post("/profile/delete-request", [UserDeletionController::class, "requestDelete"]);
    Route::post("/events/{event}/participate", EventParticipationController::class)->name("participate");
    Route::post("/follow/{type}/{id}", FollowController::class)->name("follow");
    Route::get("/followings", [FollowController::class, "followings"])->name("followings");
    Route::get("/followers", [FollowController::class, "followers"])->name("followers");
    Route::get("/profile/{user}", [UserProfileController::class, "showDetail"]);
});

Route::get("/confirm-delete/{user}", [UserDeletionController::class, "confirmDelete"])
    ->middleware("signed")
    ->name("api.confirmDelete");

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
