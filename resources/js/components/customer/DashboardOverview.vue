<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { formatTaka } from '@/lib/shop/currency';
import { formatDate, getStatusBadgeClass } from '@/lib/customer/orderStatus';

defineProps<{
    user: any;
    orders: Array<any>;
}>();

const emit = defineEmits<{
    (e: 'update:activeTab', tab: string): void;
}>();
</script>

<template>
    <div class="space-y-6">
        <!-- Welcome Banner -->
        <div
    class="relative overflow-hidden rounded-2xl border border-gray-100 bg-[#FEFBFB] p-6 shadow-sm md:p-9"
>
    <div class="relative z-10 flex items-start justify-between gap-6">
        <div class="max-w-xl">
            <div class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-shop-primary-600"></span>
                <p class="text-xs font-semibold tracking-wider text-shop-primary-700 uppercase">
                    Customer Dashboard
                </p>
            </div>
            <h2 class="mt-2 text-2xl font-bold text-black md:text-3xl">
                Welcome back, {{ user.name.split(' ')[0] }}
            </h2>
            <p class="mt-2.5 text-sm leading-relaxed text-[#7F7F88] md:text-base">
                Track your recent orders, manage your profile, and review your
                full purchase history — all in one place.
            </p>
        </div>

        <!-- Avatar / identity badge -->
        <div
            class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-shop-primary-50 text-lg font-bold text-shop-primary-700 ring-1 ring-shop-primary-100 sm:flex"
        >
            {{ user.name.charAt(0).toUpperCase() }}
        </div>
    </div>

    <!-- Decorative accent -->
    <div
        class="pointer-events-none absolute -right-16 -bottom-16 h-56 w-56 rounded-full bg-shop-primary-50 blur-2xl"
    ></div>
</div>

        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div
                class="rounded-2xl border border-[#F0DFCB] bg-[#FFF6EE] p-5 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold tracking-wider text-[#8A7A63] uppercase">
                            Total Orders
                        </p>
                        <h3 class="mt-2 text-3xl font-bold text-[#1E1A34]">
                            {{ orders.length }}
                        </h3>
                    </div>
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-shop-primary-600/10 text-shop-primary-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                <button
                    type="button"
                    @click="emit('update:activeTab', 'orders')"
                    class="mt-4 cursor-pointer text-xs font-semibold text-shop-primary-700 transition hover:text-shop-primary-800"
                >
                    View all orders &rarr;
                </button>
            </div>

            <div
                class="rounded-2xl border border-[#F0DFCB] bg-[#FFF6EE] p-5 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold tracking-wider text-[#8A7A63] uppercase">
                            Recent Order Status
                        </p>
                        <h3 class="mt-2.5">
                            <span v-if="orders.length > 0">
                                <span
                                    :class="getStatusBadgeClass(orders[0].status)"
                                    class="rounded-full px-3 py-1 text-xs font-semibold capitalize"
                                >
                                    {{ orders[0].status }}
                                </span>
                            </span>
                            <span v-else class="text-sm font-medium text-[#8A7A63]">No orders yet</span>
                        </h3>
                    </div>
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-shop-primary-600/10 text-shop-primary-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-4 text-xs text-[#8A7A63]">
                    <span v-if="orders.length > 0">Order #{{ orders[0].order_number }}</span>
                    <span v-else>&mdash;</span>
                </p>
            </div>

            <div
                class="rounded-2xl border border-[#F0DFCB] bg-[#FFF6EE] p-5 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-wider text-[#8A7A63] uppercase">
                            Primary Contact
                        </p>
                        <h3 class="mt-2.5 truncate text-sm font-semibold text-[#1E1A34]">
                            {{ user.email }}
                        </h3>
                        <p class="mt-1.5 text-xs font-medium text-[#8A7A63]">
                            {{ user.phone || 'Phone not set' }}
                        </p>
                    </div>
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-shop-primary-600/10 text-shop-primary-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Snapshot -->
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 bg-[#FFF6EE]/60 px-6 py-4">
                <h3 class="font-bold text-[#1E1A34]">Recent Orders</h3>
                <button
                    type="button"
                    @click="emit('update:activeTab', 'orders')"
                    class="cursor-pointer text-xs font-semibold text-shop-primary-700 transition hover:text-shop-primary-800"
                >
                    View all
                </button>
            </div>
            <div v-if="orders.length > 0" class="divide-y divide-gray-100">
                <div
                    v-for="order in orders.slice(0, 3)"
                    :key="order.id"
                    class="flex flex-col gap-4 px-6 py-4 transition hover:bg-[#FFF6EE]/40 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <p class="text-sm font-bold text-[#1E1A34]">
                            Order #{{ order.order_number }}
                        </p>
                        <p class="mt-0.5 text-xs text-gray-400">
                            Placed on {{ formatDate(order.placed_at) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="font-bold text-[#1E1A34]">{{ formatTaka(order.total) }}</span>
                        <span
                            :class="getStatusBadgeClass(order.status)"
                            class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                        >
                            {{ order.status }}
                        </span>
                    </div>
                </div>
            </div>
            <div v-else class="px-6 py-14 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="mt-4 text-sm font-medium text-gray-500">
                    You haven't placed any orders yet.
                </p>
                <Link
                    href="/shop"
                    class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-shop-primary-700 hover:underline"
                    >Start shopping &rarr;</Link
                >
            </div>
        </div>
    </div>
</template>
