@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-6 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Add New Product</h1>
            <p class="text-sm text-gray-500 mt-1">Fill in the fields matching your product catalog schema.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" 
           class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition">
            &larr; Back to Products
        </a>
    </div>

    <!-- Error Box -->
    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg text-rose-800 text-sm">
            <p class="font-semibold mb-1">Please fix the following validation errors:</p>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Details, Pricing, Inventory -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Card -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Product Details</h2>

                    <!-- name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-900 mb-1">
                            Product Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}" 
                               required 
                               placeholder="e.g. The Souled Store Black Panther" 
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- slug -->
                    <div>
                        <label for="slug" class="block text-sm font-semibold text-gray-900 mb-1">
                            Slug <span class="text-xs font-normal text-gray-500">(Auto-generated if left blank)</span>
                        </label>
                        <input type="text" 
                               name="slug" 
                               id="slug" 
                               value="{{ old('slug') }}" 
                               placeholder="e.g. the-souled-store-black-panther" 
                               class="w-full text-sm font-mono rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">
                        @error('slug')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- short_description -->
                    <div>
                        <label for="short_description" class="block text-sm font-semibold text-gray-900 mb-1">
                            Short Description
                        </label>
                        <textarea name="short_description" 
                                  id="short_description" 
                                  rows="2" 
                                  placeholder="The Souled Store Black Panther: Men and Boys Short..."
                                  class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">{{ old('short_description') }}</textarea>
                    </div>

                    <!-- description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-900 mb-1">
                            Full Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  placeholder="WAKANDA FOREVER LEGACY: Channel the strength of..."
                                  class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- Pricing & Stock Card -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Pricing & Stock</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- price -->
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-900 mb-1">
                                Price (₹) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" 
                                   name="price" 
                                   id="price" 
                                   step="0.01" 
                                   min="0" 
                                   value="{{ old('price', '1499.00') }}" 
                                   required 
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">
                            @error('price')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- sale_price -->
                        <div>
                            <label for="sale_price" class="block text-sm font-semibold text-gray-900 mb-1">
                                Sale Price (₹) <span class="text-xs font-normal text-gray-500">(Optional)</span>
                            </label>
                            <input type="number" 
                                   name="sale_price" 
                                   id="sale_price" 
                                   step="0.01" 
                                   min="0" 
                                   value="{{ old('sale_price', '1449.00') }}" 
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">
                            @error('sale_price')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- sku -->
                        <div>
                            <label for="sku" class="block text-sm font-semibold text-gray-900 mb-1">
                                SKU <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="sku" 
                                   id="sku" 
                                   value="{{ old('sku') }}" 
                                   required 
                                   placeholder="e.g. Souled: PANTHER-BLK-M" 
                                   class="w-full text-sm font-mono rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">
                            @error('sku')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- stock_quantity -->
                        <div>
                            <label for="stock_quantity" class="block text-sm font-semibold text-gray-900 mb-1">
                                Stock Quantity <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" 
                                   name="stock_quantity" 
                                   id="stock_quantity" 
                                   min="0" 
                                   value="{{ old('stock_quantity', 1) }}" 
                                   required 
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">
                            @error('stock_quantity')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Category, Statuses, Images -->
            <div class="space-y-6">
                <!-- category_id & Flags Card -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Categorization</h2>

                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-900 mb-1">
                            Category <span class="text-rose-500">*</span>
                        </label>
                        <select name="category_id" 
                                id="category_id" 
                                required 
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5 px-3">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->parent ? $cat->parent->name . ' → ' : '' }}{{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- is_active & is_featured -->
                    <div class="pt-2 space-y-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Active (is_active = 1)</span>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="is_featured" 
                                   value="1" 
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Featured (is_featured = 1)</span>
                        </label>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Media Gallery</h2>

                    <div>
                        <label for="images" class="block text-sm font-semibold text-gray-900 mb-1">
                            Upload Images
                        </label>
                        <input type="file" 
                               name="images[]" 
                               id="images" 
                               multiple 
                               accept="image/jpeg,image/png,image/webp" 
                               class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-lg p-2">
                        <p class="text-[11px] text-gray-400 mt-1">Upload multiple photos. The first becomes `is_primary = 1`.</p>
                        @error('images.*')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('admin.products.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                        Save Product
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection