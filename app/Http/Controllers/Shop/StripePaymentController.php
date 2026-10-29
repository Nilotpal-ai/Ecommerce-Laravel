<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    /**
     * Create a Stripe Checkout Session and redirect the customer.
     */
    public function checkout(Order $order): RedirectResponse
    {
        $order->load('items');

        if ($order->user_id && Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order->order_number)
                ->with('info', 'This order has already been paid.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'inr',
                    'unit_amount'  => (int) round($item->unit_price * 100),
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                ],
                'quantity'   => $item->quantity,
            ];
        }

        try {
            $session = StripeSession::create([
                'line_items'          => $lineItems,
                'mode'                => 'payment',
                'customer_email'      => $order->shipping_email,
                'client_reference_id' => (string) $order->id,
                'metadata'            => [
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                ],
                'success_url'         => route('checkout.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'          => route('checkout.stripe.cancel', $order->order_number),
            ]);

            $order->update([
                'payment_transaction_id' => $session->id,
            ]);

            return redirect()->away($session->url);

        } catch (Exception $e) {
            Log::error('Stripe Checkout Session creation failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
            ]);

            return redirect()->route('checkout.index')
                ->with('error', 'Stripe session creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Customer returns here upon completing Stripe Checkout.
     */
    public function success(): RedirectResponse
    {
        $sessionId = request()->query('session_id');

        if (!$sessionId) {
            return redirect()->route('shop.index')->with('error', 'Invalid payment session.');
        }

        // 1. Try matching by stored session ID
        $order = Order::where('payment_transaction_id', $sessionId)->first();

        // 2. Fallback: Retrieve Stripe Session directly via API to read client_reference_id
        if (!$order) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $stripeSession = \Stripe\Checkout\Session::retrieve($sessionId);

                $orderId = $stripeSession->client_reference_id ?? $stripeSession->metadata->order_id ?? null;

                if ($orderId) {
                    $order = Order::find($orderId);
                    if ($order) {
                        $order->update([
                            'payment_transaction_id' => $stripeSession->payment_intent ?? $sessionId,
                            'payment_status'         => 'paid',
                            'status'                 => 'processing',
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe Success Verification Error: ' . $e->getMessage());
            }
        }

        if (!$order) {
            return redirect()->route('shop.index')
                ->with('error', 'Order not found for this checkout session.');
        }

        return redirect()->route('checkout.success', $order->order_number)
            ->with('success', 'Payment verified! Thank you for your purchase.');
    }

    /**
     * Customer clicked back/cancel on Stripe Checkout page.
     */
    public function cancel(string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return redirect()->route('checkout.index')
            ->with('error', 'Payment was cancelled for order ' . $order->order_number . '. You may retry placing your order.');
    }
}