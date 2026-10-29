@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between pb-6 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
            <p class="text-xs text-gray-500 font-mono mt-1">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900 bg-gray-100 px-3 py-1.5 rounded-md transition">
            &larr; Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-800">
            {{ session('info') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-6">
        <!-- Order Items & Shipping (Left 2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            {{-- Line Items Card --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Items Ordered ({{ $order->items->count() }})</h2>
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item->product_name ?? $item->product->name }}</p>
                                <p class="text-xs text-gray-500">Unit Price: ₹{{ number_format($item->unit_price ?? $item->price, 2) }} &times; {{ $item->quantity }}</p>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                ₹{{ number_format(($item->unit_price ?? $item->price) * $item->quantity, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-200 pt-4 mt-4 flex justify-between items-center text-base font-bold text-gray-900">
                    <span>Total Amount:</span>
                    <span class="text-indigo-600">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            {{-- Shipping Address Card --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-3">Delivery Destination</h2>
                <div class="text-sm text-gray-700 space-y-1">
                    <p class="font-medium text-gray-900">{{ $order->shipping_name }}</p>
                    <p>{{ $order->shipping_address_line1 }}</p>
                    @if($order->shipping_address_line2)
                        <p>{{ $order->shipping_address_line2 }}</p>
                    @endif
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}</p>
                    <p class="text-xs text-gray-500 pt-2 font-mono">Contact: {{ $order->shipping_phone }} | {{ $order->shipping_email }}</p>
                </div>
            </div>
        </div>

        <!-- Admin Control Sidebar (Right 1 col) -->
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Order Status Control</h2>

                <!-- Current State Badge -->
                <div class="mb-6 p-4 rounded-lg bg-gray-50 border border-gray-200">
                    <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold block mb-1">Current Status</span>
                    @if($order->status === 'delivered')
                        <span class="text-lg font-bold text-green-700">✓ Delivered</span>
                    @elseif($order->status === 'cancelled')
                        <span class="text-lg font-bold text-red-600">✕ Cancelled</span>
                    @else
                        <span class="text-lg font-bold text-indigo-600">{{ ucfirst($order->status) }}</span>
                    @endif
                    <div class="text-xs text-gray-500 mt-2">
                        Payment: <strong class="text-gray-800 uppercase">{{ $order->payment_method }} ({{ $order->payment_status }})</strong>
                    </div>
                </div>

                <!-- Action Buttons: Only Two Terminal Options -->
                <div class="space-y-3">
                    {{-- 1. Mark as Delivered --}}
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit" 
                                {{ $order->status === 'delivered' ? 'disabled' : '' }}
                                onclick="return confirm('Mark this order as Delivered?')"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition">
                            Mark as Delivered
                        </button>
                    </form>

                    {{-- 2. Mark as Cancelled (Restocks Stock) --}}
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" 
                                {{ $order->status === 'cancelled' ? 'disabled' : '' }}
                                onclick="return confirm('Cancel this order? All product items will be returned to inventory.')"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 text-sm font-semibold rounded-md text-red-700 bg-white hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition">
                            Cancel Order (Restock)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection