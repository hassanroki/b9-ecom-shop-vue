<script setup lang="ts">
import type { AdminOrder } from '@/types/admin';

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import { Separator } from '@/components/ui/separator';

import { Package } from '@lucide/vue';

import { formatTaka } from '@/lib/shop/currency';

defineProps<{
    order: AdminOrder;
}>();
</script>

<template>
    <Card>
        <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-4"
        >
            <div class="space-y-1">
                <CardTitle
                    class="flex items-center gap-2 text-base font-semibold"
                >
                    <Package class="size-4 text-primary" />

                    Order Items
                </CardTitle>

                <CardDescription>
                    {{ order.items_count }}

                    {{ order.items_count === 1 ? 'item' : 'items' }}

                    included
                </CardDescription>
            </div>
        </CardHeader>

        <Separator />

        <CardContent class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/40 text-muted-foreground">
                            <th class="px-6 py-3 text-left text-xs uppercase">
                                Product
                            </th>

                            <th class="px-4 py-3 text-center text-xs uppercase">
                                Qty
                            </th>

                            <th class="px-4 py-3 text-right text-xs uppercase">
                                Unit Price
                            </th>

                            <th class="px-6 py-3 text-right text-xs uppercase">
                                Total
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        <tr
                            v-for="item in order.items"
                            :key="item.id"
                            class="transition-colors hover:bg-muted/20"
                        >
                            <td class="px-6 py-4 font-medium">
                                {{ item.product_name }}
                            </td>

                            <td class="px-4 py-4 text-center">
                                <span
                                    class="inline-flex items-center rounded-md bg-muted px-2 py-1 text-xs font-semibold"
                                >
                                    {{ item.quantity }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-right">
                                {{ formatTaka(item.unit_price) }}
                            </td>

                            <td class="px-6 py-4 text-right font-semibold">
                                {{ formatTaka(item.line_total) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Separator />

            <div class="space-y-2 bg-muted/10 p-6">
                <div class="flex justify-between text-sm text-muted-foreground">
                    <span>Subtotal</span>

                    <span>
                        {{ formatTaka(order.subtotal) }}
                    </span>
                </div>

                <div class="flex justify-between text-sm text-muted-foreground">
                    <span>Delivery Charge</span>

                    <span>
                        {{ formatTaka(order.delivery_charge) }}
                    </span>
                </div>

                <Separator class="my-2" />

                <div class="flex justify-between text-base font-bold">
                    <span>Grand Total</span>

                    <span class="text-primary">
                        {{ formatTaka(order.total) }}
                    </span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
