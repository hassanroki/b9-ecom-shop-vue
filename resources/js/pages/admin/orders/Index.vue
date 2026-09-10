<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Eye,
    Search,
    RotateCcw,
    FilterX,
    Package,
    Clock,
    CheckCircle2,
    Banknote,
    ChevronRight,
} from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent } from '@/components/ui/card';
import { formatTaka } from '@/lib/shop/currency';
import { index, show } from '@/routes/admin/orders';
import type {
    AdminOrderFilters,
    AdminOrderListItem,
    AdminStatusOption,
} from '@/types/admin';

const props = defineProps<{
    orders: AdminOrderListItem[];
    filters: AdminOrderFilters;
    statusOptions: AdminStatusOption[];
    paymentStatusOptions: AdminStatusOption[];
}>();

// Filter form state initialized from props
const filterForm = ref({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    payment_status: props.filters.payment_status ?? '',
});

// Computed active filter check
const hasActiveFilters = computed(() => {
    return Boolean(
        filterForm.value.search ||
        filterForm.value.status ||
        filterForm.value.payment_status,
    );
});

// Execute Inertia search request
function applyFilters() {
    router.get(index(), filterForm.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

// Reset filters to defaults
function handleReset() {
    filterForm.value = { search: '', status: '', payment_status: '' };
    applyFilters();
}

// Watch dropdown changes to apply immediately
watch(
    [() => filterForm.value.status, () => filterForm.value.payment_status],
    () => {
        applyFilters();
    },
);

// Debounce text search to avoid spamming network calls
let searchTimeout: ReturnType<typeof setTimeout>;
watch(
    () => filterForm.value.search,
    () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 350);
    },
);

// Computed Metrics for Quick Summary Cards
const totalRevenue = computed(() => {
    return props.orders.reduce(
        (sum, order) => sum + (Number(order.total) || 0),
        0,
    );
});

const pendingCount = computed(() => {
    return props.orders.filter((o) => o.status === 'pending').length;
});

const deliveredCount = computed(() => {
    return props.orders.filter((o) => o.status === 'delivered').length;
});

// Style Helper functions
function orderStatusConfig(status: AdminOrderListItem['status']) {
    switch (status) {
        case 'delivered':
            return {
                variant: 'default' as const,
                class: 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border-emerald-200 hover:bg-emerald-500/25',
            };
        case 'cancelled':
            return {
                variant: 'destructive' as const,
                class: 'bg-destructive/10 text-destructive border-destructive/20',
            };
        case 'pending':
            return {
                variant: 'secondary' as const,
                class: 'bg-amber-500/15 text-amber-700 dark:text-amber-400 border-amber-200',
            };
        default:
            return { variant: 'outline' as const, class: '' };
    }
}

function paymentStatusConfig(status: AdminOrderListItem['payment_status']) {
    switch (status) {
        case 'paid':
            return {
                class: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200',
            };
        case 'failed':
        case 'cancelled':
            return {
                class: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200',
            };
        default:
            return {
                class: 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-200',
            };
    }
}

