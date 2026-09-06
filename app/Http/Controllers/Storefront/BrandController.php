<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BrandController extends Controller
{
    // Brand List
    public function brandList()
    {
        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'image'])
            ->map(fn(Brand $brand): array => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'img' => $brand->image
                    ? (str_starts_with($brand->image, 'http')
                        ? $brand->image
                        : Storage::url($brand->image))
                    : null,
                'href' => route('shop.index', ['brands' => [$brand->slug]]),
            ]);

        return Inertia::render('shop/Brand', [
            'brands' => $brands,
        ]);
    }
}
