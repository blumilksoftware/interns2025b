<?php

declare(strict_types=1);

namespace Interns2025b\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Interns2025b\Models\Event;
use Interns2025b\Policies\EventPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Event::class => EventPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
