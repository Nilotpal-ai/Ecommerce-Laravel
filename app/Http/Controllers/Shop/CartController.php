<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): View
    {
        $items = $this->cartService->getDetailedCart();
        $subtotal = $this->cartService->subtotal();

        return view('shop.cart.index', compact('items', 'subtotal'));
    }

    /**
     * Add an item to the cart (Supports both AJAX and standard POST)
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);
        
        // CartService::add returns ['status' => bool, 'message' => string]
        $result = $this->cartService->add((int) $validated['product_id'], $quantity);
        $cartCount = $this->cartService->count();

        // Handle AJAX / Fetch requests
        if ($request->expectsJson() || $request->ajax()) {
            if (!$result['status']) {
                return response()->json([
                    'success'   => false,
                    'message'   => $result['message'],
                    'cartCount' => $cartCount,
                ], 422);
            }

            return response()->json([
                'success'   => true,
                'message'   => $result['message'],
                'cartCount' => $cartCount,
            ]);
        }

        // Fallback for standard synchronous form submissions
        if (!$result['status']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Alias pointing to add() to prevent broken routes if cart.store is used
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        return $this->add($request);
    }

    public function update(Request $request, int $productId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $result = $this->cartService->update($productId, (int) $validated['quantity']);

        if (!$result['status']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('cart.index')->with('success', $result['message']);
    }

    public function destroy(int $productId): RedirectResponse
    {
        $this->cartService->remove($productId);
        return redirect()->route('cart.index')->with('success', 'Item removed from your cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();
        return redirect()->route('cart.index')->with('success', 'Your cart has been cleared.');
    }
}