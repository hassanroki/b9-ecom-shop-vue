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
</script>

<template>
    <Head :title="coupon.name" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading :title="coupon.name" description="Coupon details" />

            <Button as-child>
                <Link :href="edit(coupon.id)">
                    <Pencil class="size-4" />
                    Edit coupon
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Details</CardTitle>
                <CardDescription> Overview of this coupon </CardDescription>
            </CardHeader>
            <CardContent class="grid gap-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Code</p>
                        <Badge variant="outline" class="w-fit">
                            {{ coupon.code }}
                        </Badge>
                    </div>
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Status</p>
                        <Badge
                            :variant="
                                coupon.status === 'Active'
                                    ? 'default'
                                    : coupon.status === 'Scheduled'
                                      ? 'secondary'
                                      : 'destructive'
                            "
                            class="w-fit"
                        >
                            {{ coupon.status }}
                        </Badge>
                    </div>
                </div>

                <div class="grid gap-1">
                    <p class="text-sm text-muted-foreground">Description</p>
                    <p class="font-medium">
                        {{ coupon.description || 'No description provided.' }}
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Discount Type
                        </p>
                        <p class="font-medium capitalize">
                            {{ coupon.type }}
                        </p>
                    </div>
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Discount Value
                        </p>
                        <p class="font-medium">
                            {{ coupon.formatted_discount }}
                        </p>
                    </div>
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Maximum Discount
                        </p>
                        <p class="font-medium">
                            {{ coupon.maximum_discount ?? 'No limit' }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Minimum Order
                        </p>
                        <p class="font-medium">
                            {{ coupon.minimum_amount }}
                        </p>
                    </div>
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Usage</p>
                        <p class="font-medium">
                            {{ coupon.used_count }}
                            <span v-if="coupon.usage_limit">
                                / {{ coupon.usage_limit }}
                            </span>
                            <span v-else class="text-muted-foreground">
                                / Unlimited
                            </span>
                        </p>
                    </div>
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Usage Per User
                        </p>
                        <p class="font-medium">
                            {{ coupon.usage_per_user }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Starts At</p>
                        <p class="font-medium">
                            {{ coupon.starts_at ?? 'Immediately' }}
                        </p>
                    </div>
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Expires At</p>
                        <p class="font-medium">
                            {{ coupon.expires_at ?? 'Never' }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Created</p>
                        <p class="font-medium">
                            {{ new Date(coupon.created_at).toLocaleString() }}
                        </p>
                    </div>
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Updated</p>
                        <p class="font-medium">
                            {{ new Date(coupon.updated_at).toLocaleString() }}
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
