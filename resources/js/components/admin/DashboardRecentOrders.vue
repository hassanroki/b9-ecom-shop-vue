<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatTaka } from '@/lib/shop/currency';
import { index as ordersIndex, show as orderShow } from '@/routes/admin/orders';
import type { AdminDashboardRecentOrder } from '@/types/admin';

defineProps<{
    orders: AdminDashboardRecentOrder[];
}>();

function orderStatusVariant(
    status: AdminDashboardRecentOrder['status'],
): 'default' | 'secondary' | 'destructive' | 'outline' {
    switch (status) {
        case 'delivered':
            return 'default';
        case 'cancelled':
            return 'destructive';
        case 'pending':
            return 'secondary';
        default:
            return 'outline';
    }
}

function paymentStatusVariant(
    status: AdminDashboardRecentOrder['payment_status'],
): 'default' | 'secondary' | 'destructive' | 'outline' {
    switch (status) {
        case 'paid':
            return 'default';
        case 'failed':
        case 'cancelled':
            return 'destructive';
        default:
            return 'secondary';
    }
}

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleString('en-BD', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}
</script>

<template>
    <Card>
        <CardHeader class="flex flex-row items-center justify-between gap-4">
            <div>
                <CardTitle>Recent orders</CardTitle>
                <CardDescription>Latest customer activity</CardDescription>
            </div>
            <Button variant="outline" size="sm" as-child>
                <Link :href="ordersIndex()">View all</Link>
            </Button>
        </CardHeader>
        <CardContent class="overflow-x-auto">
            <table class="w-full min-w-130 text-sm">
                <thead>
                    <tr class="border-b text-left text-muted-foreground">
                        <th class="pr-4 pb-3 font-medium">Order</th>
                        <th class="pr-4 pb-3 font-medium">Customer</th>
                        <th class="pr-4 pb-3 font-medium">Total</th>
                        <th class="pr-4 pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium">Placed</th>
                        <th class="pb-3 pl-4 font-medium">
                            <span class="sr-only">View</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="order in orders"
                        :key="order.id"
                        class="border-b last:border-0"
                    >
                        <td class="py-3 pr-4 font-medium">
                            {{ order.order_number }}
                        </td>
                        <td class="py-3 pr-4">{{ order.customer_name }}</td>
                        <td class="py-3 pr-4">{{ formatTaka(order.total) }}</td>
                        <td class="py-3 pr-4">
                            <div class="flex flex-wrap gap-1.5">
                                <Badge
                                    :variant="orderStatusVariant(order.status)"
                                >
                                    {{ order.status }}
                                </Badge>
                                <Badge
                                    :variant="
                                        paymentStatusVariant(
                                            order.payment_status,
                                        )
                                    "
                                >
                                    {{ order.payment_status }}
                                </Badge>
                            </div>
                        </td>
                        <td class="py-3 text-muted-foreground">
                            {{ formatDate(order.placed_at) }}
                        </td>
                        <td class="py-3 pl-4 text-right">
                            <Button variant="ghost" size="icon-sm" as-child>
                                <Link :href="orderShow(order.id)">
                                    <Eye class="size-4" />
                                    <span class="sr-only">View order</span>
                                </Link>
                            </Button>
                        </td>
                    </tr>
                    <tr v-if="orders.length === 0">
                        <td
                            colspan="6"
                            class="py-8 text-center text-muted-foreground"
                        >
                            No orders yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardContent>
    </Card>
</template>
