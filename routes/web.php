<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::get("/", fn(): Response => inertia("Welcome"));
Route::get("/login", fn(): Response => inertia("LoginPage"));
Route::get("/register", fn(): Response => inertia("Welcome"));
//
