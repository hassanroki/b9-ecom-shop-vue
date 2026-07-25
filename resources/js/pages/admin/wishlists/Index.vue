<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Heart,
    Search,
    X,
    Package,
    User,
    Calendar,
    ExternalLink,
} from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatTaka } from '@/lib/shop/currency';
import { dashboard } from '@/routes';
import { index } from '@/routes/admin/wishlists';
import type {
    AdminWishlistFilters,
    AdminWishlistListItem,
} from '@/types/admin';

const props = defineProps<{
    wishlists: AdminWishlistListItem[];
    filters: AdminWishlistFilters;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Wishlists', href: index() },
        ],
    },
});

// Filter state
const search = ref(props.filters.search || '');
let debounceTimer: ReturnType<typeof setTimeout>;

function performSearch() {
    router.get(
        index(),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
}

// Debounced typing handler
watch(search, (newValue) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        performSearch();
    }, 350);
});

function handleReset() {
    search.value = '';
    performSearch();
}

function stockStatusVariant(
    status: AdminWishlistListItem['product']['stock_status'],
): 'default' | 'secondary' | 'destructive' | 'outline' {
    return status === 'in_stock' ? 'default' : 'destructive';
}

function formatDate(value: string): string {
    if (!value) return '—';
    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Wishlists" />

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Wishlists"
                description="Monitor customer wishlist analytics and product interest across your store."
            />
            <div class="flex items-center gap-2">
                <Badge variant="outline" class="px-3 py-1 text-xs">
                    Total Items: {{ wishlists.length }}
                </Badge>
            </div>
        </div>

        <!-- Filter Bar -->
        <div
            class="flex flex-col items-center gap-3 rounded-xl border bg-card p-4 shadow-sm sm:flex-row"
        >
            <div class="relative w-full sm:max-w-md">
                <Search
                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    type="text"
                    placeholder="Search by customer name, email, or product..."
                    class="pr-9 pl-9"
                />
                <button
                    v-if="search"
                    type="button"
                    @click="handleReset"
                    class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                >
                    <X class="size-4" />
                </button>
            </div>

            <div
                v-if="props.filters.search"
                class="flex w-full items-center gap-2 sm:w-auto"
            >
                <Button
                    variant="ghost"
                    size="sm"
                    @click="handleReset"
                    class="text-xs"
                >
                    Clear Filters
                </Button>
            </div>
        </div>

        <!-- Desktop View: Table -->
        <div
            class="hidden overflow-hidden rounded-xl border bg-card shadow-sm md:block"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b bg-muted/50 text-xs tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-6 py-3.5 font-medium">Customer</th>
                            <th class="px-6 py-3.5 font-medium">Product</th>
                            <th class="px-6 py-3.5 font-medium">Price</th>
                            <th class="px-6 py-3.5 font-medium">
                                Stock Status
                            </th>
                            <th class="px-6 py-3.5 font-medium">Date Added</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="wishlist in wishlists"
                            :key="wishlist.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <!-- Customer Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary"
                                    >
                                        {{
                                            wishlist.user.name
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </div>
                                    <div class="flex min-w-0 flex-col">
                                        <span
                                            class="truncate font-medium text-foreground"
                                        >
                                            {{ wishlist.user.name }}
                                        </span>
                                        <span
                                            class="truncate text-xs text-muted-foreground"
                                        >
                                            {{ wishlist.user.email }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Product Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-muted"
                                    >
                                        <img
                                            v-if="wishlist.product.image"
                                            :src="wishlist.product.image"
                                            :alt="wishlist.product.name"
                                            class="size-full object-cover"
                                        />
                                        <Package
                                            v-else
                                            class="size-5 text-muted-foreground/60"
                                        />
                                    </div>
                                    <div class="flex max-w-xs flex-col">
                                        <span
                                            class="line-clamp-1 font-medium text-foreground"
                                        >
                                            {{ wishlist.product.name }}
                                        </span>
                                        <div
                                            class="mt-0.5 flex items-center gap-2"
                                        >
                                            <Badge
                                                v-if="
                                                    !wishlist.product.is_active
                                                "
                                                variant="outline"
                                                class="border-amber-500/30 bg-amber-50 px-1.5 py-0 text-[10px] text-amber-600 dark:bg-amber-950/20"
                                            >
                                                Inactive
                                            </Badge>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-4 font-semibold text-foreground">
                                {{ formatTaka(wishlist.product.price) }}
                            </td>

                            <!-- Stock -->
                            <td class="px-6 py-4">
                                <Badge
                                    :variant="
                                        stockStatusVariant(
                                            wishlist.product.stock_status,
                                        )
                                    "
                                    class="capitalize"
                                >
                                    {{
                                        wishlist.product.stock_status.replace(
                                            '_',
                                            ' ',
                                        )
                                    }}
                                </Badge>
                            </td>

                            <!-- Added Date -->
                            <td
                                class="px-6 py-4 text-xs whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(wishlist.created_at) }}
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="wishlists.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div
                                    class="flex flex-col items-center justify-center gap-2"
                                >
                                    <div
                                        class="flex size-12 items-center justify-center rounded-full bg-muted"
                                    >
                                        <Heart
                                            class="size-6 text-muted-foreground"
                                        />
                                    </div>
                                    <h3
                                        class="mt-2 font-semibold text-foreground"
                                    >
                                        No wishlist items found
                                    </h3>
                                    <p
                                        class="max-w-sm text-sm text-muted-foreground"
                                    >
                                        {{
                                            search
                                                ? 'No items match your search filter. Try clearing the search box.'
                                                : 'Customers have not added any items to their wishlist yet.'
                                        }}
                                    </p>
                                    <Button
                                        v-if="search"
                                        variant="outline"
                                        size="sm"
                                        @click="handleReset"
                                        class="mt-2"
                                    >
                                        Clear Search
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile View: Cards Layout -->
        <div class="grid grid-cols-1 gap-4 md:hidden">
            <div
                v-for="wishlist in wishlists"
                :key="wishlist.id"
                class="flex flex-col gap-3 rounded-xl border bg-card p-4 shadow-sm"
            >
                <div
                    class="flex items-start justify-between gap-3 border-b pb-3"
                >
                    <div class="flex min-w-0 items-center gap-2">
                        <div
                            class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary"
                        >
                            {{ wishlist.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex min-w-0 flex-col">
                            <span class="truncate text-xs font-medium">{{
                                wishlist.user.name
                            }}</span>
                            <span
                                class="truncate text-[11px] text-muted-foreground"
                                >{{ wishlist.user.email }}</span
                            >
                        </div>
                    </div>
                    <span class="shrink-0 text-[11px] text-muted-foreground">
                        {{ formatDate(wishlist.created_at) }}
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-muted"
                    >
                        <img
                            v-if="wishlist.product.image"
                            :src="wishlist.product.image"
                            :alt="wishlist.product.name"
                            class="size-full object-cover"
                        />
                        <Package
                            v-else
                            class="size-6 text-muted-foreground/60"
                        />
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col">
                        <span
                            class="line-clamp-1 text-sm font-medium text-foreground"
                        >
                            {{ wishlist.product.name }}
                        </span>
                        <div
                            class="mt-1 flex items-center justify-between gap-2"
                        >
                            <span class="text-sm font-semibold">
                                {{ formatTaka(wishlist.product.price) }}
                            </span>
                            <Badge
                                :variant="
                                    stockStatusVariant(
                                        wishlist.product.stock_status,
                                    )
                                "
                                class="px-1.5 py-0 text-[10px] capitalize"
                            >
                                {{
                                    wishlist.product.stock_status.replace(
                                        '_',
                                        ' ',
                                    )
                                }}
                            </Badge>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Empty State -->
            <div
                v-if="wishlists.length === 0"
                class="rounded-xl border bg-card p-8 text-center"
            >
                <Heart class="mx-auto mb-2 size-8 text-muted-foreground/60" />
                <p class="font-medium text-foreground">No wishlists found</p>
                <p class="mt-1 text-xs text-muted-foreground">
                    Try adjusting your search criteria.
                </p>
            </div>
        </div>
    </div>
</template>
