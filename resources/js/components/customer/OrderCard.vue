<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { formatTaka } from '@/lib/shop/currency';
import { productShowUrl } from '@/lib/shop/product';
import {
    formatDate,
    getPaymentStatusBadgeClass,
    getStatusBadgeClass,
} from '@/lib/customer/orderStatus';

defineProps<{
    order: any;
}>();

const expanded = ref(false);

function toggleExpand(): void {
    expanded.value = !expanded.value;
}

function resolveImg(path: string | null | undefined): string | null {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
}
</script>

<template>
    <div
        class="border-gray-150 overflow-hidden rounded-xl border bg-white transition hover:border-gray-200 hover:shadow-sm"
    >
        <!-- Order Main Info Row -->
        <div
            class="gap-4 border-b border-gray-100 bg-gray-50/50 p-4 sm:flex sm:items-center sm:justify-between"
        >
            <div
                class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs sm:flex sm:items-center"
            >
                <div>
                    <p
                        class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase"
                    >
                        Order Number
                    </p>
                    <p
                        class="mt-0.5 text-sm font-bold text-gray-700 sm:text-xs"
                    >
                        #{{ order.order_number }}
                    </p>
                </div>
                <div>
                    <p
                        class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase"
                    >
                        Date Placed
                    </p>
                    <p class="mt-0.5 font-medium text-gray-700">
                        {{ formatDate(order.placed_at) }}
                    </p>
                </div>
                <div>
                    <p
                        class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase"
                    >
                        Total Amount
                    </p>
                    <p class="mt-0.5 font-bold text-shop-primary-600">
                        {{ formatTaka(order.total) }}
                    </p>
                </div>
                <div>
                    <p
                        class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase"
                    >
                        Payment Method
                    </p>
                    <p class="mt-0.5 font-medium text-gray-700 capitalize">
                        {{
                            order.payment_method === 'cod'
                                ? 'Cash On Delivery'
                                : order.payment_method
                        }}
                    </p>
                </div>
            </div>

            <div
                class="mt-3 flex items-center justify-between gap-3 sm:mt-0 sm:justify-end"
            >
                <div class="flex items-center gap-2">
                    <span
                        :class="
                            getPaymentStatusBadgeClass(order.payment_status)
                        "
                        class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold capitalize"
                    >
                        Pay: {{ order.payment_status }}
                    </span>
                    <span
                        :class="getStatusBadgeClass(order.status)"
                        class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold capitalize"
                    >
                        {{ order.status }}
                    </span>
                </div>
                <button
                    type="button"
                    @click="toggleExpand"
                    class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-gray-200 p-1.5 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700"
                >
                    <svg
                        class="h-4 w-4 transition-transform duration-200"
                        :class="{ 'rotate-180': expanded }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Expandable Order details -->
        <div v-show="expanded" class="border-t border-gray-50 bg-white p-4">
            <h4
                class="mb-3 text-xs font-bold tracking-wider text-gray-400 uppercase"
            >
                Order Items
            </h4>
            <div class="space-y-3.5">
                <div
                    v-for="item in order.items"
                    :key="item.id"
                    class="flex items-center justify-between gap-4 border-b border-gray-50 pb-3 last:border-b-0 last:pb-0"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-gray-50"
                        >
                            <img
                                v-if="item.img"
                                :src="item.img"
                                :alt="item.product_name"
                                class="h-full w-full object-cover" loading="lazy"
                            />
                            <svg
                                v-else
                                class="h-6 w-6 text-gray-300"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">
                                <Link
                                    v-if="item.product"
                                    :href="productShowUrl(item.product)"
                                    class="hover:text-shop-primary-600 hover:underline"
                                >
                                    {{ item.product_name }}
                                </Link>
                                <span v-else>{{ item.product_name }}</span>
                            </p>
                            <p class="mt-0.5 text-xs text-gray-500">
                                Qty: {{ item.quantity }} &times;
                                {{ formatTaka(item.unit_price) }}
                            </p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-700">{{
                        formatTaka(item.line_total)
                    }}</span>
                </div>
            </div>

            <!-- Delivery Details -->
            <div
                class="mt-4 grid grid-cols-1 gap-4 border-t border-gray-100 pt-4 text-xs text-gray-600 sm:grid-cols-2"
            >
                <div>
                    <p class="mb-1 font-semibold text-gray-700">
                        Shipping Address
                    </p>
                    <p class="font-medium">{{ order.customer_name }}</p>
                    <p>
                        {{ order.address }}, {{ order.area }},
                        {{ order.district }}
                    </p>
                    <p class="mt-0.5 font-medium">Contact: {{ order.phone }}</p>
                </div>
                <div
                    class="justify-end space-y-1.5 sm:flex sm:flex-col sm:items-end"
                >
                    <div class="flex w-full max-w-50 justify-between">
                        <span>Subtotal:</span>
                        <span class="font-bold text-gray-700">{{
                            formatTaka(order.subtotal)
                        }}</span>
                    </div>
                    <div class="flex w-full max-w-50 justify-between">
                        <span>Delivery Charge:</span>
                        <span class="font-bold text-gray-700">{{
                            formatTaka(order.delivery_charge)
                        }}</span>
                    </div>
                    <div
                        v-if="order.discount_amount > 0"
                        class="flex w-full max-w-50 justify-between text-green-600"
                    >
                        <span>Discount:</span>
                        <span class="font-bold"
                            >-{{ formatTaka(order.discount_amount) }}</span
                        >
                    </div>
                    <div
                        class="flex w-full max-w-50 justify-between border-t border-gray-100 pt-1.5 text-sm font-bold"
                    >
                        <span class="text-gray-700">Total:</span>
                        <span class="text-shop-primary-600">{{
                            formatTaka(order.total)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
