<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * @var list<array{
     *     name: string,
     *     slug: string,
     *     sort_order: int,
     *     faqs: list<array{question: string, answer: string, sort_order: int}>
     * }>
     */
    private const CATEGORIES = [
        [
            'name' => 'Order & Delivery',
            'slug' => 'order-delivery',
            'sort_order' => 1,
            'faqs' => [
                [
                    'question' => 'সারাদেশে কি হোম ডেলিভারি দেওয়া হয়?',
                    'answer' => 'হ্যাঁ, আমরা সারা বাংলাদেশে হোম ডেলিভারি প্রদান করে থাকি।',
                    'sort_order' => 1,
                ],
                [
                    'question' => 'ক্যাশ অন ডেলিভারি (COD) সুবিধা আছে কি?',
                    'answer' => 'হ্যাঁ, ক্যাশ অন ডেলিভারি সুবিধা উপলব্ধ।',
                    'sort_order' => 2,
                ],
                [
                    'question' => 'ডেলিভারি চার্জ কত?',
                    'answer' => 'ঢাকার ভেতরে ৬০ টাকা এবং ঢাকার বাইরে ১২০ টাকা ডেলিভারি চার্জ প্রযোজ্য।',
                    'sort_order' => 3,
                ],
                [
                    'question' => 'কোন কুরিয়ারের মাধ্যমে ডেলিভারি করা হয়?',
                    'answer' => 'আমরা বিশ্বস্ত কুরিয়ার পার্টনারদের মাধ্যমে ডেলিভারি করে থাকি।',
                    'sort_order' => 4,
                ],
            ],
        ],
        [
            'name' => 'Products & Pricing',
            'slug' => 'products-pricing',
            'sort_order' => 2,
            'faqs' => [
                [
                    'question' => 'পণ্যের দাম কি নির্দিষ্ট, নাকি দরদাম করা যায়?',
                    'answer' => 'আমাদের সকল পণ্যের দাম নির্দিষ্ট এবং ওয়েবসাইটে উল্লেখ করা আছে।',
                    'sort_order' => 1,
                ],
            ],
        ],
        [
            'name' => 'Size & Fit',
            'slug' => 'size-fit',
            'sort_order' => 3,
            'faqs' => [
                [
                    'question' => 'সাইজ চার্ট কোথায় পাবো?',
                    'answer' => 'প্রতিটি প্রোডাক্ট পেজে সাইজ চার্ট দেওয়া আছে।',
                    'sort_order' => 1,
                ],
            ],
        ],
        [
            'name' => 'Stores & Brand',
            'slug' => 'stores-brand',
            'sort_order' => 4,
            'faqs' => [
                [
                    'question' => 'আপনাদের কি ফিজিক্যাল স্টোর আছে?',
                    'answer' => 'বর্তমানে আমরা শুধুমাত্র অনলাইনে সেবা প্রদান করছি।',
                    'sort_order' => 1,
                ],
            ],
        ],
        [
            'name' => 'Payment & Returns',
            'slug' => 'payment-returns',
            'sort_order' => 5,
            'faqs' => [
                [
                    'question' => 'রিটার্ন পলিসি কী?',
                    'answer' => 'পণ্য হাতে পাওয়ার ৩ দিনের মধ্যে রিটার্ন করা যাবে, শর্ত প্রযোজ্য।',
                    'sort_order' => 1,
                ],
            ],
        ],
    ];

    /**
     * Seed FAQ categories and their questions.
     */
    public function run(): void
    {
        foreach (self::CATEGORIES as $categoryData) {
            $faqs = $categoryData['faqs'];
            unset($categoryData['faqs']);

            $category = FaqCategory::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData,
            );

            foreach ($faqs as $faq) {
                Faq::query()->updateOrCreate(
                    [
                        'faq_category_id' => $category->id,
                        'question' => $faq['question'],
                    ],
                    [...$faq, 'faq_category_id' => $category->id],
                );
            }
        }
    }
}
