<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ShopLayout from '@/layouts/shop/ShopLayout.vue';
import ShopPageBreadcrumb from '@/components/shop/ShopPageBreadcrumb.vue';
import DashboardSidebar from '@/components/customer/DashboardSidebar.vue';
import DashboardOverview from '@/components/customer/DashboardOverview.vue';
import DashboardOrders from '@/components/customer/DashboardOrders.vue';
import DashboardProfile from '@/components/customer/DashboardProfile.vue';

const page = usePage();
const user = computed(() => page.props.auth.user as any);

defineProps<{
    orders: Array<any>;
}>();

const activeTab = ref('overview');
</script>

<template>
    <ShopLayout>
        <Head title="My Dashboard" />

        <div class="min-h-screen bg-gray-50/50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <ShopPageBreadcrumb :items="[{ label: 'Dashboard' }]" />

                <div class="mt-6 md:grid md:grid-cols-4 md:gap-8">
                    <DashboardSidebar
                        :user="user"
                        :active-tab="activeTab"
                        @update:active-tab="activeTab = $event"
                    />

                    <main class="md:col-span-3">
                        <DashboardOverview
                            v-if="activeTab === 'overview'"
                            :user="user"
                            :orders="orders"
                            @update:active-tab="activeTab = $event"
                        />

                        <DashboardOrders v-if="activeTab === 'orders'" :orders="orders" />

                        <DashboardProfile v-if="activeTab === 'profile'" :user="user" />
                    </main>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
