<script setup lang="ts">
import { AlertTriangle, Heart, Package, ShoppingCart } from '@lucide/vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { AdminDashboardOverview } from '@/types/admin';

defineProps<{
    overview: AdminDashboardOverview;
}>();
</script>

<template>
    <Card class="h-full">
        <CardHeader>
            <CardTitle>Quick stats</CardTitle>
            <CardDescription>Operational snapshot</CardDescription>
        </CardHeader>
        <CardContent class="grid gap-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400"
                    >
                        <ShoppingCart class="size-4" />
                    </div>
                    <div>
                        <p class="text-sm font-medium">Pending orders</p>
                        <p class="text-xs text-muted-foreground">
                            Awaiting fulfillment
                        </p>
                    </div>
                </div>
                <span class="text-lg font-bold">{{
                    overview.pending_orders
                }}</span>
            </div>

            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary"
                    >
                        <Package class="size-4" />
                    </div>
                    <div>
                        <p class="text-sm font-medium">Active products</p>
                        <p class="text-xs text-muted-foreground">
                            {{ overview.total_products }} total listed
                        </p>
                    </div>
                </div>
                <span class="text-lg font-bold">{{
                    overview.active_products
                }}</span>
            </div>

            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-red-500/10 text-red-600 dark:text-red-400"
                    >
                        <AlertTriangle class="size-4" />
                    </div>
                    <div>
                        <p class="text-sm font-medium">Out of stock</p>
                        <p class="text-xs text-muted-foreground">
                            Products needing restock
                        </p>
                    </div>
                </div>
                <span class="text-lg font-bold">{{
                    overview.out_of_stock_products
                }}</span>
            </div>

            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-pink-500/10 text-pink-600 dark:text-pink-400"
                    >
                        <Heart class="size-4" />
                    </div>
                    <div>
                        <p class="text-sm font-medium">Wishlist items</p>
                        <p class="text-xs text-muted-foreground">
                            Customer demand signals
                        </p>
                    </div>
                </div>
                <span class="text-lg font-bold">{{
                    overview.total_wishlists
                }}</span>
            </div>
        </CardContent>
    </Card>
</template>
