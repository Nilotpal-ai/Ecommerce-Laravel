<?php

namespace App\Listeners;

use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationNotification
{
    public function handle($event): void
    {
        $order = $event->order;

        // Determine recipient email: fallback to shipping_email if user email is empty
        $recipient = $order->shipping_email ?? $order->user?->email;

        if ($recipient) {
            Mail::to($recipient)->send(new OrderConfirmationMail($order));
        }
    }
}