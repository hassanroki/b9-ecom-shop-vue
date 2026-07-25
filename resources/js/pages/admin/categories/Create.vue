<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import CategoryController from '@/actions/App/Http/Controllers/Admin/CategoryController';
import CategoryForm from '@/components/admin/CategoryForm.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { create, index } from '@/routes/admin/categories';
import type { CategoryFormData } from '@/types/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Categories', href: index() },
            { title: 'Create', href: create() },
        ],
    },
});

const form = useForm<CategoryFormData>({
    name: '',
    slug: '',
    image_source: 'url',
    image: '',
    image_file: null,
    description: '',
    sort_order: 0,
    is_active: true,
});

function submit(): void {
    form.post(CategoryController.store.url(), {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Create category" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">
        <div class="flex items-center gap-3">
            <Button
                as-child
                variant="ghost"
                size="icon"
                class="size-9 shrink-0 text-muted-foreground hover:bg-muted hover:text-foreground"
            >
                <Link :href="index()" aria-label="Back to categories">
                    <svg
                        class="size-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </Link>
            </Button>
            <Heading
                title="Create category"
                description="Add a new product category to your store"
            />
        </div>

        <div
            class="max-w-2xl rounded-xl border border-border bg-card shadow-sm"
        >
            <form class="p-6" @submit.prevent="submit">
                <CategoryForm :form="form" />

                <div
                    class="mt-8 flex items-center gap-3 border-t border-border pt-6"
                >
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
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />
                        </svg>
                        {{ form.processing ? 'Creating…' : 'Create category' }}
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="index()">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
