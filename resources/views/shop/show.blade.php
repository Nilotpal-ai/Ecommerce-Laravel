<x-storefront-layout :title="$product->name">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Left: Gallery -->
            <div class="space-y-4">
                @php
                    $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                @endphp

                <!-- Main Display Image -->
                <div class="aspect-square bg-white border border-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                    <img id="mainImage" 
                         src="{{ $primary ? asset('storage/' . $primary->image_path) : '' }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover">
                </div>

                <!-- Thumbnails Slider/Row -->
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-5 gap-3">
                        @foreach($product->images as $img)
                            <button type="button" 
                                    onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $img->image_path) }}'"
                                    class="border rounded-md overflow-hidden aspect-square hover:ring-2 hover:ring-indigo-500 focus:outline-none">
                                <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover" alt="Thumbnail">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Details & Add to Cart -->
            <div class="space-y-6">
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-indigo-600">
                        {{ $product->category->name }}
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>
                    <p class="text-xs text-gray-500 mt-1 font-mono">SKU: {{ $product->sku }}</p>
                </div>

                <!-- Pricing -->
                <div class="flex items-baseline space-x-3">
                    @if($product->sale_price)
                        <span class="text-3xl font-bold text-gray-900">₹{{ number_format($product->sale_price, 2) }}</span>
                        <span class="text-lg text-gray-400 line-through">₹{{ number_format($product->price, 2) }}</span>
                        <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded">
                            Save {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                        </span>
                    @else
                        <span class="text-3xl font-bold text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <!-- Short Summary -->
                @if($product->short_description)
                    <p class="text-sm text-gray-600 leading-relaxed border-t border-b border-gray-100 py-4">
                        {{ $product->short_description }}
                    </p>
                @endif

                <!-- Stock & Add to Cart Action -->
                <div class="space-y-4">
                    @if($product->stock_quantity > 0)
                        <div class="text-xs text-green-700 font-medium flex items-center">
                            ✓ In Stock ({{ $product->stock_quantity }} units available)
                        </div>

                        <!-- Form ready for Cart phase -->
                        <form id="add-to-cart-form" action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity ?? 10 }}">
                            
                            <button type="submit" 
                                id="add-to-cart-btn" 
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors duration-150 cursor-pointer">
                            Add to Cart
                        </button>
                        </form>

                        <script>
                        document.querySelectorAll('.add-to-cart-form').forEach(form => {
                            form.addEventListener('submit', async function(e) {
                                e.preventDefault();

                                const submitBtn = this.querySelector('button[type="submit"]');
                                const originalText = submitBtn.innerHTML;
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = 'Adding...';

                                try {
                                    const formData = new FormData(this);
                                    const response = await fetch(this.action, {
                                        method: 'POST',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: formData
                                    });

                                    const data = await response.json();

                                    if (response.ok && data.success) {
                                        // Update badge immediately
                                        window.updateCartBadge(data.cartCount);

                                        // Optional: Dispatch event for other components (drawers, modals)
                                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cartCount } }));
                                    } else {
                                        alert(data.message || 'Could not add product to cart.');
                                    }
                                } catch (error) {
                                    console.error('Add to cart failed:', error);
                                    alert('Something went wrong. Please try again.');
                                } finally {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalText;
                                }
                            });
                        });
                        </script>
                    @else
                        <div class="p-3 bg-red-50 text-red-700 text-sm font-medium rounded-md">
                            Currently Out of Stock
                        </div>
                    @endif
                </div>

                <!-- Full Description -->
                @if($product->description)
                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-sm font-bold text-gray-900 mb-2">Product Description</h2>
                        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $product->description }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-storefront-layout>