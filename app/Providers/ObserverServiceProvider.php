<?php

declare(strict_types=1);

namespace Interns2025b\Providers;

use Illuminate\Support\ServiceProvider;
use Interns2025b\Models\Event;
use Interns2025b\Observers\EventObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::observe(EventObserver::class);
    }
}
