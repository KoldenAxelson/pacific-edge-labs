<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::active()->with(['category', 'images'])->orderBy('name');

        if ($request->filled('category')) {
            $query->inCategory($request->string('category')->toString());
        }

        if ($request->filled('q')) {
            $query->search($request->string('q')->toString());
        }

        $products   = $query->get();
        $categories = Category::active()->ordered()->get();
        $selected   = $request->input('category', '');

        return view('products.index', compact('products', 'categories', 'selected'));
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->string('q')->toString();

        if (strlen(trim($term)) < 2) {
            return response()->json(['results' => [], 'count' => 0]);
        }

        $products = Product::active()
            ->search($term)
            ->with(['category', 'images'])
            ->limit(20)
            ->get()
            ->map(fn (Product $p) => [
                'name'         => $p->name,
                'category'     => $p->category->name,
                'price'        => $p->formatted_price,
                'originalPrice' => $p->formatted_compare_price,
                'url'          => route('products.show', $p->slug),
                'image'        => $p->primary_image?->url,
                'summary'      => $p->short_description,
            ]);

        return response()->json([
            'results' => $products,
            'count'   => $products->count(),
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('active', true)
            ->with(['category', 'images', 'researchLinks'])
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'images'])
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