function formatDate(value: string | null) {
    if (!value) return '—';
    return new Date(value).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

const selectClass =
    'h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
</script>

<template>
    <Head title="Orders Management" />

    <div
        class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6"
    >
        <!-- Page Title Header -->
        <div
            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Orders"
                description="Monitor customer orders, fulfillment status, and transaction histories."
            />
        </div>

        <!-- Quick Metrics Overview Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card class="shadow-xs transition-all hover:shadow-sm">
                <CardContent
                    class="flex items-center justify-between p-4 sm:p-6"
                >
                    <div class="space-y-1">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Total Shown
                        </p>
                        <p class="text-2xl font-bold tracking-tight">
                            {{ orders.length }}
                        </p>
                    </div>
                    <div class="rounded-xl bg-primary/10 p-2.5 text-primary">
                        <Package class="size-5" />
                    </div>
                </CardContent>
            </Card>

            <Card class="shadow-xs transition-all hover:shadow-sm">
                <CardContent
                    class="flex items-center justify-between p-4 sm:p-6"
                >
                    <div class="space-y-1">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Pending Orders
                        </p>
                        <p
                            class="text-2xl font-bold tracking-tight text-amber-600 dark:text-amber-400"
                        >
                            {{ pendingCount }}
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-amber-500/10 p-2.5 text-amber-600 dark:text-amber-400"
                    >
                        <Clock class="size-5" />
                    </div>
                </CardContent>
            </Card>

            <Card class="shadow-xs transition-all hover:shadow-sm">
                <CardContent
                    class="flex items-center justify-between p-4 sm:p-6"
                >
                    <div class="space-y-1">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Delivered
                        </p>
                        <p
                            class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400"
                        >
                            {{ deliveredCount }}
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-emerald-500/10 p-2.5 text-emerald-600 dark:text-emerald-400"
                    >
                        <CheckCircle2 class="size-5" />
                    </div>
                </CardContent>
            </Card>

            <Card class="shadow-xs transition-all hover:shadow-sm">
                <CardContent
                    class="flex items-center justify-between p-4 sm:p-6"
                >
                    <div class="space-y-1">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Batch Volume
                        </p>
                        <p class="text-2xl font-bold tracking-tight">
                            {{ formatTaka(totalRevenue) }}
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-blue-500/10 p-2.5 text-blue-600 dark:text-blue-400"
                    >
                        <Banknote class="size-5" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Filter Toolbar -->
        <Card class="shadow-xs">
            <CardContent class="p-4">
                <form
                    @submit.prevent="applyFilters"
                    class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_200px_200px_auto]"
                >
                    <!-- Search Input -->
                    <div class="space-y-1.5">
                        <Label for="search" class="text-xs font-medium"
                            >Search Records</Label
                        >
                        <div class="relative">
                            <Search
                                class="absolute top-2.5 left-2.5 size-4 text-muted-foreground"
                            />
                            <Input
                                id="search"
                                v-model="filterForm.search"
                                class="pl-9 text-sm"
                                placeholder="Order #, name, phone, or email..."
                            />
                        </div>
                    </div>

                    <!-- Order Status -->
                    <div class="space-y-1.5">
                        <Label for="status" class="text-xs font-medium"
                            >Order Status</Label
                        >
                        <select
                            id="status"
                            v-model="filterForm.status"
                            :class="selectClass"
                        >
                            <option value="">All Statuses</option>
                            <option
                                v-for="option in statusOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Payment Status -->
                    <div class="space-y-1.5">
                        <Label for="payment_status" class="text-xs font-medium"
                            >Payment Status</Label
                        >
                        <select
                            id="payment_status"
                            v-model="filterForm.payment_status"
                            :class="selectClass"
                        >
                            <option value="">All Payments</option>
                            <option
                                v-for="option in paymentStatusOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="flex items-end">
                        <Button
                            type="button"
                            variant="ghost"
                            class="w-full text-xs sm:w-auto"
                            :disabled="!hasActiveFilters"
                            @click="handleReset"
                        >
                            <RotateCcw class="mr-1.5 size-3.5" />
                            Reset Filters
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <!-- Orders View Section -->
        <div class="space-y-4">
            <!-- 1. Desktop Table View (Hidden on mobile/small screens) -->
            <div
                class="hidden overflow-hidden rounded-xl border bg-card shadow-xs md:block"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="border-b bg-muted/50 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            <tr>
                                <th class="px-5 py-3.5">Order</th>
                                <th class="px-5 py-3.5">Customer</th>
                                <th class="px-5 py-3.5">Items</th>
                                <th class="px-5 py-3.5">Total</th>
                                <th class="px-5 py-3.5">Payment</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Placed At</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="order in orders"
                                :key="order.id"
                                class="group transition-colors hover:bg-muted/30"
                            >
                                <td class="px-5 py-4">
                                    <span
                                        class="font-semibold text-foreground group-hover:text-primary"
                                    >
                                        #{{ order.order_number }}
                                    </span>
                                    <p class="text-xs text-muted-foreground">
                                        {{
                                            order.payment_method === 'cod'
                                                ? 'Cash on Delivery'
                                                : 'SSLCommerz'
                                        }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-medium text-foreground">
                                        {{ order.customer_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ order.phone }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center rounded-md bg-muted px-2 py-1 text-xs font-medium"
                                    >
                                        {{ order.items_count }}
                                        {{
                                            order.items_count === 1
                                                ? 'item'
                                                : 'items'
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-4 font-semibold text-foreground"
                                >
                                    {{ formatTaka(order.total) }}
                                </td>
                                <td class="px-5 py-4">
                                    <Badge
                                        variant="outline"
                                        :class="[
                                            'text-xs font-medium capitalize',
                                            paymentStatusConfig(
                                                order.payment_status,
                                            ).class,
                                        ]"
                                    >
                                        {{ order.payment_status }}
                                    </Badge>
                                </td>
                                <td class="px-5 py-4">
                                    <Badge
                                        :variant="
                                            orderStatusConfig(order.status)
                                                .variant
                                        "
                                        :class="[
                                            'text-xs font-medium capitalize',
                                            orderStatusConfig(order.status)
                                                .class,
                                        ]"
                                    >
                                        {{ order.status }}
                                    </Badge>
                                </td>
                                <td
                                    class="px-5 py-4 text-xs whitespace-nowrap text-muted-foreground"
                                >
                                    {{ formatDate(order.placed_at) }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="sm"
                                        class="h-8"
                                    >
                                        <Link :href="show(order.id)">
                                            <Eye class="mr-1.5 size-3.5" /> View
                                        </Link>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Mobile Card List View (Visible on mobile/small screens) -->
            <div class="grid gap-3 md:hidden">
                <Card
                    v-for="order in orders"
                    :key="order.id"
                    class="overflow-hidden shadow-xs transition-all active:scale-[0.99]"
                >
                    <CardContent class="space-y-3 p-4">
                        <div
                            class="flex items-center justify-between border-b pb-2"
                        >
                            <div>
                                <span class="font-bold text-foreground"
                                    >#{{ order.order_number }}</span
                                >
                                <p class="text-xs text-muted-foreground">
                                    {{ formatDate(order.placed_at) }}
                                </p>
                            </div>
                            <Badge
                                :variant="
                                    orderStatusConfig(order.status).variant
                                "
                                :class="[
                                    'text-xs capitalize',
                                    orderStatusConfig(order.status).class,
                                ]"
                            >
                                {{ order.status }}
                            </Badge>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <p class="font-medium text-foreground">
                                    {{ order.customer_name }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ order.phone }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-foreground">
                                    {{ formatTaka(order.total) }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ order.items_count }} items
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between border-t pt-2 text-xs"
                        >
                            <Badge
                                variant="outline"
                                :class="[
                                    'capitalize',
                                    paymentStatusConfig(order.payment_status)
                                        .class,
                                ]"
                            >
                                {{ order.payment_status }}
                            </Badge>

                            <Button
                                as-child
                                variant="outline"
                                size="sm"
                                class="h-7 text-xs"
                            >
                                <Link :href="show(order.id)">
                                    Details <ChevronRight class="ml-1 size-3" />
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State Component -->
            <Card v-if="orders.length === 0" class="border-dashed shadow-none">
                <CardContent
                    class="flex flex-col items-center justify-center p-12 text-center"
                >
                    <div
                        class="rounded-full bg-muted p-4 text-muted-foreground"
                    >
                        <FilterX class="size-8" />
                    </div>
                    <h3 class="mt-4 text-base font-semibold">
                        No orders found
                    </h3>
                    <p class="mt-1 max-w-sm text-xs text-muted-foreground">
                        We couldn't find any orders matching your selected
                        filters. Try searching for something else or clearing
                        filters.
                    </p>
                    <Button
                        v-if="hasActiveFilters"
                        variant="outline"
                        size="sm"
                        class="mt-4"
                        @click="handleReset"
                    >
                        <RotateCcw class="mr-1.5 size-3.5" />
                        Clear Filters
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
