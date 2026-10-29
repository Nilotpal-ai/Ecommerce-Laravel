<?php
namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderStatusUpdatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';
    public int $tries = 3;
    public int $backoff = 60;

    public function handle(OrderStatusUpdated $event): void
    {
        // Suppress notification if status did not actually change
        if ($event->previousStatus === $event->newStatus) {
            return;
        }

        $order = $event->order;
        $recipient = $order->user?->email;

        if ($recipient) {
            Mail::to($recipient)->send(new OrderStatusUpdatedMail($order, $event->newStatus));
        }
    }
}