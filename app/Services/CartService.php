<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected const SESSION_KEY = 'cart';

    /**
     * Get the raw cart collection from session.
     */
    public function get(): Collection
    {
        return collect(Session::get(self::SESSION_KEY, []));
    }

    /**
     * Get cart items populated with fresh Product models and resolved pricing.
     */
    public function getDetailedCart(): Collection
    {
        $cart = $this->get();
        if ($cart->isEmpty()) {
            return collect();
        }

        $products = Product::with(['images' => function ($q) {
            $q->where('is_primary', true)->orWhere('sort_order', 0);
        }])
        ->whereIn('id', $cart->pluck('product_id'))
        ->get()
        ->keyBy('id');

        return $cart->map(function ($item) use ($products) {
            $product = $products->get($item['product_id']);

            if (!$product || !$product->is_active) {
                return null;
            }

            $effectivePrice = ($product->sale_price !== null && $product->sale_price < $product->price)
                ? (float) $product->sale_price
                : (float) $product->price;

            $quantity = min((int) $item['quantity'], $product->stock_quantity);

            return [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'thumbnail' => $product->images->first()?->image_path,
                'price' => $effectivePrice,
                'original_price' => (float) $product->price,
                'quantity' => $quantity,
                'stock_quantity' => $product->stock_quantity,
                'line_total' => $effectivePrice * $quantity,
            ];
        })->filter()->values();
    }

    /**
     * Add product to cart with strict stock validation.
     */
    public function add(int $productId, int $quantity = 1): array
    {
        $product = Product::findOrFail($productId);

        if (!$product->is_active || $product->stock_quantity < 1) {
            return ['status' => false, 'message' => 'This product is currently out of stock.'];
        }

        $cart = $this->get();
        $currentQty = (int) ($cart->firstWhere('product_id', $productId)['quantity'] ?? 0);
        $newQty = $currentQty + $quantity;

        if ($newQty > $product->stock_quantity) {
            return [
                'status' => false,
                'message' => "Only {$product->stock_quantity} units available. You already have {$currentQty} in your cart.",
            ];
        }

        if ($cart->has($productId)) {
            $cart->transform(function ($item, $id) use ($productId, $newQty) {
                if ($id == $productId) {
                    $item['quantity'] = $newQty;
                }
                return $item;
            });
        } else {
            $cart->put($productId, [
                'product_id' => $product->id,
                'quantity' => $newQty,
            ]);
        }

        Session::put(self::SESSION_KEY, $cart->toArray());

        return ['status' => true, 'message' => "{$product->name} added to your cart."];
    }

    /**
     * Update quantity for a specific product.
     */
    public function update(int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            $this->remove($productId);
            return ['status' => true, 'message' => 'Item removed from cart.'];
        }

        $product = Product::findOrFail($productId);

        if ($quantity > $product->stock_quantity) {
            return [
                'status' => false,
                'message' => "Cannot update quantity. Only {$product->stock_quantity} units in stock.",
            ];
        }

        $cart = $this->get();
        if ($cart->has($productId)) {
            $cart->transform(function ($item, $id) use ($productId, $quantity) {
                if ($id == $productId) {
                    $item['quantity'] = $quantity;
                }
                return $item;
            });
            Session::put(self::SESSION_KEY, $cart->toArray());
        }

        return ['status' => true, 'message' => 'Cart updated successfully.'];
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(int $productId): void
    {
        $cart = $this->get();
        $cart->forget($productId);
        Session::put(self::SESSION_KEY, $cart->toArray());
    }

    /**
     * Empty entire cart.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Return total unique item count or sum of quantities.
     */
    public function count(): int
    {
        $cart = session()->get('cart', []);
        
        // Sum total quantities across all line items
        return collect($cart)->sum(function ($item) {
            return is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
        });
    }

    /**
     * Calculate cart subtotal.
     */
    public function subtotal(): float
    {
        return (float) $this->getDetailedCart()->sum('line_total');
    }
}