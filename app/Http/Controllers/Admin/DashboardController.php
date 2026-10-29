<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Total revenue strictly from completed/delivered orders
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');

        // 2. Order summary
        $totalOrders = Order::count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $pendingOrders = Order::whereNotIn('status', ['delivered', 'cancelled'])->count();

        // 3. Low-stock inventory warnings (5 units or fewer)
        $lowStockProducts = Product::where('is_active', true)
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity', 'asc')
            ->take(8)
            ->get();

        // 4. Latest 5 orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'deliveredOrders',
            'pendingOrders',
            'lowStockProducts',
            'recentOrders'
        ));
    }
}
