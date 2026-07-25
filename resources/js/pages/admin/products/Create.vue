<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
import ProductForm from '@/components/admin/ProductForm.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { serializeProductImagesForSubmit } from '@/lib/admin/productImages';
import { dashboard } from '@/routes';
import { create, index } from '@/routes/admin/products';
import type { AdminCategoryOption, ProductFormData } from '@/types/admin';

defineProps<{
    categories: AdminCategoryOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Products', href: index() },
            { title: 'Create', href: create() },
        ],
    },
});

const form = useForm<ProductFormData>({
    category_id: '',
    name: '',
    slug: '',
    short_description: '',
    description: '',
    price: '',
    compare_at_price: '',
    stock_status: 'in_stock',
    is_best_seller: false,
    is_featured: false,
    is_active: true,
    sold_count: 0,
    images: [],
});

function submit(): void {
    form
        .transform((data) => ({
            ...data,
            images: serializeProductImagesForSubmit(data.images),
        }))
        .post(ProductController.store.url(), {
            forceFormData: true,
            preserveScroll: true,
        });
}
</script>

<template>
    <Head title="Create product" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="icon" class="size-9 shrink-0 text-muted-foreground hover:bg-muted hover:text-foreground">
                <Link :href="index()" aria-label="Back to products">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
            </Button>
            <Heading title="Create product" description="Add a new product to your catalog" />
        </div>

        <div class="max-w-3xl overflow-hidden rounded-xl border border-border bg-card shadow-sm">
            <form class="p-6" @submit.prevent="submit">
                <ProductForm :form="form" :categories="categories" />

                <div class="mt-8 flex items-center gap-3 border-t border-border pt-6">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-primary text-primary-foreground hover:bg-primary/90 disabled:opacity-60"
                    >
                        <svg
                            v-if="form.processing"
                            class="size-4 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        {{ form.processing ? 'Creating…' : 'Create product' }}
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="index()">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
