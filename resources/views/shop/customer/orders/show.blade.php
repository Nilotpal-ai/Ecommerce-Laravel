<x-storefront-layout title="Track Order #{{ $order->id }}">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-gray-200 gap-4">
        <div>
            <a href="{{ route('customer.orders.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                &larr; Back to Order History
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Track Order #{{ $order->id }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div>
            @php
                $status = strtolower($order->status);
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                {{ $status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : ($status === 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <!-- Order Fulfillment Progress Tracker -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-wider">Fulfillment Status</h2>
        
        <div class="relative flex items-center justify-between">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 -z-0"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-indigo-600 transition-all duration-500 -z-0"
                 style="width: {{ $status === 'delivered' ? '100%' : ($status === 'cancelled' ? '0%' : '50%') }}"></div>

            <!-- Placed -->
            <div class="relative flex flex-col items-center bg-white px-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $status !== 'cancelled' ? 'bg-indigo-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                    ✓
                </div>
                <span class="text-xs font-semibold text-gray-900 mt-2">Placed</span>
            </div>

            <!-- Processing -->
            <div class="relative flex flex-col items-center bg-white px-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ in_array($status, ['processing', 'shipped', 'delivered']) ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                    2
                </div>
                <span class="text-xs font-semibold text-gray-900 mt-2">Processing</span>
            </div>

            <!-- Delivered / Completed -->
            <div class="relative flex flex-col items-center bg-white px-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $status === 'delivered' ? 'bg-emerald-600 text-white' : ($status === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                    {{ $status === 'cancelled' ? '✕' : '3' }}
                </div>
                <span class="text-xs font-semibold {{ $status === 'cancelled' ? 'text-rose-600' : 'text-gray-900' }} mt-2">
                    {{ $status === 'cancelled' ? 'Cancelled' : 'Delivered' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Ordered Items Summary -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Items in this Package</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($order->items as $item)
                <div class="p-4 sm:p-6 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                            @if($item->product && $item->product->images->first())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-xs font-mono text-gray-400">IMG</span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ $item->product->name ?? 'Product' }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Qty: {{ $item->quantity }} &times; ₹{{ number_format($item->price, 2) }}</p>
                        </div>
                    </div>
                    <div class="text-right font-bold text-sm text-gray-900">
                        ₹{{ number_format($item->quantity * $item->price, 2) }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-between items-center text-base font-bold text-gray-900">
            <span>Total Paid</span>
            <span>₹{{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>
</div>
</x-storefront-layout>