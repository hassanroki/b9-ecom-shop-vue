<?php

namespace App\Http\Controllers\Storefront\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSuggestionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'categories' => [],
                'products' => [],
            ]);
        }

        // ক্যাটাগরি ফিল্টার
        $categories = Category::query()
            ->where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->take(3)
            ->get(['name', 'slug'])
            ->map(fn($cat) => [
                'name' => $cat->name,
                'slug' => $cat->slug,
                'url'  => route('shop.index', ['categories' => [$cat->slug]]),
            ]);

        // প্রোডাক্ট ফিল্টার
        $products = Product::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%");
            })
            ->with(['images' => fn($q) => $q->where('is_primary', true)])
            ->take(5)
            ->get(['id', 'name', 'slug', 'price'])
            ->map(function ($product) {
                $primaryImage = $product->images->first();

                return [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'slug'  => $product->slug,
                    'price' => (float) $product->price,
                    'img'   => ImageUrl::resolve($primaryImage?->image_path),
                    'url'   => route('shop.index', ['search' => $product->name]), // অথবা আপনার প্রোডাক্ট ডিটেইলস ইউআরএল
                ];
            });

        return response()->json([
            'categories' => $categories,
            'products'   => $products,
        ]);
    }
}
