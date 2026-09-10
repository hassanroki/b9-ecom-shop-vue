<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import CouponController from '@/actions/App/Http/Controllers/Admin/CouponController';
import CouponForm from '@/components/admin/CouponForm.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { create, index } from '@/routes/admin/coupons';
import type { CouponFormData } from '@/types/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Coupons',
                href: index(),
            },
            {
                title: 'Create',
                href: create(),
            },
        ],
    },
});

const form = useForm<CouponFormData>({
    name: '',
    code: '',
    description: '',
    type: 'percentage',
    value: '',
    minimum_amount: '',
    maximum_discount: '',
    usage_limit: '',
    usage_per_user: 1,
    starts_at: '',
    expires_at: '',
    is_active: true,
});

function submit(): void {
    form.post(CouponController.store.url(), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Create coupon" />

    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 p-4 sm:p-6 lg:p-8">
        <!-- Page Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Create coupon"
                description="Add a new discount coupon for your store"
            />

            <!-- Quick Back Link -->
            <div class="flex items-center gap-2">
                <Button as-child variant="outline" size="sm" class="h-9">
                    <Link :href="index()" class="flex items-center gap-1.5">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Back to Coupons
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Main Content Form -->
        <form @submit.prevent="submit" class="space-y-6">
            <div
                class="rounded-xl border border-border/80 bg-card p-6 shadow-xs backdrop-blur-sm sm:p-8"
            >
                <CouponForm :form="form" />
            </div>

            <!-- Sticky Bottom Action Bar -->
            <div
                class="sticky bottom-4 z-10 flex items-center justify-between gap-4 rounded-xl border border-border/80 bg-background/95 p-4 shadow-lg backdrop-blur-md"
            >
                <p class="hidden text-xs text-muted-foreground sm:block">
                    Double-check discount logic and limits before publishing.
                </p>
                <div
                    class="flex w-full items-center justify-end gap-3 sm:w-auto"
                >
                    <Button
                        as-child
                        variant="ghost"
                        :disabled="form.processing"
                    >
                        <Link :href="index()">Cancel</Link>
                    </Button>

                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="min-w-30"
                    >
                        <template v-if="form.processing">
                            <svg
                                class="mr-2 h-4 w-4 animate-spin"
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
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                />
                            </svg>
                            Creating...
                        </template>
                        <template v-else> Create coupon </template>
                    </Button>
                </div>
            </div>
        </form>
    </div>
</template>
