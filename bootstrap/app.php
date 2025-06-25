<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Interns2025b\Http\Middleware\SetLocaleMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . "/../routes/api.php",
        web: __DIR__ . "/../routes/web.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->append(SetLocaleMiddleware::class);
        $middleware->append(StartSession::class);
        $middleware->alias([
            "role" => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })->create();
