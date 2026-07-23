<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
import ProductForm from '@/components/admin/ProductForm.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { mapServerProductImages, serializeProductImagesForSubmit } from '@/lib/admin/productImages';
import { dashboard } from '@/routes';
import { edit, index } from '@/routes/admin/products';
import type {
    AdminCategoryOption,
    AdminProduct,
    ProductFormData,
} from '@/types/admin';

const props = defineProps<{
    product: AdminProduct;
    categories: AdminCategoryOption[];
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
        {
            title: 'Products',
            href: index(),
        },
        {
            title: `Edit ${props.product.name}`,
            href: edit(props.product.id),
        },
    ],
});

const form = useForm<ProductFormData>({
    category_id: props.product.category_id,
    name: props.product.name,
    slug: props.product.slug,
    short_description: props.product.short_description ?? '',
    description: props.product.description ?? '',
    price: props.product.price,
    compare_at_price: props.product.compare_at_price ?? '',
    stock_status: props.product.stock_status,
    is_best_seller: props.product.is_best_seller,
    is_featured: props.product.is_featured,
    is_active: props.product.is_active,
    sold_count: props.product.sold_count,
    images: mapServerProductImages(props.product.images),
});

function submit(): void {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
            images: serializeProductImagesForSubmit(data.images),
        }))
        .post(ProductController.update.url(props.product.id), {
            forceFormData: true,
            preserveScroll: true,
        });
}
</script>

<template>
    <Head :title="`Edit ${product.name}`" />

    <div class="mx-auto flex max-w-5xl flex-col gap-6 p-4 sm:p-6 lg:p-8">
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <Heading :title="`Edit ${product.name}`" description="Update product details, pricing, and media" />
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="product.is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-muted text-muted-foreground'"
                    >
                        {{ product.is_active ? 'Active' : 'Draft' }}
                    </span>
                </div>
            </div>

            <!-- Quick Back Link -->
            <div class="flex items-center gap-2">
                <Button as-child variant="outline" size="sm" class="h-9">
                    <Link :href="index()" class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Products
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Main Content Card -->
        <form @submit.prevent="submit" class="space-y-6">
            <div class="rounded-xl border border-border/80 bg-card p-6 shadow-xs backdrop-blur-sm sm:p-8">
                <ProductForm :form="form" :categories="categories" />
            </div>

            <!-- Sticky/Bottom Action Bar -->
            <div class="sticky bottom-4 z-10 flex items-center justify-between gap-4 rounded-xl border border-border/80 bg-background/95 p-4 shadow-lg backdrop-blur-md">
                <p class="hidden text-xs text-muted-foreground sm:block">
                    Ensure all required fields are filled before saving.
                </p>
                <div class="flex w-full items-center justify-end gap-3 sm:w-auto">
                    <Button as-child variant="ghost" :disabled="form.processing">
                        <Link :href="index()">Cancel</Link>
                    </Button>

                    <Button type="submit" :disabled="form.processing" class="min-w-30">
                        <template v-if="form.processing">
                            <svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            Saving...
                        </template>
                        <template v-else>
                            Save changes
                        </template>
                    </Button>
                </div>
            </div>
        </form>
    </div>
</template>
