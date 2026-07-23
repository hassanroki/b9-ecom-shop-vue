<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';

import OrderController from '@/actions/App/Http/Controllers/Admin/OrderController';

import type {
    AdminOrder,
    AdminStatusOption,
    OrderUpdateFormData,
} from '@/types/admin';

import { dashboard } from '@/routes';
import { index, show } from '@/routes/admin/orders';

import OrderHeader from '@/components/admin/orders/OrderHeader.vue';
import OrderStepper from '@/components/admin/orders/OrderStepper.vue';
import OrderItems from '@/components/admin/orders/OrderItems.vue';
import StatusHistory from '@/components/admin/orders/StatusHistory.vue';
import CustomerCard from '@/components/admin/orders/CustomerCard.vue';
import ShippingCard from '@/components/admin/orders/ShippingCard.vue';
import PaymentCard from '@/components/admin/orders/PaymentCard.vue';
import OrderUpdateCard from '@/components/admin/orders/OrderUpdateCard.vue';
import PrintInvoice from '@/components/admin/orders/PrintInvoice.vue';

const props = defineProps<{
    order: AdminOrder;
    statusOptions: AdminStatusOption[];
    paymentStatusOptions: AdminStatusOption[];
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
        {
            title: 'Orders',
            href: index(),
        },
        {
            title: props.order.order_number,
            href: show(props.order.id),
        },
    ],
});

const form = useForm<OrderUpdateFormData>({
    status: props.order.status,
    payment_status: props.order.payment_status,
    note: '',
});

function submit() {
    form.put(OrderController.update.url(props.order.id), {
        preserveScroll: true,
        onSuccess: () => form.reset('note'),
    });
}

function printInvoice() {
    window.print();
}
</script>

<template>
    <Head :title="order.order_number" />

    <div id="order-screen" class="mx-auto flex max-w-7xl flex-col gap-6 p-6">
        <OrderHeader
            :order="order"
            @print="printInvoice"
            @download="printInvoice"
        />

        <OrderStepper :order="order" />

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <OrderItems :order="order" />

                <StatusHistory :order="order" />
            </div>

            <div class="space-y-6">
                <CustomerCard :order="order" />

                <ShippingCard :order="order" />

                <PaymentCard :order="order" />

                <OrderUpdateCard
                    :form="form"
                    :status-options="statusOptions"
                    :payment-status-options="paymentStatusOptions"
                    @submit="submit"
                />
            </div>
        </div>
    </div>

    <PrintInvoice :order="order" />
</template>

<style scoped>
@page {
    size: A4;
    margin: 10mm;
}

@media print {
    html,
    body {
        background: #fff !important;
        margin: 0;
        padding: 0;
    }

    body * {
        visibility: hidden;
    }

    #invoice-print,
    #invoice-print * {
        visibility: visible;
    }

    #invoice-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        background: #fff;
    }

    #order-screen {
        display: none !important;
    }

    header,
    nav,
    aside,
    footer {
        display: none !important;
    }
}
</style>
