<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Interns2025b\Http\Controllers\AdminManagementController;
use Interns2025b\Http\Controllers\EmailVerificationController;
use Interns2025b\Http\Controllers\EventController;
use Interns2025b\Http\Controllers\FacebookController;
use Interns2025b\Http\Controllers\LoginController;
use Interns2025b\Http\Controllers\LogoutController;
use Interns2025b\Http\Controllers\OrganizationController;
use Interns2025b\Http\Controllers\RegisterController;
use Interns2025b\Http\Controllers\ResetPasswordController;
use Interns2025b\Http\Controllers\UpdatePasswordController;
use Interns2025b\Http\Controllers\UserManagementController;
use Interns2025b\Http\Controllers\UserProfileController;

Route::middleware("auth:sanctum")->group(function (): void {
    Route::get("/user", fn(Request $request): JsonResponse => $request->user())->name("user.profile");
    Route::get("/link/facebook/callback", [FacebookController::class, "linkCallback"]);
    Route::get("/user", fn(Request $request): JsonResponse => $request->user());
    Route::post("/auth/logout", LogoutController::class);
    Route::get("/profile", [UserProfileController::class, "show"]);
    Route::put("/profile", [UserProfileController::class, "update"]);
    Route::put("/auth/change-password", [UpdatePasswordController::class, "updatePassword"]);
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

Route::middleware("auth:sanctum")->group(function (): void {
    Route::post("events", [EventController::class, "store"]);
    Route::put("events/{event}", [EventController::class, "update"]);
    Route::patch("events/{event}", [EventController::class, "update"]);
    Route::delete("events/{event}", [EventController::class, "destroy"]);
});

Route::get("events", [EventController::class, "index"]);
Route::get("events/{event}", [EventController::class, "show"]);

Route::group(["prefix" => "admin",  "middleware" => ["auth:sanctum", "role:administrator|superAdministrator"]], function (): void {
    Route::get("/events", [EventController::class, "index"]);
    Route::get("/organizations", [OrganizationController::class, "index"]);
    Route::get("/organizations/{id}", [OrganizationController::class, "show"]);
    Route::get("/users", [UserManagementController::class, "index"])->name("users.index");
    Route::get("/users/{user}", [UserManagementController::class, "show"])->name("users.show");
    Route::post("/users", [UserManagementController::class, "store"])->name("users.store");
    Route::put("/users/{user}", [UserManagementController::class, "update"])->name("users.update");
    Route::delete("/users/{user}", [UserManagementController::class, "destroy"])->name("users.destroy");
});

Route::group(["prefix" => "superadmin", "middleware" => ["auth:sanctum", "role:superAdministrator"]], function (): void {
    Route::get("/admins", [AdminManagementController::class, "index"])->name("admins.index");
    Route::get("/admins/{admin}", [AdminManagementController::class, "show"])->name("admins.show");
    Route::post("/admins", [AdminManagementController::class, "store"])->name("admins.store");
    Route::put("/admins/{admin}", [AdminManagementController::class, "update"])->name("admins.update");
    Route::delete("/admins/{admin}", [AdminManagementController::class, "destroy"])->name("admins.destroy");
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
