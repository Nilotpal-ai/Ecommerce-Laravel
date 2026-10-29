<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('shop.customer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        // Verify ownership
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['items.product.images', 'user']);

        return view('shop.customer.orders.show', compact('order'));
    }
}