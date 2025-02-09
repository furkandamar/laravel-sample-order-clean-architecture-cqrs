<?php

namespace App\Providers;

use App\Infrastructure\Events\RequestLogEvent;
use App\Infrastructure\Services\EventListenerService;
use App\Models\EventLog;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    protected $listen = [
        RequestLogEvent::class => [
            EventListenerService::class,
        ],
    ];


    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
