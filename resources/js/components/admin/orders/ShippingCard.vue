<script setup lang="ts">
import type { AdminOrder } from '@/types/admin';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

import { Separator } from '@/components/ui/separator';

import { MapPin } from '@lucide/vue';

defineProps<{
    order: AdminOrder;
}>();
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base font-semibold">
                <MapPin class="size-4 text-primary" />

                Shipping Address
            </CardTitle>
        </CardHeader>

        <Separator />

        <CardContent class="space-y-4 pt-4 text-sm">
            <div>
                <p class="leading-relaxed text-foreground">
                    {{ order.address }}

                    <br />

                    <span class="text-muted-foreground">
                        {{ order.area }}, {{ order.district }}
                    </span>
                </p>
            </div>

            <div
                v-if="order.notes"
                class="rounded-lg border border-amber-200 bg-amber-50 p-3"
            >
                <p
                    class="mb-2 text-xs font-semibold tracking-wider text-amber-700 uppercase"
                >
                    Customer Note
                </p>

                <p class="text-sm text-amber-900">
                    {{ order.notes }}
                </p>
            </div>
        </CardContent>
    </Card>
</template>
