@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Checkout</h1>

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Shipping Information -->
            <div class="lg:col-span-7 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Shipping Details</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="shipping_name" value="{{ old('shipping_name', $user->name ?? '') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('shipping_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="shipping_email" value="{{ old('shipping_email', $user->email ?? '') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('shipping_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="text" name="shipping_phone" value="{{ old('shipping_phone') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('shipping_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                        <input type="text" name="shipping_address_line1" value="{{ old('shipping_address_line1') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('shipping_address_line1') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                        <input type="text" name="shipping_address_line2" value="{{ old('shipping_address_line2') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                            <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('shipping_city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                            <input type="text" name="shipping_state" value="{{ old('shipping_state') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('shipping_state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                            <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('shipping_postal_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <select name="shipping_country" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="IN" {{ old('shipping_country', 'IN') === 'IN' ? 'selected' : '' }}>India (IN)</option>
                            <option value="US" {{ old('shipping_country') === 'US' ? 'selected' : '' }}>United States (US)</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" name="payment_method" value="stripe" {{ old('payment_method', 'stripe') === 'stripe' ? 'checked' : '' }}
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-gray-900 font-medium">Stripe (Credit / Debit Card)</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" name="payment_method" value="cod" {{ old('payment_method') === 'cod' ? 'checked' : '' }}
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-gray-900 font-medium">Cash on Delivery (COD)</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Notes (Optional)</label>
                        <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-5">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Order Summary</h2>

                    <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto mb-4">
                        @foreach ($items as $item)
                            @php
                                $name = is_array($item) ? ($item['name'] ?? $item['product']['name'] ?? 'Product') : ($item->name ?? $item->product->name ?? 'Product');
                                $price = is_array($item) ? ($item['price'] ?? 0) : ($item->price ?? 0);
                                $quantity = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                                $lineTotal = is_array($item) ? ($item['subtotal'] ?? ($price * $quantity)) : ($item->subtotal ?? ($price * $quantity));
                            @endphp
                            <div class="py-3 flex justify-between items-center text-sm">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $name }}</p>
                                    <p class="text-gray-500">Qty: {{ $quantity }} &times; ₹{{ number_format($price, 2) }}</p>
                                </div>
                                <span class="font-semibold text-gray-900">₹{{ number_format($lineTotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 pt-2 border-t text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600 font-medium">Free</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t">
                            <span>Total</span>
                            <span class="text-indigo-600">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="mt-6 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-md shadow transition">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection