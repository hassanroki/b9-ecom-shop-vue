<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqCategoryRequest;
use App\Http\Requests\Admin\UpdateFaqCategoryRequest;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class FaqCategoryController extends Controller
{
    /**
     * Display a listing of the FAQ categories.
     */
    public function index(): Response
    {
        $faqCategories = FaqCategory::query()
            ->withCount('faqs')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn(FaqCategory $faqCategory): array => $this->faqCategoryPayload($faqCategory));

        return Inertia::render('admin/faq-categories/Index', [
            'faqCategories' => $faqCategories,
        ]);
    }

    /**
     * Show the form for creating a new FAQ category.
     */
    public function create(): Response
    {
        return Inertia::render('admin/faq-categories/Create');
    }

    /**
     * Store a newly created FAQ category in storage.
     */
    public function store(StoreFaqCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['name']);

        FaqCategory::query()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ category created.')]);

        return to_route('admin.faq-categories.index');
    }

    /**
     * Display the specified FAQ category.
     */
    public function show(FaqCategory $faqCategory): Response
    {
        $faqCategory->loadCount('faqs');

        return Inertia::render('admin/faq-categories/Show', [
            'faqCategory' => $this->faqCategoryPayload($faqCategory),
        ]);
    }

    /**
     * Show the form for editing the specified FAQ category.
     */
    public function edit(FaqCategory $faqCategory): Response
    {
        return Inertia::render('admin/faq-categories/Edit', [
            'faqCategory' => $this->faqCategoryPayload($faqCategory),
        ]);
    }

    /**
     * Update the specified FAQ category in storage.
     */
    public function update(UpdateFaqCategoryRequest $request, FaqCategory $faqCategory): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['name'], $faqCategory->id);

        $faqCategory->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ category updated.')]);

        return to_route('admin.faq-categories.index');
    }

    /**
     * Remove the specified FAQ category from storage.
     */
    public function destroy(FaqCategory $faqCategory): RedirectResponse
    {
        $faqCategory->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ category deleted.')]);

        return to_route('admin.faq-categories.index');
    }

    /**
     * Resolve a unique slug — use the given value if present, otherwise
     * generate one from the name, appending a numeric suffix on collision.
     */
    private function resolveSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug(filled($slug) ? $slug : $name);
        $candidate = $base;
        $suffix = 1;

        while (
            FaqCategory::query()
            ->where('slug', $candidate)
            ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
            ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     slug: string,
     *     sort_order: int,
     *     is_active: bool,
     *     faqs_count: int,
     *     created_at: string,
     *     updated_at: string
     * }
     */
    private function faqCategoryPayload(FaqCategory $faqCategory): array
    {
        return [
            'id' => $faqCategory->id,
            'name' => $faqCategory->name,
            'slug' => $faqCategory->slug,
            'sort_order' => $faqCategory->sort_order,
            'is_active' => $faqCategory->is_active,
            'faqs_count' => (int) ($faqCategory->faqs_count ?? 0),
            'created_at' => $faqCategory->created_at?->toIso8601String() ?? '',
            'updated_at' => $faqCategory->updated_at?->toIso8601String() ?? '',
        ];
    }
}
