@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-gray-200 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Overview</h1>
            <p class="text-sm text-gray-500 mt-1">Real-time revenue, order statistics, and inventory alerts.</p>
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                Manage Orders &rarr;
            </a>
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Delivered Revenue</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">₹{{ number_format($totalRevenue, 2) }}</h3>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">From completed deliveries</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Orders</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalOrders) }}</h3>
                <p class="text-[11px] text-gray-400 mt-1">Lifetime customer orders</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
        </div>

        <!-- Pending Delivery Card -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Awaiting Delivery</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $pendingOrders }}</h3>
                <p class="text-[11px] text-amber-600 font-medium mt-1">Pending / Processing</p>
            </div>
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Stock Warnings Card -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Stock Warnings</p>
                <h3 class="text-2xl font-black {{ $lowStockProducts->count() > 0 ? 'text-rose-600' : 'text-gray-900' }} mt-1">
                    {{ $lowStockProducts->count() }}
                </h3>
                <p class="text-[11px] text-rose-500 font-medium mt-1">&le; 5 units remaining</p>
            </div>
            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Inventory Warnings & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Low Stock Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Low Stock Inventory Alerts</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Products with 5 or fewer items remaining</p>
                </div>
                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[11px] font-bold rounded-full">
                    Threshold: &le; 5
                </span>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">SKU</th>
                            <th class="px-6 py-3">Stock Left</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($lowStockProducts as $prod)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3.5 font-medium text-gray-900 truncate max-w-xs">
                                    {{ $prod->name }}
                                </td>
                                <td class="px-6 py-3.5 text-xs font-mono text-gray-500">
                                    {{ $prod->sku }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-black {{ $prod->stock_quantity == 0 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $prod->stock_quantity }} left
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    @if(Route::has('admin.products.edit'))
                                        <a href="{{ route('admin.products.edit', $prod) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                                            Restock &rarr;
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">Edit</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-xs text-gray-400">
                                    All inventory levels are healthy (&gt; 5 units).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Orders Feed -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Recent Orders</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Latest 5 placed orders</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                    View All &rarr;
                </a>
            </div>
            <div class="divide-y divide-gray-100 flex-1">
                @forelse($recentOrders as $order)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-mono text-sm font-bold text-gray-900">#{{ $order->id }}</span>
                                <span class="text-xs text-gray-500">&bull; {{ $order->shipping_name ?? $order->user->name }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center space-x-3 text-right">
                            <div>
                                <p class="text-sm font-bold text-gray-900">₹{{ number_format($order->total_amount, 2) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <a href="{{ route('admin.orders.show', $order) }}" class="p-1 text-gray-400 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-400">
                        No orders recorded yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection