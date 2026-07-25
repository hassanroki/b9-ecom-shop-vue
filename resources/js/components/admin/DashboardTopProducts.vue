<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { formatTaka } from '@/lib/shop/currency';
import { index as productsIndex } from '@/routes/admin/products';
import type { AdminDashboardTopProduct } from '@/types/admin';

defineProps<{
    products: AdminDashboardTopProduct[];
}>();
</script>

<template>
    <Card>
        <CardHeader class="flex flex-row items-center justify-between gap-4">
            <div>
                <CardTitle>Top products</CardTitle>
                <CardDescription>By units sold</CardDescription>
            </div>
            <Button variant="outline" size="sm" as-child>
                <Link :href="productsIndex()">View all</Link>
            </Button>
        </CardHeader>
        <CardContent class="grid gap-4">
            <div
                v-for="(product, index) in products"
                :key="product.id"
                class="flex items-center justify-between gap-3"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <span
                        class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted text-xs font-semibold"
                    >
                        {{ index + 1 }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate font-medium">{{ product.name }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ product.category_name }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold">
                        {{ product.sold_count }} sold
                    </p>
                    <p class="text-xs text-muted-foreground">
                        {{ formatTaka(product.price) }}
                    </p>
                </div>
            </div>
            <p
                v-if="products.length === 0"
                class="py-4 text-center text-sm text-muted-foreground"
            >
                No product sales data yet.
            </p>
        </CardContent>
    </Card>
</template>
