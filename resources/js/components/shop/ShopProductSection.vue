<script setup lang="ts">
import ShopProductCard from '@/components/shop/ShopProductCard.vue';
import type { ShopProduct } from '@/types/shop';

const {
    id,
    title,
    description,
    products,
    background = 'white',
} = defineProps<{
    id: string;
    title: string;
    description: string;
    products: ShopProduct[];
    background?: 'white' | 'gray';
}>();
</script>

<template>
    <!-- লাক্সারি মিনিমালিস্ট গ্রিড সেকশন -->
    <section
        :id="id"
        class="py-12 transition-colors duration-300 md:py-16 lg:py-20"
        :class="
            background === 'gray'
                ? 'border-y border-neutral-200/60 bg-[#FFF5ED]'
                : 'bg-white'
        "
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- হেডার এরিয়া -->
            <div
                class="mb-10 flex items-end justify-between border-b border-neutral-200/60 pb-5"
            >
                <div class="max-w-xl space-y-1.5">
                    <h2
                        class="text-xl font-black tracking-tight text-neutral-900 uppercase md:text-2xl"
                    >
                        {{ title }}
                    </h2>
                    <p
                        class="text-xs leading-relaxed font-medium text-neutral-500 md:text-sm"
                    >
                        {{ description }}
                    </p>
                </div>

                <!-- আপগ্রেড করা প্রিমিয়াম "View all" অ্যাকশন বাটন -->
                <a
                    href="#"
                    class="group inline-flex items-center gap-1.5 border-b-2 border-transparent pb-1 text-xs font-bold tracking-widest text-neutral-900 uppercase transition-all duration-200 hover:border-[#87E64B]"
                >
                    <span class="transition-colors group-hover:text-neutral-700"
                        >View all</span
                    >
                    <svg
                        class="h-3.5 w-3.5 text-neutral-900 transition-all duration-200 group-hover:translate-x-1 group-hover:text-[#87E64B]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </a>
            </div>

            <!-- প্রোডাক্ট কার্ড গ্রিড লেআউট (মোবাইলে ২টা এবং ডেস্কটপে ৪টা) -->
            <div
                class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 lg:grid-cols-4"
            >
                <ShopProductCard
                    v-for="product in products"
                    :key="product.name"
                    :product="product"
                    class="h-full transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                />
            </div>
        </div>
    </section>
</template>
