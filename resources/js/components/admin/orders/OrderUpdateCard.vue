<script setup lang="ts">
import type { AdminStatusOption, OrderUpdateFormData } from '@/types/admin';

import type { InertiaForm } from '@inertiajs/vue3';

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import { Separator } from '@/components/ui/separator';

import { RotateCcw } from '@lucide/vue';

import OrderStatusForm from '@/components/admin/OrderStatusForm.vue';

defineProps<{
    form: InertiaForm<OrderUpdateFormData>;
    statusOptions: AdminStatusOption[];
    paymentStatusOptions: AdminStatusOption[];
}>();
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base font-semibold">
                <RotateCcw class="size-4 text-primary" />

                Update Order
            </CardTitle>

            <CardDescription>
                Update fulfillment or payment status
            </CardDescription>
        </CardHeader>

        <Separator />

        <CardContent class="pt-4">
            <form @submit.prevent>
                <OrderStatusForm
                    :form="form"
                    :status-options="statusOptions"
                    :payment-status-options="paymentStatusOptions"
                />
            </form>
        </CardContent>
    </Card>
</template>
