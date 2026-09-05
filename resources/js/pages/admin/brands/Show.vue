<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { Pencil } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import { edit, index, show } from '@/routes/admin/brands';
import type { AdminBrand } from '@/types/admin';

const props = defineProps<{
    brand: AdminBrand;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
        {
            title: 'Brands',
            href: index(),
        },
        {
            title: props.brand.name,
            href: show(props.brand.id),
        },
    ],
});
</script>

<template>

    <Head :title="brand.name" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading :title="brand.name" description="Brand details" />

            <Button as-child>
                <Link :href="edit(brand.id)">
                    <Pencil class="size-4" />
                    Edit brand
                </Link>
            </Button>
        </div>

        <div class="grid gap-4 lg:grid-cols-[320px_1fr]">
            <Card>
                <CardHeader>
                    <CardTitle>Image</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="aspect-square overflow-hidden rounded-lg border bg-muted">
                        <img v-if="brand.image" :src="brand.image" :alt="brand.name" class="size-full object-cover" />
                        <div v-else class="flex size-full items-center justify-center text-sm text-muted-foreground">
                            No image
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Details</CardTitle>
                    <CardDescription>
                        Overview of this brand
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Slug</p>
                        <p class="font-medium">{{ brand.slug }}</p>
                    </div>

                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Description
                        </p>
                        <p class="font-medium">
                            {{
                                brand.description ||
                                'No description provided.'
                            }}
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">
                                Products
                            </p>
                            <p class="font-medium">
                                {{ brand.products_count }}
                            </p>
                        </div>
                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">
                                Sort order
                            </p>
                            <p class="font-medium">
                                {{ brand.sort_order }}
                            </p>
                        </div>
                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">Status</p>
                            <Badge :variant="brand.is_active
                                ? 'default'
                                : 'secondary'
                                ">
                                {{
                                    brand.is_active ? 'Active' : 'Inactive'
                                }}
                            </Badge>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">
                                Created
                            </p>
                            <p class="font-medium">
                                {{
                                    new Date(
                                        brand.created_at,
                                    ).toLocaleString()
                                }}
                            </p>
                        </div>
                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">
                                Updated
                            </p>
                            <p class="font-medium">
                                {{
                                    new Date(
                                        brand.updated_at,
                                    ).toLocaleString()
                                }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
