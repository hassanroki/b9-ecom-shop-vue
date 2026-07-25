<script setup lang="ts">
import type { AdminOrder } from '@/types/admin';
import { formatDate, paymentMethodLabel } from '@/lib/admin/order';
import { formatTaka } from '@/lib/shop/currency';

defineProps<{
    order: AdminOrder;
}>();
</script>

<template>
    <div id="invoice-print" class="hidden bg-white p-8 text-black print:block">
        <!-- Invoice Header -->
        <div class="mb-6 flex items-start justify-between border-b pb-6">
            <div>
                <h1 class="text-3xl font-bold">INVOICE</h1>

                <p class="mt-1 text-sm text-gray-500">
                    Order #{{ order.order_number }}
                </p>
            </div>

            <div class="text-right">
                <h2 class="text-xl font-bold">B9-Ecom</h2>

                <p class="text-xs text-gray-500">support@b9ecom.com</p>
            </div>
        </div>

        <!-- Customer + Order -->
        <div class="mb-8 grid grid-cols-2 gap-8 text-sm">
            <div>
                <h3
                    class="mb-2 border-b pb-1 text-xs font-bold text-gray-500 uppercase"
                >
                    Customer
                </h3>

                <p class="font-semibold">
                    {{ order.customer_name }}
                </p>

                <p>{{ order.phone }}</p>

                <p>{{ order.email }}</p>

                <p class="mt-2">
                    {{ order.address }}
                </p>

                <p>
                    {{ order.area }},
                    {{ order.district }}
                </p>
            </div>

            <div>
                <h3
                    class="mb-2 border-b pb-1 text-xs font-bold text-gray-500 uppercase"
                >
                    Order Details
                </h3>

                <p>
                    <strong>Date :</strong>

                    {{ formatDate(order.placed_at) }}
                </p>

                <p>
                    <strong>Payment :</strong>

                    {{ paymentMethodLabel(order.payment_method) }}
                </p>

                <p>
                    <strong>Payment Status :</strong>

                    {{ order.payment_status }}
                </p>

                <p>
                    <strong>Order Status :</strong>

                    {{ order.status }}
                </p>
            </div>
        </div>

        <!-- Table -->

        <table class="mb-8 w-full border-collapse text-sm">
            <thead>
                <tr class="border-b-2 border-gray-300">
                    <th class="py-2 text-left">#</th>

                    <th class="py-2 text-left">Product</th>

                    <th class="py-2 text-center">Qty</th>

                    <th class="py-2 text-right">Price</th>

                    <th class="py-2 text-right">Total</th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="(item, index) in order.items"
                    :key="item.id"
                    class="border-b"
                >
                    <td class="py-3">
                        {{ index + 1 }}
                    </td>

                    <td>
                        {{ item.product_name }}
                    </td>

                    <td class="text-center">
                        {{ item.quantity }}
                    </td>

                    <td class="text-right">
                        {{ formatTaka(item.unit_price) }}
                    </td>

                    <td class="text-right">
                        {{ formatTaka(item.line_total) }}
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" class="py-2 text-right">Subtotal</td>

                    <td class="text-right">
                        {{ formatTaka(order.subtotal) }}
                    </td>
                </tr>

                <tr>
                    <td colspan="4" class="py-2 text-right">Delivery Charge</td>

                    <td class="text-right">
                        {{ formatTaka(order.delivery_charge) }}
                    </td>
                </tr>

                <tr class="border-t-2 border-black text-base font-bold">
                    <td colspan="4" class="py-3 text-right">Grand Total</td>

                    <td class="text-right">
                        {{ formatTaka(order.total) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="border-t pt-5 text-center text-xs text-gray-500">
            <p>Thank you for shopping with us.</p>
        </div>
    </div>
</template>
