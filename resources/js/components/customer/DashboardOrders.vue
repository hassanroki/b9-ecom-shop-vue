<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import OrderCard from '@/components/customer/OrderCard.vue';

defineProps<{
    orders: Array<any>;
}>();
</script>

<template>
    <div class="space-y-6">
        <div class="border-gray-150 rounded-2xl border bg-white p-6 shadow-sm">
            <h3 class="border-b border-gray-100 pb-3 text-lg font-bold text-gray-900">
                My Order History
            </h3>

            <div v-if="orders.length > 0" class="mt-4 space-y-4">
                <OrderCard v-for="order in orders" :key="order.id" :order="order" />
            </div>

            <div v-else class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="mt-4 text-sm font-medium text-gray-500">
                    You have no order history yet.
                </p>
                <Link
                    href="/shop"
                    class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-shop-primary-600 hover:underline"
                    >Start shopping &rarr;</Link
                >
            </div>
        </div>
    </div>
</template>
