<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;


Route::get("/", fn(): Response => inertia("Welcome"));
//Route::get("/login", fn(): Response => inertia("Auth/LoginPage"));
Route::get("/login", function (): Response {
    return inertia("Auth/LoginPage", [
        'notification' => request('notification')
    ]);
});

Route::get("/register", fn(): Response => inertia("Auth/RegisterPage"));
Route::get("/forgotpassword", fn(): Response => inertia("Auth/ForgotPasswordPage"));

