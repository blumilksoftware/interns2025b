<?php

declare(strict_types=1);

namespace Interns2025b\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Inertia::share([
            "auth.user" => fn() => Auth::user()
                ? [
                    "id" => Auth::id(),
                    "name" => Auth::user()->name,
                    "email" => Auth::user()->email,
                ]
                : null,
        ]);
    }
}
