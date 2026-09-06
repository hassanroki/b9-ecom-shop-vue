<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import ShopCategoryGrid from '@/components/shop/ShopCategoryGrid.vue';
import type { ShopCategory } from '@/types/shop';

const props = defineProps<{
    categories?: ShopCategory[];
}>();

const categories = computed(() => props.categories ?? []);
</script>

<template>
    <Head title="All Categories">
        <meta name="description" content="Browse all product categories on ShopEase." />
    </Head>

    <div class="border-b border-gray-100 bg-gray-50 px-4 py-10 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">All Categories</h1>
        <p class="mt-2 text-sm text-gray-500">Browse products by category.</p>
    </div>

    <div v-if="categories.length === 0" class="px-4 py-16 text-center text-sm text-gray-500">
        No categories found.
    </div>

    <ShopCategoryGrid v-else :categories="categories" />
</template>
