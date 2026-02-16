<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(string $slug): View
    {
        $category = Category::where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        $products = $category->products()
            ->active()
            ->with('images')
            ->orderBy('name')
            ->get();

        $categories = Category::active()->ordered()->get();

        return view('categories.show', compact('category', 'products', 'categories'));
    }
}
