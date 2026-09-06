<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function categoryList()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'image'])
            ->map(fn(Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'img' => $category->image
                    ? (str_starts_with($category->image, 'http')
                        ? $category->image
                        : Storage::url($category->image))
                    : null,
                'href' => route('shop.index', ['categories' => [$category->slug]]),
            ]);

        return Inertia::render('shop/Category', [
            'categories' => $categories,
        ]);
    }
}
