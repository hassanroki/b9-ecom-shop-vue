<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\BrandImageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function __construct(
        private readonly BrandImageService $brandImageService,
    ) {}

    /**
     * Display a listing of the brands.
     */
    public function index(): Response
    {
        $brands = Brand::query()
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn(Brand $brand): array => $this->brandPayload($brand));

        return Inertia::render('admin/brands/Index', [
            'brands' => $brands,
        ]);
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create(): Response
    {
        return Inertia::render('admin/brands/Create');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $this->brandImageService->prepareForStorage(
            $request->safe()->except(['image_file']),
            $request->file('image_file'),
        );

        Brand::query()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Brand created.')]);

        return to_route('admin.brands.index');
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand): Response
    {
        $brand->loadCount('products');

        return Inertia::render('admin/brands/Show', [
            'brand' => $this->brandPayload($brand),
        ]);
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand): Response
    {
        return Inertia::render('admin/brands/Edit', [
            'brand' => $this->brandPayload($brand),
        ]);
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $this->brandImageService->prepareForStorage(
            $request->safe()->except(['image_file']),
            $request->file('image_file'),
            $brand,
        );

        $brand->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Brand updated.')]);

        return to_route('admin.brands.index');
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $this->brandImageService->deleteIfUploaded($brand->image);

        $brand->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Brand deleted.')]);

        return to_route('admin.brands.index');
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     slug: string,
     *     image: string|null,
     *     image_source: 'url'|'upload',
     *     image_url: string,
     *     description: string|null,
     *     sort_order: int,
     *     is_active: bool,
     *     products_count: int,
     *     created_at: string,
     *     updated_at: string
     * }
     */
    private function brandPayload(Brand $brand): array
    {
        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            ...$this->brandImageService->payloadForBrand($brand),
            'description' => $brand->description,
            'sort_order' => $brand->sort_order,
            'is_active' => $brand->is_active,
            'products_count' => (int) ($brand->products_count ?? 0),
            'created_at' => $brand->created_at?->toIso8601String() ?? '',
            'updated_at' => $brand->updated_at?->toIso8601String() ?? '',
        ];
    }
}
