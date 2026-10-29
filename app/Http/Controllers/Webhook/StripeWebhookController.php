<?php

namespace App\Http\Controllers\Webhook;

use App\Events\OrderPlaced;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming events dispatched by Stripe.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (Exception $e) {
            Log::error('Stripe webhook payload error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
                break;

            case 'payment_intent.succeeded':
                $session = $event->data->object;
                $this->handlePaymentIntentSucceeded($session);
                break;

            case 'checkout.session.expired':
                $session = $event->data->object;
                $this->handleCheckoutSessionExpired($session);
                break;

            default:
                Log::info('Unhandled Stripe event: ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Mark order paid and update status to processing.
     */
    protected function handleCheckoutSessionCompleted($session): void
    {
        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            $order = Order::where('payment_transaction_id', $session->id)->first();
        } else {
            $order = Order::find($orderId);
        }

        if (!$order) {
            Log::error("Stripe Webhook: Order not found for session ID {$session->id}");
            return;
        }

        if ($order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_status'         => 'paid',
            'status'                 => 'processing',
            'payment_transaction_id' => $session->payment_intent ?? $session->id,
        ]);

        event(new OrderPlaced($order));

        Log::info("Order #{$order->order_number} marked as PAID via Stripe Webhook.");
    }

    protected function handlePaymentIntentSucceeded($paymentIntent): void
    {
        // Locate order by matching either the PaymentIntent ID or the stored checkout session
        $order = Order::where('payment_transaction_id', $paymentIntent->id)
            ->orWhere('payment_transaction_id', $paymentIntent->metadata->order_id ?? null)
            ->first();

        if ($order && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
            ]);
            
            \Illuminate\Support\Facades\Log::info("Order #{$order->order_number} marked as PAID via payment_intent.succeeded.");
        }
    }

    /**
     * If user abandons Stripe page and session expires, cancel order and restore stock.
     */
    protected function handleCheckoutSessionExpired($session): void
    {
        $orderId = $session->metadata->order_id ?? null;
        $order   = $orderId ? Order::with('items')->find($orderId) : null;

        if (!$order || $order->payment_status === 'paid' || $order->status === 'cancelled') {
            return;
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update([
                'status'         => 'cancelled',
                'payment_status' => 'failed',
            ]);
        });

        Log::info("Order #{$order->order_number} cancelled and stock replenished due to expired Stripe session.");
    }
}