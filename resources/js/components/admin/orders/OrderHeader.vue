<script setup lang="ts">
import { Calendar, Download, Printer } from '@lucide/vue';
import { Button } from '@/components/ui/button';

import type { AdminOrder } from '@/types/admin';

import {
    formatDate,
    paymentBadgeClasses,
    statusBadgeClasses,
} from '@/lib/admin/order';

defineProps<{
    order: AdminOrder;
}>();

const emit = defineEmits<{
    (e: 'print'): void;
    (e: 'download'): void;
}>();
</script>

<template>
    <div
        class="flex flex-col gap-4 rounded-xl border bg-card p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ order.order_number }}
                </h1>

                <span
                    class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold uppercase"
                    :class="statusBadgeClasses(order.status)"
                >
                    {{ order.status }}
                </span>

                <span
                    class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold uppercase"
                    :class="paymentBadgeClasses(order.payment_status)"
                >
                    {{ order.payment_status }}
                </span>
            </div>

            <p class="flex items-center gap-2 text-xs text-muted-foreground">
                <Calendar class="size-4" />

                Placed on {{ formatDate(order.placed_at) }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <Button variant="outline" size="sm" @click="emit('print')">
                <Printer class="mr-2 size-4" />

                Print Invoice
            </Button>

            <Button variant="outline" size="sm" @click="emit('download')">
                <Download class="mr-2 size-4" />

                Save PDF
            </Button>
        </div>
    </div>
</template>
