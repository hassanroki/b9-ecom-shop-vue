<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import ShopLayout from '@/layouts/shop/ShopLayout.vue';
import type { ShopFaqCategory } from '@/types/shop';

defineOptions({
    layout: ShopLayout,
});

const props = defineProps<{
    faqCategories: ShopFaqCategory[];
}>();

const activeCategoryId = ref<number | null>(props.faqCategories[0]?.id ?? null);
const openFaqId = ref<number | null>(null);

const activeCategory = computed(() =>
    props.faqCategories.find((category) => category.id === activeCategoryId.value) ?? null,
);

function selectCategory(id: number): void {
    activeCategoryId.value = id;
    openFaqId.value = null;
}

function toggleFaq(id: number): void {
    openFaqId.value = openFaqId.value === id ? null : id;
}
</script>

<template>

    <Head title="Frequently Asked Questions">
        <meta name="description"
            content="Answers to common questions about orders, delivery, products, and payments." />
    </Head>

    <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 md:text-4xl">
                Frequently Asked Questions
            </h1>
            <p class="mt-2 text-sm text-shop-primary-600 md:text-base">
                Common questions and answers about ShopBD
            </p>
        </div>

        <!-- Empty state -->
        <div v-if="faqCategories.length === 0" class="mt-12 text-center text-sm text-gray-500">
            No FAQs available right now.
        </div>

        <template v-else>
            <!-- Tabs -->
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <button v-for="category in faqCategories" :key="category.id" type="button"
                    class="rounded-lg border px-5 py-2.5 text-sm font-semibold transition-colors" :class="activeCategoryId === category.id
                        ? 'border-shop-accent-500 bg-shop-accent-500 text-white'
                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'"
                    @click="selectCategory(category.id)">
                    {{ category.name }}
                </button>
            </div>

            <!-- Accordion -->
            <div v-if="activeCategory" class="mt-8 flex flex-col gap-3">
                <div v-for="faq in activeCategory.faqs" :key="faq.id"
                    class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left"
                        :aria-expanded="openFaqId === faq.id" @click="toggleFaq(faq.id)">
                        <span class="font-medium text-gray-800">
                            {{ faq.question }}
                        </span>
                        <span
                            class="flex size-6 shrink-0 items-center justify-center rounded-full text-shop-accent-500 transition-transform"
                            :class="{ 'rotate-45': openFaqId === faq.id }">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                    </button>

                    <div v-show="openFaqId === faq.id"
                        class="px-5 pb-4 text-sm leading-relaxed whitespace-pre-line text-gray-600">
                        {{ faq.answer }}
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
