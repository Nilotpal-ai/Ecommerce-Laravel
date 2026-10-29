<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Listeners\SendOrderConfirmationNotification;
use App\Listeners\SendOrderStatusNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(
            OrderPlaced::class,
            SendOrderConfirmationNotification::class
        );

        Event::listen(
            OrderStatusUpdated::class,
            SendOrderStatusNotification::class
        );
    }
}