<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHeroSlideRequest;
use App\Http\Requests\Admin\UpdateHeroSlideRequest;
use App\Models\HeroSlide;
use App\Services\HeroSlideImageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HeroSlideController extends Controller
{
    public function __construct(
        private readonly HeroSlideImageService $heroSlideImageService,
    ) {}

    /**
     * Display a listing of the hero slides.
     */
    public function index(): Response
    {
        $heroSlides = HeroSlide::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn(HeroSlide $heroSlide): array => $this->heroSlidePayload($heroSlide));

        return Inertia::render('admin/heroslide/Index', [
            'heroSlides' => $heroSlides,
        ]);
    }

    /**
     * Show the form for creating a new hero slide.
     */
    public function create(): Response
    {
        return Inertia::render('admin/heroslide/Create');
    }

    /**
     * Store a newly created hero slide in storage.
     */
    public function store(StoreHeroSlideRequest $request): RedirectResponse
    {
        $data = $this->heroSlideImageService->prepareForStorage(
            $request->safe()->except(['image_file']),
            $request->file('image_file'),
        );

        HeroSlide::query()->create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Hero slide created.')]);

        return to_route('admin.slides.index');
    }

    /**
     * Show the form for editing the specified hero slide.
     */
    public function edit(HeroSlide $heroSlide): Response
    {
        return Inertia::render('admin/heroslide/Edit', [
            'heroSlide' => $this->heroSlidePayload($heroSlide),
        ]);
    }

    /**
     * Update the specified hero slide in storage.
     */
    public function update(UpdateHeroSlideRequest $request, HeroSlide $heroSlide): RedirectResponse
    {
        $data = $this->heroSlideImageService->prepareForStorage(
            $request->safe()->except(['image_file']),
            $request->file('image_file'),
            $heroSlide,
        );

        $heroSlide->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Hero slide updated.')]);

        return to_route('admin.slides.index');
    }

    /**
     * Display the specified hero slide.
     */
    public function show(HeroSlide $heroSlide): Response
    {
        return Inertia::render('admin/heroslide/Show', [
            'heroSlide' => $this->heroSlidePayload($heroSlide),
        ]);
    }

    /**
     * Remove the specified hero slide from storage.
     */
    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        $this->heroSlideImageService->deleteIfUploaded($heroSlide->image);

        $heroSlide->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Hero slide deleted.')]);

        return to_route('admin.slides.index');
    }

    /**
     * @return array{
     *     id: int,
     *     title: string|null,
     *     subtitle: string|null,
     *     button_text: string|null,
     *     image: string|null,
     *     image_source: 'url'|'upload',
     *     image_url: string,
     *     link: string|null,
     *     sort_order: int,
     *     is_active: bool,
     *     created_at: string,
     *     updated_at: string
     * }
     */
    private function heroSlidePayload(HeroSlide $heroSlide): array
    {
        return [
            'id' => $heroSlide->id,
            'title' => $heroSlide->title,
            'subtitle' => $heroSlide->subtitle,
            'button_text' => $heroSlide->button_text,
            ...$this->heroSlideImageService->payloadForHeroSlide($heroSlide),
            'link' => $heroSlide->link,
            'sort_order' => $heroSlide->sort_order,
            'is_active' => $heroSlide->is_active,
            'created_at' => $heroSlide->created_at?->toIso8601String() ?? '',
            'updated_at' => $heroSlide->updated_at?->toIso8601String() ?? '',
        ];
    }
}
