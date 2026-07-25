<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import CouponController from '@/actions/App/Http/Controllers/Admin/CouponController';
import CouponForm from '@/components/admin/CouponForm.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { edit, index } from '@/routes/admin/coupons';
import type { AdminCoupon, CouponFormData } from '@/types/admin';

const props = defineProps<{
    coupon: AdminCoupon;
}>();

setLayoutProps({
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
            title: `Edit ${props.coupon.name}`,
            href: edit(props.coupon.id),
        },
    ],
});

const form = useForm<CouponFormData>({
    name: props.coupon.name,
    code: props.coupon.code,
    description: props.coupon.description ?? '',
    type: props.coupon.type,
    value: props.coupon.value,
    minimum_amount: props.coupon.minimum_amount,
    maximum_discount: props.coupon.maximum_discount ?? '',
    usage_limit: props.coupon.usage_limit?.toString() ?? '',
    usage_per_user: props.coupon.usage_per_user,
    starts_at: props.coupon.starts_at ?? '',
    expires_at: props.coupon.expires_at ?? '',
    is_active: props.coupon.is_active,
});

function submit(): void {
    form.put(CouponController.update.url(props.coupon.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Edit ${coupon.name}`" />

    <div class="mx-auto flex max-w-4xl flex-col gap-6 p-4 sm:p-6 lg:p-8">
        <!-- Page Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <Heading
                        :title="`Edit ${coupon.name}`"
                        description="Update discount settings, constraints, and validity"
                    />
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="
                            coupon.is_active
                                ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                : 'bg-muted text-muted-foreground'
                        "
                    >
                        {{ coupon.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

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

        <!-- Main Content Card -->
        <form @submit.prevent="submit" class="space-y-6">
            <div
                class="rounded-xl border border-border/80 bg-card p-6 shadow-xs backdrop-blur-sm sm:p-8"
            >
                <CouponForm :form="form" :coupon="coupon" />
            </div>

            <!-- Sticky Bottom Action Bar -->
            <div
                class="sticky bottom-4 z-10 flex items-center justify-between gap-4 rounded-xl border border-border/80 bg-background/95 p-4 shadow-lg backdrop-blur-md"
            >
                <p class="hidden text-xs text-muted-foreground sm:block">
                    Changes made here will apply immediately upon saving.
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
                            Saving...
                        </template>
                        <template v-else> Save changes </template>
                    </Button>
                </div>
            </div>
        </form>
    </div>
</template>
