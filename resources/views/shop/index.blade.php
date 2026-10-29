<x-storefront-layout title="Shop All Products">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-64 shrink-0">
            <form id="filter-form" action="{{ route('shop.index') }}" method="GET" class="space-y-6">
                <!-- Keep search query if present -->
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <!-- Keep sort selection when submitting filters -->
                <input type="hidden" name="sort" id="filter-sort-input" value="{{ request('sort', 'latest') }}">

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Filters</h2>
                        @if(request()->hasAny(['category', 'min_price', 'max_price', 'in_stock', 'search', 'sort']))
                            <a href="{{ route('shop.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                Clear All
                            </a>
                        @endif
                    </div>

                    <!-- Category Hierarchy Filter -->
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Categories</h3>
                        <div class="space-y-2 text-sm">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="category" value="" 
                                       onchange="document.getElementById('filter-form').submit()"
                                       {{ !request('category') ? 'checked' : '' }}
                                       class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="text-gray-700">All Categories</span>
                            </label>

                            @foreach($categories as $parentCat)
                                <div class="space-y-1">
                                    <label class="flex items-center space-x-2 cursor-pointer font-medium">
                                        <input type="radio" name="category" value="{{ $parentCat->slug }}" 
                                               onchange="document.getElementById('filter-form').submit()"
                                               {{ request('category') === $parentCat->slug ? 'checked' : '' }}
                                               class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <span class="text-gray-900">{{ $parentCat->name }}</span>
                                    </label>

                                    @if($parentCat->children->count() > 0)
                                        <div class="pl-5 space-y-1">
                                            @foreach($parentCat->children as $childCat)
                                                <label class="flex items-center space-x-2 cursor-pointer text-xs">
                                                    <input type="radio" name="category" value="{{ $childCat->slug }}" 
                                                           onchange="document.getElementById('filter-form').submit()"
                                                           {{ request('category') === $childCat->slug ? 'checked' : '' }}
                                                           class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                    <span class="text-gray-600">{{ $childCat->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Price Range (₹)</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                   class="text-xs px-2.5 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                                   class="text-xs px-2.5 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- In-Stock Filter -->
                    <div class="border-t border-gray-100 pt-5">
                        <label class="flex items-center space-x-2 cursor-pointer text-sm">
                            <input type="checkbox" name="in_stock" value="1" 
                                   onchange="document.getElementById('filter-form').submit()"
                                   {{ request('in_stock') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-gray-700 font-medium">In Stock Only</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        Apply Filters
                    </button>
                </div>
            </form>
        </aside>

        <!-- Product Grid Area -->
        <main class="flex-1">
            <!-- Top Bar: Result count & Sort Dropdown -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-bold text-gray-900">{{ $products->total() }}</span> products
                    @if(request('search'))
                        for "<span class="font-semibold text-indigo-600">{{ request('search') }}</span>"
                    @endif
                </div>

                <div class="flex items-center space-x-2">
                    <label for="sort-select" class="text-xs font-medium text-gray-500 whitespace-nowrap">Sort by:</label>
                    <select id="sort-select" 
                            onchange="updateSort(this.value)"
                            class="text-xs border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 py-1.5 pl-3 pr-8">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                        <!-- Image -->
                        <a href="{{ route('shop.product.show', $product->slug) }}" class="block relative aspect-square bg-gray-100 overflow-hidden">
                            @php
                                $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                            @endphp
                            @if($primaryImage)
                                <img src="{{ asset('storage/' . $primaryImage->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            @if($product->sale_price !== null && $product->sale_price < $product->price)
                                <span class="absolute top-2 left-2 bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                    Sale
                                </span>
                            @endif
                        </a>

                        <!-- Product Content -->
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider font-semibold text-indigo-600">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </p>
                                <a href="{{ route('shop.product.show', $product->slug) }}" class="block text-sm font-bold text-gray-900 mt-1 hover:text-indigo-600 transition truncate">
                                    {{ $product->name }}
                                </a>
                                @if($product->short_description)
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $product->short_description }}</p>
                                @endif
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex items-center justify-between mt-4">
                                <div>
                                    @if($product->sale_price !== null && $product->sale_price < $product->price)
                                        <div class="text-sm font-bold text-gray-900">₹{{ number_format($product->sale_price, 2) }}</div>
                                        <div class="text-xs text-gray-400 line-through">₹{{ number_format($product->price, 2) }}</div>
                                    @else
                                        <div class="text-sm font-bold text-gray-900">₹{{ number_format($product->price, 2) }}</div>
                                    @endif
                                </div>

                                <a href="{{ route('shop.product.show', $product->slug) }}" 
                                   class="text-xs font-semibold px-3 py-1.5 bg-gray-900 hover:bg-indigo-600 text-white rounded-lg transition">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 bg-white border border-gray-200 rounded-xl text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-base font-bold text-gray-800">No products found</h3>
                        <p class="text-xs text-gray-500 mt-1">Try adjusting your category, price range, or search terms.</p>
                        <a href="{{ route('shop.index') }}" class="inline-block mt-4 text-xs font-semibold text-indigo-600 hover:underline">
                            Reset All Filters
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </main>
    </div>
</div>

<script>
    function updateSort(val) {
        document.getElementById('filter-sort-input').value = val;
        document.getElementById('filter-form').submit();
    }
</script>
</x-storefront-layout>