<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Services\HeroSlideImageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as SupportCollection;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageUrl;

class HomeController extends Controller
{
    public function __construct(
        private readonly HeroSlideImageService $heroSlideImageService,
    ) {}

    private const HOMEPAGE_PRODUCT_LIMIT = 4;

    /**
     * Display the storefront homepage.
     */
    public function __invoke(): Response
    {
        return Inertia::render('shop/Home', [
            'heroSlides' => $this->heroSlides(),
            'categories' => $this->categories(),
            'brands' => $this->brands(),
            'bestSellingProducts' => $this->products(
                fn($query) => $query->where('is_best_seller', true)->orderByDesc('sold_count'),
                'Best Seller',
            ),
            'newCollectionProducts' => $this->products(
                fn($query) => $query->where('is_featured', true)->orderByDesc('created_at'),
                'New',
            ),
        ]);
    }


    /**
     * @return list<array{
     *     src: string,
     *     alt: string,
     *     title: string|null,
     *     subtitle: string|null,
     *     buttonText: string|null,
     *     link: string|null
     * }>
     */
    private function heroSlides(): array
    {
        return HeroSlide::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['image', 'title', 'subtitle', 'button_text', 'link'])
            ->map(fn(HeroSlide $slide): array => [
                'src' => $this->heroSlideImageService->resolveUrl($slide->image) ?? '',
                'alt' => $slide->title ?? 'Featured promotion banner',
                'title' => $slide->title,
                'subtitle' => $slide->subtitle,
                'buttonText' => $slide->button_text,
                'link' => $slide->link,
            ])
            ->all();
    }

    /**
     * @return list<array{name: string, img: string, href: string}>
     */
    private function categories(): array
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['name', 'slug', 'image'])
            ->map(fn(Category $category): array => [
                'name' => $category->name,
                'img' => $category->image
                    ? (str_starts_with($category->image, 'http')
                        ? $category->image
                        : Storage::url($category->image))
                    : null,
                'href' => route('shop.index', ['categories' => [$category->slug]]),
            ])
            ->all();
    }

    /**
     * @return list<array{name: string, img: string, href: string}>
     */
    private function brands(): array
    {
        return Brand::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['name', 'slug', 'image'])
            ->map(fn(Brand $brand): array => [
                'name' => $brand->name,
                'img' => $brand->image
                    ? (str_starts_with($brand->image, 'http')
                        ? $brand->image
                        : Storage::url($brand->image))
                    : null,
                'href' => route('shop.index', ['brands' => [$brand->slug]]),
            ])
            ->all();
    }

    /**
     * @param  callable(Builder<Product>): mixed  $scope
     * @return list<array{
     *     name: string,
     *     slug: string,
     *     price: float,
     *     oldPrice: float|null,
     *     img: string,
     *     rating: float,
     *     reviews: int,
     *     inStock: bool,
     *     tag: string
     * }>
     */
    private function products(callable $scope, string $tag): array
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['images' => fn($query) => $query->where('is_primary', true)])
            ->withAvg(['reviews' => fn($query) => $query->where('is_approved', true)], 'rating')
            ->withCount(['reviews' => fn($query) => $query->where('is_approved', true)]);

        $scope($query);

        /** @var Collection<int, Product> $products */
        $products = $query
            ->limit(self::HOMEPAGE_PRODUCT_LIMIT)
            ->get();

        return $this->mapProducts($products, $tag)->all();
    }

    /**
     * @param  Collection<int, Product>  $products
     * @return SupportCollection<int, array{
     *     name: string,
     *     slug: string,
     *     price: float,
     *     oldPrice: float|null,
     *     img: string,
     *     rating: float,
     *     reviews: int,
     *     inStock: bool,
     *     tag: string
     * }>
     */
    private function mapProducts(Collection $products, string $tag): SupportCollection
    {
        return $products->map(function (Product $product) use ($tag): array {
            $primaryImage = $product->images->first();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'oldPrice' => $product->compare_at_price !== null
                    ? (float) $product->compare_at_price
                    : null,
                'img' => ImageUrl::resolve($primaryImage?->image_path),
                'rating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
                'reviews' => (int) $product->reviews_count,
                'inStock' => $product->stock_status === 'in_stock',
                'tag' => $tag,
            ];
        });
    }
}
