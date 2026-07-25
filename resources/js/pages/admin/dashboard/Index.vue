<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ShoppingCart, TrendingUp, Users } from '@lucide/vue';
import DashboardBreakdownCard from '@/components/admin/DashboardBreakdownCard.vue';
import DashboardQuickStats from '@/components/admin/DashboardQuickStats.vue';
import DashboardRecentOrders from '@/components/admin/DashboardRecentOrders.vue';
import DashboardRevenueChart from '@/components/admin/DashboardRevenueChart.vue';
import DashboardStatCard from '@/components/admin/DashboardStatCard.vue';
import DashboardTopCategories from '@/components/admin/DashboardTopCategories.vue';
import DashboardTopProducts from '@/components/admin/DashboardTopProducts.vue';
import Heading from '@/components/Heading.vue';
import { formatTaka } from '@/lib/shop/currency';
import { dashboard } from '@/routes';
import type {
    AdminDashboardOverview,
    AdminDashboardPaymentMethodBreakdown,
    AdminDashboardRecentOrder,
    AdminDashboardRevenuePoint,
    AdminDashboardStatusBreakdown,
    AdminDashboardTopCategory,
    AdminDashboardTopProduct,
} from '@/types/admin';

defineProps<{
    overview: AdminDashboardOverview;
    revenue_chart: AdminDashboardRevenuePoint[];
    orders_by_status: AdminDashboardStatusBreakdown[];
    payment_status_breakdown: AdminDashboardStatusBreakdown[];
    payment_method_breakdown: AdminDashboardPaymentMethodBreakdown[];
    top_products: AdminDashboardTopProduct[];
    top_categories: AdminDashboardTopCategory[];
    recent_orders: AdminDashboardRecentOrder[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <Heading
            title="Dashboard"
            description="Store performance and operational overview"
        />

        <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            <DashboardStatCard
                title="Total revenue"
                :value="formatTaka(overview.total_revenue)"
                :change-percent="overview.revenue_change_percent"
                :icon="TrendingUp"
                icon-class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
            />
            <DashboardStatCard
                title="Total orders"
                :value="overview.total_orders.toLocaleString()"
                :change-percent="overview.orders_change_percent"
                :icon="ShoppingCart"
                icon-class="bg-blue-500/10 text-blue-600 dark:text-blue-400"
            />
            <DashboardStatCard
                title="Average order value"
                :value="formatTaka(overview.average_order_value)"
                description="Based on paid orders"
                :icon="TrendingUp"
                icon-class="bg-violet-500/10 text-violet-600 dark:text-violet-400"
            />
            <DashboardStatCard
                title="Customers"
                :value="overview.total_customers.toLocaleString()"
                :description="`${overview.new_customers_this_month} new this month`"
                :icon="Users"
                icon-class="bg-amber-500/10 text-amber-600 dark:text-amber-400"
            />
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <DashboardRevenueChart :data="revenue_chart" />
            </div>
            <DashboardQuickStats :overview="overview" />
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <DashboardBreakdownCard
                title="Orders by status"
                description="Fulfillment pipeline"
                :items="orders_by_status"
                :total="overview.total_orders"
                bar-class="bg-primary"
            />
            <DashboardBreakdownCard
                title="Payment status"
                description="Collection health"
                :items="payment_status_breakdown"
                :total="overview.total_orders"
                bar-class="bg-emerald-500"
            />
            <DashboardBreakdownCard
                title="Payment methods"
                description="How customers pay"
                :items="payment_method_breakdown"
                :total="overview.total_orders"
                bar-class="bg-violet-500"
            />
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            <DashboardRecentOrders :orders="recent_orders" />

            <div class="grid gap-4">
                <DashboardTopProducts :products="top_products" />
                <DashboardTopCategories :categories="top_categories" />
            </div>
        </div>
    </div>
</template>
