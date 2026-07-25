<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { AdminDashboardTopCategory } from '@/types/admin';

defineProps<{
    categories: AdminDashboardTopCategory[];
}>();
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Top categories</CardTitle>
            <CardDescription>By product count</CardDescription>
        </CardHeader>
        <CardContent class="grid gap-3">
            <div
                v-for="category in categories"
                :key="category.id"
                class="flex items-center justify-between gap-3 text-sm"
            >
                <span class="font-medium">{{ category.name }}</span>
                <span class="text-muted-foreground">
                    {{ category.products_count }} products
                </span>
            </div>
            <p
                v-if="categories.length === 0"
                class="py-4 text-center text-sm text-muted-foreground"
            >
                No categories yet.
            </p>
        </CardContent>
    </Card>
</template>
