<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    Clock,
    Copy,
    CreditCard,
    Layers,
    Pencil,
    Users,
} from 'lucide-vue-next';
import { ref } from 'vue';
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
import { edit, index, show } from '@/routes/admin/coupons';
import type { AdminCoupon } from '@/types/admin';

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
            title: props.coupon.code,
            href: show(props.coupon.id),
        },
    ],
});

const copied = ref(false);

function copyCode(): void {
    navigator.clipboard.writeText(props.coupon.code);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
}
</script>

<template>
    <Head :title="coupon.name" />

    <div class="mx-auto flex max-w-5xl flex-col gap-6 p-4 sm:p-6 lg:p-8">
        <!-- Header Actions -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-3">
                <Button
                    as-child
                    variant="outline"
                    size="icon"
                    class="h-9 w-9 shrink-0"
                >
                    <Link :href="index()">
                        <ArrowLeft class="size-4" />
                        <span class="sr-only">Back to coupons</span>
                    </Link>
                </Button>
                <div>
                    <div class="flex items-center gap-2.5">
                        <Heading
                            :title="coupon.name"
                            description="Coupon details and usage metrics"
                        />
                        <Badge
                            :variant="
                                coupon.status === 'Active'
                                    ? 'default'
                                    : coupon.status === 'Scheduled'
                                      ? 'secondary'
                                      : 'destructive'
                            "
                            class="capitalize"
                        >
                            {{ coupon.status }}
                        </Badge>
                    </div>
                </div>
            </div>

            <Button
                as-child
                size="sm"
                class="h-9 gap-1.5 self-start sm:self-auto"
            >
                <Link :href="edit(coupon.id)">
                    <Pencil class="size-3.5" />
                    Edit coupon
                </Link>
            </Button>
        </div>

        <!-- Quick Summary Bar -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Code Banner Card -->
            <Card class="border-border/80 bg-card shadow-xs">
                <CardContent class="p-5">
                    <p class="text-xs font-medium text-muted-foreground">
                        Coupon Code
                    </p>
                    <div class="mt-2 flex items-center justify-between">
                        <code
                            class="rounded-md bg-muted px-2.5 py-1 font-mono text-base font-bold tracking-wider text-foreground"
                        >
                            {{ coupon.code }}
                        </code>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-8 px-2 text-xs text-muted-foreground hover:text-foreground"
                            @click="copyCode"
                        >
                            <Copy class="mr-1 size-3.5" />
                            {{ copied ? 'Copied' : 'Copy' }}
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Discount Value -->
            <Card class="border-border/80 bg-card shadow-xs">
                <CardContent class="p-5">
                    <p class="text-xs font-medium text-muted-foreground">
                        Discount
                    </p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-2xl font-semibold tracking-tight">
                            {{ coupon.formatted_discount }}
                        </span>
                        <span class="text-xs text-muted-foreground capitalize">
                            {{ coupon.type }}
                        </span>
                    </div>
                </CardContent>
            </Card>

            <!-- Total Usage -->
            <Card class="border-border/80 bg-card shadow-xs">
                <CardContent class="p-5">
                    <p class="text-xs font-medium text-muted-foreground">
                        Redemptions
                    </p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-2xl font-semibold tracking-tight">
                            {{ coupon.used_count }}
                        </span>
                        <span class="text-xs text-muted-foreground">
                            <template v-if="coupon.usage_limit">
                                of {{ coupon.usage_limit }} max
                            </template>
                            <template v-else> / Unlimited </template>
                        </span>
                    </div>
                </CardContent>
            </Card>

            <!-- Minimum Spend -->
            <Card class="border-border/80 bg-card shadow-xs">
                <CardContent class="p-5">
                    <p class="text-xs font-medium text-muted-foreground">
                        Minimum Order
                    </p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-2xl font-semibold tracking-tight">
                            {{ coupon.minimum_amount }}
                        </span>
                        <span class="text-xs text-muted-foreground"
                            >Requirement</span
                        >
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Detailed Breakdown -->
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Rules & Limits Container -->
            <Card class="border-border/80 bg-card shadow-xs md:col-span-2">
                <CardHeader class="pb-4">
                    <CardTitle class="text-base font-semibold"
                        >Rules & Conditions</CardTitle
                    >
                    <CardDescription
                        >Constraints and limitations assigned to this
                        discount</CardDescription
                    >
                </CardHeader>
                <CardContent class="grid gap-6">
                    <div
                        class="space-y-1 rounded-lg border border-border/50 bg-muted/30 p-3.5"
                    >
                        <span class="text-xs font-medium text-muted-foreground"
                            >Description</span
                        >
                        <p class="text-sm font-normal text-foreground">
                            {{
                                coupon.description ||
                                'No detailed description provided for this coupon.'
                            }}
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div
                            class="flex items-start gap-3 rounded-lg border border-border/50 p-3.5"
                        >
                            <CreditCard
                                class="mt-0.5 size-4 text-muted-foreground"
                            />
                            <div>
                                <p
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    Max Discount Amount
                                </p>
                                <p class="mt-0.5 text-sm font-medium">
                                    {{
                                        coupon.maximum_discount ??
                                        'No ceiling cap set'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 rounded-lg border border-border/50 p-3.5"
                        >
                            <Users
                                class="mt-0.5 size-4 text-muted-foreground"
                            />
                            <div>
                                <p
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    User Limit
                                </p>
                                <p class="mt-0.5 text-sm font-medium">
                                    {{ coupon.usage_per_user }}
                                    {{
                                        coupon.usage_per_user === 1
                                            ? 'use'
                                            : 'uses'
                                    }}
                                    per customer
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Timing & History Sidebar -->
            <Card class="border-border/80 bg-card shadow-xs">
                <CardHeader class="pb-4">
                    <CardTitle class="text-base font-semibold"
                        >Validity & Timeline</CardTitle
                    >
                    <CardDescription
                        >Schedule and system timestamps</CardDescription
                    >
                </CardHeader>
                <CardContent class="space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <Calendar class="mt-0.5 size-4 text-muted-foreground" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Start Date
                            </p>
                            <p class="font-medium text-foreground">
                                {{ coupon.starts_at ?? 'Immediately active' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <Clock class="mt-0.5 size-4 text-muted-foreground" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Expiration Date
                            </p>
                            <p class="font-medium text-foreground">
                                {{ coupon.expires_at ?? 'Never expires' }}
                            </p>
                        </div>
                    </div>

                    <hr class="border-border/60" />

                    <div class="flex items-start gap-3">
                        <Layers class="mt-0.5 size-4 text-muted-foreground" />
                        <div class="space-y-1 text-xs">
                            <div>
                                <span class="text-muted-foreground"
                                    >Created:
                                </span>
                                <span class="font-medium text-foreground">
                                    {{
                                        new Date(
                                            coupon.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </span>
                            </div>
                            <div>
                                <span class="text-muted-foreground"
                                    >Updated:
                                </span>
                                <span class="font-medium text-foreground">
                                    {{
                                        new Date(
                                            coupon.updated_at,
                                        ).toLocaleDateString()
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
