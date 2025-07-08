<?php

declare(strict_types=1);

namespace Interns2025b\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Interns2025b\Models\User;
use Interns2025b\Policies\AdminPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => AdminPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
