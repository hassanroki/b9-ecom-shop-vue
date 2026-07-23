<script setup lang="ts">
import type { AdminOrder } from '@/types/admin';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

import { Separator } from '@/components/ui/separator';

import { CreditCard } from '@lucide/vue';

import { paymentBadgeClasses, paymentMethodLabel } from '@/lib/admin/order';

defineProps<{
    order: AdminOrder;
}>();
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base font-semibold">
                <CreditCard class="size-4 text-primary" />

                Payment Information
            </CardTitle>
        </CardHeader>

        <Separator />

        <CardContent class="space-y-4 pt-4 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-muted-foreground"> Payment Method </span>

                <span class="font-medium">
                    {{ paymentMethodLabel(order.payment_method) }}
                </span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-muted-foreground"> Payment Status </span>

                <span
                    :class="[
                        'rounded-md border px-2 py-1 text-xs font-semibold uppercase',
                        paymentBadgeClasses(order.payment_status),
                    ]"
                >
                    {{ order.payment_status }}
                </span>
            </div>
        </CardContent>
    </Card>
</template>
