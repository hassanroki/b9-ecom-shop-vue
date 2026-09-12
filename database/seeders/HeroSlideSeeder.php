<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    /**
     * @var list<array{title: string, subtitle: string|null, button_text: string|null, image: string, link: string|null, sort_order: int}>
     */
    private const SLIDES = [
        [
            'title' => 'New Season Arrivals',
            'subtitle' => 'Discover the latest trends for this season',
            'button_text' => 'Shop Now',
            'image' => 'hero-slides/slide1.jpg',
            'link' => '/shop',
            'sort_order' => 1,
        ],
        [
            'title' => 'Smart Living, Smarter Prices',
            'subtitle' => 'Top-rated smart home devices, hand-picked for you',
            'button_text' => 'Explore Bestsellers',
            'image' => 'hero-slides/slide2.jpg',
            'link' => '/shop?sort=best',
            'sort_order' => 2,
        ],
        [
            'title' => 'Fashion Edit',
            'subtitle' => 'Curated styles to elevate your wardrobe',
            'button_text' => 'Shop Fashion',
            'image' => 'hero-slides/slide3.jpg',
            'link' => '/shop?category=fashion',
            'sort_order' => 3,
        ],
    ];

    /**
     * Seed the storefront hero carousel slides.
     */
    public function run(): void
    {
        foreach (self::SLIDES as $slide) {
            HeroSlide::query()->updateOrCreate(
                ['image' => $slide['image']],
                $slide,
            );
        }
    }
}
