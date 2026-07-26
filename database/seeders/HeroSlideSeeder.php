<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    /**
     * @var list<array{image: string, link: string|null, sort_order: int}>
     */
    private const SLIDES = [
        [
            'image' => 'https://images.pexels.com/photos/8886939/pexels-photo-8886939.jpeg',
            'link' => '/shop',
            'sort_order' => 1,
        ],
        [
            'image' => 'https://img.magnific.com/free-photo/rendering-smart-home-device_23-2151039324.jpg?t=st=1784957786~exp=1784961386~hmac=82e55dc90a660b097733d2cf32f5fb841efc20528eab940dfe580b909bd34acc&w=1480',
            'link' => '/shop?sort=best',
            'sort_order' => 2,
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=1920&q=70',
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
