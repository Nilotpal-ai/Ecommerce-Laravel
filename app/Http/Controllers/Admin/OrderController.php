<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display list of all customer orders with filtering
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Inspect order details
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status: Strictly 'delivered' or 'cancelled'
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        // Enforce only the two allowable terminal states
        $validated = $request->validate([
            'status' => ['required', Rule::in(['delivered', 'cancelled'])],
        ]);

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        // Guard against redundant submissions
        if ($previousStatus === $newStatus) {
            return back()->with('info', "Order is already marked as {$newStatus}.");
        }

        DB::transaction(function () use ($order, $previousStatus, $newStatus) {
            // 1. If transitioning to CANCELLED: Restore inventory
            if ($newStatus === 'cancelled' && $previousStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)
                            ->lockForUpdate()
                            ->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            // 2. If transitioning to DELIVERED: If COD and unpaid, mark paid
            $paymentStatus = $order->payment_status;
            if ($newStatus === 'delivered' && $order->payment_method === 'cod') {
                $paymentStatus = 'paid';
            }

            // 3. Persist status changes
            $order->update([
                'status'         => $newStatus,
                'payment_status' => $paymentStatus,
            ]);
        });

        // Fire notification event if registered
        if (class_exists(OrderStatusUpdated::class)) {
            event(new OrderStatusUpdated($order, $previousStatus, $newStatus));
        }

        return back()->with('success', "Order #{$order->id} successfully updated to " . ucfirst($newStatus) . ".");
    }
}