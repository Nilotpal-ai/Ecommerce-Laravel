@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-3xl">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <!-- Success Icon -->
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Order Confirmed!</h1>
        <p class="text-gray-600 mb-6">
            Thank you for your order. We have sent an email confirmation to 
            <span class="font-medium text-gray-800">{{ $order->shipping_email }}</span>.
        </p>

        <div class="bg-gray-50 rounded-md p-4 text-left mb-6 border border-gray-200">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 block">Order Number</span>
                    <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Date</span>
                    <span class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Payment Method</span>
                    <span class="font-semibold text-gray-900 uppercase">{{ $order->payment_method }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Payment Status</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Purchased Items Summary -->
        <div class="text-left mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Order Details</h2>
            <div class="divide-y divide-gray-100">
                @foreach ($order->items as $item)
                    <div class="py-3 flex justify-between items-center text-sm">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-gray-500">Qty: {{ $item->quantity }} &times; ₹{{ number_format($item->unit_price, 2) }}</p>
                        </div>
                        <span class="font-semibold text-gray-900">₹{{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t flex justify-between font-bold text-gray-900 text-base">
                <span>Total Amount Paid</span>
                <span>₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Shipping Destination -->
        <div class="text-left bg-gray-50 p-4 rounded-md border border-gray-200 mb-8 text-sm">
            <h3 class="font-semibold text-gray-800 mb-1">Delivering to:</h3>
            <p class="text-gray-700">{{ $order->shipping_name }}</p>
            <p class="text-gray-600">{{ $order->shipping_address_line1 }} {{ $order->shipping_address_line2 }}</p>
            <p class="text-gray-600">{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }} ({{ $order->shipping_country }})</p>
            <p class="text-gray-600">Phone: {{ $order->shipping_phone }}</p>
        </div>

        <div class="flex justify-center gap-4">
            <a href="{{ route('shop.index') }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md shadow transition">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection