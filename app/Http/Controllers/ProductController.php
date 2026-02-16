<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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
