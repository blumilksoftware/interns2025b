<?php

declare(strict_types=1);
use Interns2025b\Providers\AppServiceProvider;
use Interns2025b\Providers\EventServiceProvider;
use Interns2025b\Providers\ObserverServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    ObserverServiceProvider::class,
];
