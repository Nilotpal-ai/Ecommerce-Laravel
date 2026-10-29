<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CheckoutRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Events\OrderPlaced;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): View|RedirectResponse
    {
        $items = $this->cartService->getDetailedCart();
        $subtotal = $this->cartService->subtotal();

        if (count($items) === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your shopping cart is empty.');
        }

        return view('shop.checkout.index', [
            'items'    => $items,
            'subtotal' => $subtotal,
            'user'     => Auth::user(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $this->cartService->getDetailedCart();
        $subtotal = $this->cartService->subtotal();

        if (count($items) === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your shopping cart is empty.');
        }

        try {
            $order = DB::transaction(function () use ($validated, $items, $subtotal) {
                // Resolve IDs whether items are arrays or Eloquent models
                $productIds = collect($items)->map(function ($item) {
                    return is_array($item) ? ($item['id'] ?? $item['product_id']) : $item->id;
                })->all();

                $lockedProducts = Product::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($items as $item) {
                    $pId = is_array($item) ? ($item['id'] ?? $item['product_id']) : $item->id;
                    $qty = is_array($item) ? $item['quantity'] : $item->quantity;
                    $name = is_array($item) ? ($item['name'] ?? $item['product']['name'] ?? 'Item') : ($item->name ?? $item->product->name ?? 'Item');

                    $product = $lockedProducts->get($pId);

                    if (!$product || !$product->is_active) {
                        throw new Exception("Product \"{$name}\" is no longer available.");
                    }

                    if ($product->stock_quantity < $qty) {
                        throw new Exception("Insufficient stock for \"{$product->name}\". Only {$product->stock_quantity} remaining.");
                    }
                }

                // Create Order
                $order = Order::create([
                    'user_id'                => Auth::id(),
                    'status'                 => 'pending',
                    'payment_status'         => $validated['payment_method'] === 'cod' ? 'unpaid' : 'pending',
                    'payment_method'         => $validated['payment_method'],
                    'subtotal'               => $subtotal,
                    'tax_amount'             => 0.00,
                    'shipping_amount'        => 0.00,
                    'total_amount'           => $subtotal,
                    'shipping_name'          => $validated['shipping_name'],
                    'shipping_email'         => $validated['shipping_email'],
                    'shipping_phone'         => $validated['shipping_phone'],
                    'shipping_address_line1' => $validated['shipping_address_line1'],
                    'shipping_address_line2' => $validated['shipping_address_line2'] ?? null,
                    'shipping_city'          => $validated['shipping_city'],
                    'shipping_state'         => $validated['shipping_state'],
                    'shipping_postal_code'   => $validated['shipping_postal_code'],
                    'shipping_country'       => strtoupper($validated['shipping_country']),
                    'notes'                  => $validated['notes'] ?? null,
                ]);

                // Save order line items and decrement inventory
                foreach ($items as $item) {
                    $pId = is_array($item) ? ($item['id'] ?? $item['product_id']) : $item->id;
                    $qty = is_array($item) ? $item['quantity'] : $item->quantity;
                    $price = is_array($item) ? $item['price'] : $item->price;
                    $product = $lockedProducts->get($pId);

                    $order->items()->create([
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'sku'          => $product->sku,
                        'unit_price'   => $price,
                        'quantity'     => $qty,
                        'subtotal'     => $price * $qty,
                    ]);

                    $product->decrement('stock_quantity', $qty);
                }

                return $order;
            });

            // Clear cart for COD orders immediately
            if ($validated['payment_method'] === 'cod') {
                $this->cartService->clear();
                event(new \App\Events\OrderPlaced($order));
                return redirect()->route('checkout.success', $order->order_number)
                    ->with('success', 'Order placed successfully!');
            }

            // For Stripe: Clear cart and redirect directly to Stripe checkout route
            $this->cartService->clear();

            if ($validated['payment_method'] === 'stripe') {
                return redirect()->route('checkout.payment.stripe', $order->id);
            }

            return redirect()->route('checkout.success', $order->order_number)
                ->with('success', 'Thank you! Your order has been placed.');

        } catch (Exception $e) {
            Log::error('Checkout Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function success(string $orderNumber): View|RedirectResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        return view('shop.checkout.success', compact('order'));
    }
}