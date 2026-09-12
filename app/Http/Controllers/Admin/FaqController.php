<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    /**
     * Display a listing of the FAQs.
     */
    public function index(): Response
    {
        $faqs = Faq::query()
            ->with('category')
            ->orderBy('faq_category_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn(Faq $faq): array => $this->faqPayload($faq));

        return Inertia::render('admin/faqs/Index', [
            'faqs' => $faqs,
        ]);
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create(): Response
    {
        return Inertia::render('admin/faqs/Create', [
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(StoreFaqRequest $request): RedirectResponse
    {
        Faq::query()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ created.')]);

        return to_route('admin.faqs.index');
    }

    /**
     * Display the specified FAQ.
     */
    public function show(Faq $faq): Response
    {
        $faq->load('category');

        return Inertia::render('admin/faqs/Show', [
            'faq' => $this->faqPayload($faq),
        ]);
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(Faq $faq): Response
    {
        return Inertia::render('admin/faqs/Edit', [
            'faq' => $this->faqPayload($faq),
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ updated.')]);

        return to_route('admin.faqs.index');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ deleted.')]);

        return to_route('admin.faqs.index');
    }

    /**
     * @return array{
     *     id: int,
     *     faq_category_id: int,
     *     category: array{id: int, name: string}|null,
     *     question: string,
     *     answer: string,
     *     sort_order: int,
     *     is_active: bool,
     *     created_at: string,
     *     updated_at: string
     * }
     */
    private function faqPayload(Faq $faq): array
    {
        return [
            'id' => $faq->id,
            'faq_category_id' => $faq->faq_category_id,
            'category' => $faq->category ? [
                'id' => $faq->category->id,
                'name' => $faq->category->name,
            ] : null,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'sort_order' => $faq->sort_order,
            'is_active' => $faq->is_active,
            'created_at' => $faq->created_at?->toIso8601String() ?? '',
            'updated_at' => $faq->updated_at?->toIso8601String() ?? '',
        ];
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    private function categoryOptions(): array
    {
        return FaqCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn(FaqCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
            ])
            ->all();
    }
}
