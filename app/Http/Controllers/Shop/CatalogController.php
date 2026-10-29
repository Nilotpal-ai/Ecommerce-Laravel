<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Homepage: Featured products & top categories
     */
    public function home(): View
    {
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with(['category', 'images' => fn($q) => $q->orderBy('is_primary', 'desc')])
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->get();

        return view('shop.home', compact('featuredProducts', 'categories'));
    }

    /**
     * Shop Catalog: Search, filter by price/category, and sort
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images'])
            ->where('is_active', true);

        // 1. Text Search (Name, SKU, Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Category Filter (Handles parent + child categories)
        if ($request->filled('category')) {
            $selectedCategory = Category::where('slug', $request->category)
                ->orWhere('id', $request->category)
                ->first();

            if ($selectedCategory) {
                // If it's a parent category, include all subcategory IDs as well
                $categoryIds = Category::where('parent_id', $selectedCategory->id)
                    ->pluck('id')
                    ->push($selectedCategory->id);

                $query->whereIn('category_id', $categoryIds);
            }
        }

        // 3. Price Range Filter
        if ($request->filled('min_price')) {
            $minPrice = (float) $request->min_price;
            $query->where(function ($q) use ($minPrice) {
                $q->where(function ($sub) use ($minPrice) {
                    $sub->whereNotNull('sale_price')->where('sale_price', '>=', $minPrice);
                })->orWhere(function ($sub) use ($minPrice) {
                    $sub->whereNull('sale_price')->where('price', '>=', $minPrice);
                });
            });
        }

        if ($request->filled('max_price')) {
            $maxPrice = (float) $request->max_price;
            $query->where(function ($q) use ($maxPrice) {
                $q->where(function ($sub) use ($maxPrice) {
                    $sub->whereNotNull('sale_price')->where('sale_price', '<=', $maxPrice);
                })->orWhere(function ($sub) use ($maxPrice) {
                    $sub->whereNull('sale_price')->where('price', '<=', $maxPrice);
                });
            });
        }

        // 4. In-Stock Filter
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // 5. Sorting Options
        switch ($request->get('sort')) {
            case 'price_low':
                // Uses effective price: COALESCE(sale_price, price)
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;

            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;

            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        // Paginate and retain all active filter parameters across pages
        $products = $query->paginate(12)->withQueryString();

        // Load root categories with children for sidebar filter tree
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('shop.index', compact('products', 'categories'));
    }

    /**
     * Single category shortcut route.
     */
    public function category(Category $category): View
    {
        return redirect()->route('shop.index', ['category' => $category->slug]);
    }

    /**
     * Product detail view.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'images']);
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}