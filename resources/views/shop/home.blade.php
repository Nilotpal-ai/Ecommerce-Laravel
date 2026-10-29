<x-storefront-layout title="Home">
    <!-- Hero Banner -->
    <div class="bg-indigo-700 text-white py-16 px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">
            Welcome to Our Store
        </h1>
        <p class="mt-4 text-lg text-indigo-100 max-w-2xl mx-auto">
            Discover quality products, unbeatable deals, and fast delivery.
        </p>
        <div class="mt-6">
            <a href="{{ route('shop.index') }}" class="inline-block bg-white text-indigo-700 font-semibold px-6 py-3 rounded-md shadow hover:bg-gray-100 transition">
                Browse All Products
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        <!-- Categories Showcase -->
        @if($categories->isNotEmpty())
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Shop by Category</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                        <a href="{{ route('shop.category', $category) }}" 
                           class="p-6 bg-white border border-gray-200 rounded-lg text-center hover:border-indigo-500 hover:shadow-sm transition">
                            <h3 class="font-semibold text-gray-800">{{ $category->name }}</h3>
                            <span class="text-xs text-gray-500 mt-1 block">{{ $category->products_count }} Products</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Featured Products Grid -->
        @if($featuredProducts->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
                    <a href="{{ route('shop.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">View All &rarr;</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition flex flex-col">
                            <a href="{{ route('shop.product.show', $product) }}" class="aspect-square bg-gray-100 block overflow-hidden">
                                @php
                                    $img = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                                @endphp
                                @if($img)
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Image</div>
                                @endif
                            </a>
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-indigo-600 mb-1">
                                        {{ $product->category->name }}
                                    </div>
                                    <h3 class="font-medium text-gray-900 text-sm">
                                        <a href="{{ route('shop.product.show', $product) }}" class="hover:underline">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <div>
                                        @if($product->sale_price)
                                            <span class="text-base font-bold text-gray-900">₹{{ number_format($product->sale_price, 2) }}</span>
                                            <span class="text-xs text-gray-400 line-through ml-1">₹{{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="text-base font-bold text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-green-700 bg-green-50 px-2 py-0.5 rounded font-medium">In Stock</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-storefront-layout>