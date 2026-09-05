<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    ChevronRight,
    Heart,
    LayoutGrid,
    Package,
    ShoppingCart,
    Tags,
    Ticket,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import {
    index as brandsIndex,
    create as brandsCreate,
} from '@/routes/admin/brands';
import {
    index as categoriesIndex,
    create as categoriesCreate,
} from '@/routes/admin/categories';
import { index as couponsIndex } from '@/routes/admin/coupons';
import { index as ordersIndex } from '@/routes/admin/orders';
import { index as productsIndex } from '@/routes/admin/products';
import { index as wishlistsIndex } from '@/routes/admin/wishlists';

const mainNavItems = ref([
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Categories',
        icon: Tags,
        isOpen: false,
        children: [
            { title: 'All Categories', href: categoriesIndex() },
            { title: 'Add Category', href: categoriesCreate() },
        ],
    },
    {
        title: 'Brands',
        icon: Tags,
        isOpen: false,
        children: [
            { title: 'All Brands', href: brandsIndex() },
            { title: 'Add Brand', href: brandsCreate() },
        ],
    },
    {
        title: 'Products',
        href: productsIndex(),
        icon: Package,
    },
    {
        title: 'Coupons',
        href: couponsIndex(),
        icon: Ticket,
    },
    {
        title: 'Orders',
        href: ordersIndex(),
        icon: ShoppingCart,
    },
    {
        title: 'Wishlists',
        href: wishlistsIndex(),
        icon: Heart,
    },
]);

const toggleSubmenu = (item: any) => {
    if (item.children) {
        item.isOpen = !item.isOpen;
    }
};
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="px-2 py-4">
            <div v-for="item in mainNavItems" :key="item.title" class="mb-1">
                <!-- If item has children (Submenu) -->
                <template v-if="item.children">
                    <button @click="toggleSubmenu(item)"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                        <div class="flex items-center gap-x-3">
                            <component :is="item.icon" class="h-4 w-4" />
                            <span>{{ item.title }}</span>
                        </div>
                        <ChevronRight class="h-4 w-4 transition-transform duration-200"
                            :class="{ 'rotate-90': item.isOpen }" />
                    </button>

                    <!-- Submenu items -->
                    <div v-show="item.isOpen"
                        class="mt-1 ml-6 flex flex-col space-y-1 border-l border-sidebar-border pl-2">
                        <Link v-for="subItem in item.children" :key="subItem.title" :href="subItem.href"
                            class="rounded-md px-3 py-1.5 text-sm text-neutral-600 transition-colors hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-100">
                            {{ subItem.title }}
                        </Link>
                    </div>
                </template>

                <!-- Normal Item without Children -->
                <template v-else>
                    <Link :href="item.href!"
                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                        <component :is="item.icon" class="h-4 w-4" />
                        <span>{{ item.title }}</span>
                    </Link>
                </template>
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
