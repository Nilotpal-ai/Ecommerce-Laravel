@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Shopping Cart</h1>
            <p class="text-sm text-gray-500 mt-1">
                @if (count($items) > 0)
                    You have {{ count($items) }} distinct {{ Str::plural('item', count($items)) }} in your cart.
                @else
                    Your cart is currently empty.
                @endif
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('shop.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Continue Shopping
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (count($items) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Items Table -->
            <div class="lg:col-span-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase tracking-wider text-gray-600">
                                <th class="py-4 px-6">Product</th>
                                <th class="py-4 px-4 text-center">Price</th>
                                <th class="py-4 px-4 text-center">Quantity</th>
                                <th class="py-4 px-4 text-right">Subtotal</th>
                                <th class="py-4 px-4 text-center"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($items as $key => $item)
                                @php
                                    // Handle array or object access cleanly
                                    $productId = is_array($item) ? ($item['id'] ?? $item['product_id'] ?? $key) : ($item->id ?? $key);
                                    $name = is_array($item) ? ($item['name'] ?? $item['product']['name'] ?? 'Product') : ($item->name ?? $item->product->name ?? 'Product');
                                    $sku = is_array($item) ? ($item['sku'] ?? $item['product']['sku'] ?? null) : ($item->sku ?? $item->product->sku ?? null);
                                    $price = is_array($item) ? ($item['price'] ?? 0) : ($item->price ?? 0);
                                    $quantity = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                                    $lineTotal = is_array($item) ? ($item['subtotal'] ?? ($price * $quantity)) : ($item->subtotal ?? ($price * $quantity));
                                    $image = is_array($item) ? ($item['image'] ?? $item['product']['image'] ?? null) : ($item->image ?? $item->product->image ?? null);
                                    $maxStock = is_array($item) ? ($item['stock_quantity'] ?? 99) : ($item->stock_quantity ?? 99);
                                @endphp
                                <tr class="hover:bg-gray-50/60 transition">
                                    <!-- Product Info & Thumbnail -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-4">
                                            @if (!empty($image))
                                                <img src="{{ asset('storage/' . ltrim(str_replace('public/', '', $image), '/')) }}" 
                                                     alt="{{ $name }}" 
                                                     class="w-16 h-16 object-cover rounded-md border border-gray-200 flex-shrink-0">
                                            @else
                                                <div class="w-16 h-16 bg-gray-100 rounded-md border border-gray-200 flex items-center justify-center text-gray-400 text-xs flex-shrink-0">
                                                    No image
                                                </div>
                                            @endif
                                            <div>
                                                <h2 class="text-sm font-semibold text-gray-900 leading-snug">
                                                    {{ $name }}
                                                </h2>
                                                @if (!empty($sku))
                                                    <p class="text-xs text-gray-500 mt-0.5">SKU: {{ $sku }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Unit Price -->
                                    <td class="py-4 px-4 text-center text-sm font-medium text-gray-700 whitespace-nowrap">
                                        ₹{{ number_format($price, 2) }}
                                    </td>

                                    <!-- Quantity Update Form -->
                                    <td class="py-4 px-4 text-center">
                                        <form action="{{ route('cart.update', $productId) }}" method="POST" class="inline-flex items-center justify-center space-x-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="{{ $quantity }}" 
                                                   min="1" 
                                                   max="{{ $maxStock }}" 
                                                   class="w-16 text-center text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                            <button type="submit" 
                                                    title="Update quantity" 
                                                    class="p-1.5 text-gray-500 hover:text-indigo-600 rounded-md hover:bg-gray-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Line Total -->
                                    <td class="py-4 px-4 text-right text-sm font-bold text-gray-900 whitespace-nowrap">
                                        ₹{{ number_format($lineTotal, 2) }}
                                    </td>

                                    <!-- Delete Item Button -->
                                    <td class="py-4 px-4 text-center">
                                        <form action="{{ route('cart.remove', $productId) }}" method="POST" onsubmit="return confirm('Remove this item from your cart?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Remove item" 
                                                    class="text-gray-400 hover:text-red-600 transition p-1 rounded-md hover:bg-red-50">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Clear Cart Footer Action -->
                <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to empty your entire cart?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 transition">
                            Clear Entire Cart
                        </button>
                    </form>
                    <span class="text-xs text-gray-400">Taxes calculated during checkout</span>
                </div>
            </div>

            <!-- Right Column: Sticky Order Summary -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">
                        Order Summary
                    </h2>

                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span class="text-green-600 font-medium">Free</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Estimated Tax</span>
                            <span class="text-gray-500">₹0.00</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 flex justify-between items-baseline text-base font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-xl text-indigo-600">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <div class="mt-6">
                        <a href="{{ route('checkout.index') }}" 
                           class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-md shadow-sm transition">
                            <span>Proceed to Checkout</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>

                    <div class="mt-4 flex items-center justify-center space-x-2 text-xs text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Encrypted & secure checkout</span>
                    </div>
                </div>
            </div>

        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-12 text-center max-w-lg mx-auto">
            <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Your cart is empty</h3>
            <p class="text-sm text-gray-500 mb-6">Looks like you haven't added any items to your shopping cart yet.</p>
            <a href="{{ route('shop.index') }}" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">
                Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection