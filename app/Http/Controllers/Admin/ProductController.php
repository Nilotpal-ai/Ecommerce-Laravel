<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products with categories and images.
     */
    public function index(Request $request): View
    {
        $products = Product::with(['category.parent', 'images'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::with('parent')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'name'              => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'sku'               => ['required', 'string', 'max:100', 'unique:products,sku'],
            'short_description' => ['nullable', 'string'],
            'description'       => ['nullable', 'string'],
            'price'             => ['required', 'numeric', 'min:0'],
            'sale_price'        => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock_quantity'    => ['required', 'integer', 'min:0'],
            'is_active'         => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'images.*'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        DB::transaction(function () use ($validated, $slug, $request) {
            $product = Product::create([
                'category_id'       => $validated['category_id'],
                'name'              => $validated['name'],
                'slug'              => $slug,
                'sku'               => trim($validated['sku']),
                'short_description' => $validated['short_description'] ?? null,
                'description'       => $validated['description'] ?? null,
                'price'             => $validated['price'],
                'sale_price'        => $validated['sale_price'] ?? null,
                'stock_quantity'    => $validated['stock_quantity'],
                'is_active'         => $request->has('is_active'),
                'is_featured'       => $request->has('is_featured'),
            ]);

            // Handle multi-image upload
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product '{$validated['name']}' created successfully!");
    }

    /**
     * Show the form for editing an existing product.
     */
    public function edit(Product $product): View
    {
        $product->load(['category', 'images']);

        $categories = Category::with('parent')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id),
            ],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'short_description' => ['nullable', 'string'],
            'description'       => ['nullable', 'string'],
            'price'             => ['required', 'numeric', 'min:0'],
            'sale_price'        => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock_quantity'    => ['required', 'integer', 'min:0'],
            'is_active'         => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'images.*'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        DB::transaction(function () use ($product, $validated, $slug, $request) {
            $product->update([
                'category_id'       => $validated['category_id'],
                'name'              => $validated['name'],
                'slug'              => $slug,
                'sku'               => trim($validated['sku']),
                'short_description' => $validated['short_description'] ?? null,
                'description'       => $validated['description'] ?? null,
                'price'             => $validated['price'],
                'sale_price'        => $validated['sale_price'] ?? null,
                'stock_quantity'    => $validated['stock_quantity'],
                'is_active'         => $request->has('is_active'),
                'is_featured'       => $request->has('is_featured'),
            ]);

            // Append newly uploaded images
            if ($request->hasFile('images')) {
                $hasExistingPrimary = $product->images()->where('is_primary', true)->exists();

                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => (!$hasExistingPrimary && $index === 0),
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product '{$product->name}' updated successfully!");
    }

    /**
     * Remove the specified product and its media from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $productName = $product->name;

        DB::transaction(function () use ($product) {
            // Delete associated stored image files from disk
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }

            // Delete database image records
            $product->images()->delete();

            // Delete product row
            $product->delete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product '{$productName}' deleted successfully.");
    }
}