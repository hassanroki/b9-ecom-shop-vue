<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Inertia\Inertia;
use Inertia\Response;

class FaqPageController extends Controller
{
    /**
     * Display the storefront FAQ page.
     */
    public function __invoke(): Response
    {
        return Inertia::render('shop/Faq', [
            'faqCategories' => $this->faqCategories(),
        ]);
    }

    /**
     * @return list<array{
     *     id: int,
     *     name: string,
     *     slug: string,
     *     faqs: list<array{id: int, question: string, answer: string}>
     * }>
     */
    private function faqCategories(): array
    {
        return FaqCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['faqs' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->get()
            ->filter(fn(FaqCategory $category) => $category->faqs->isNotEmpty())
            ->map(fn(FaqCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'faqs' => $category->faqs
                    ->map(fn($faq): array => [
                        'id' => $faq->id,
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }
}
