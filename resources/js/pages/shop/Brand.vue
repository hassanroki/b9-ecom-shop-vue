<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { ShopBrand } from '@/types/shop';
import ShopBrandGrid from '@/components/shop/ShopBrandGrid.vue';

const props = defineProps<{
    brands?: ShopBrand[];
}>();

const brands = computed(() => props.brands ?? []);
</script>

<template>
    <Head title="All Brands">
        <meta name="description" content="Browse all product brands on ShopEase." />
    </Head>

    <div class="border-b border-gray-100 bg-gray-50 px-4 py-10 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">All Brands</h1>
        <p class="mt-2 text-sm text-gray-500">Browse products by brand.</p>
    </div>

    <div v-if="brands.length === 0" class="px-4 py-16 text-center text-sm text-gray-500">
        No brands found.
    </div>

    <ShopBrandGrid v-else :brands="brands" />
</template>
