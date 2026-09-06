<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import ShopLogo from '@/components/shop/ShopLogo.vue';
import { useShopCart } from '@/composables/shop/useShopCart';
import { useShopCatalog } from '@/composables/shop/useShopCatalog';
import { useShopUi } from '@/composables/shop/useShopUi';
import { useShopWishlist } from '@/composables/shop/useShopWishlist';
import { home, login } from '@/routes';
import customer from '@/routes/customer';
import shop from '@/routes/shop';
import category from '@/routes/category';
import brand from '@/routes/brand';

interface SuggestionCategory {
    name: string;
    slug: string;
    url: string;
}

interface SuggestionProduct {
    id: number;
    name: string;
    slug: string;
    price: number;
    img: string | null;
    url: string;
}

const page = usePage();
const { openMobileMenu } = useShopUi();
const { cartQty } = useShopCart();
const { wishCount } = useShopWishlist();
const { search, setSearch } = useShopCatalog();

const isShopPage = computed(() => page.component === 'shop/Shop');
const isHomePage = computed(() => page.component === 'shop/Home');
const isCartPage = computed(() => page.component === 'shop/Cart');
const isWishlistPage = computed(() => page.component === 'shop/Wishlist');
const isCheckoutPage = computed(() => page.component === 'shop/Checkout');

const showMobileSearch = ref(false);
const searchQuery = ref('');
const suggestions = ref<{
    categories: SuggestionCategory[];
    products: SuggestionProduct[];
}>({
    categories: [],
    products: [],
});
const isLoading = ref(false);
const showDropdown = ref(false);

let debounceTimeout: ReturnType<typeof setTimeout> | null = null;

const isAuthenticated = computed(() => !!page.props.auth?.user);
const accountHref = computed(() =>
    isAuthenticated.value ? customer.dashboard() : login(),
);

function toggleMobileSearch(): void {
    showMobileSearch.value = !showMobileSearch.value;
}

// (Debounced)
function handleSearchInput(event: Event): void {
    const val = (event.target as HTMLInputElement).value;
    searchQuery.value = val;

    if (isShopPage.value) {
        setSearch(val);
    }

    if (debounceTimeout) clearTimeout(debounceTimeout);

    if (val.trim().length < 2) {
        suggestions.value = { categories: [], products: [] };
        showDropdown.value = false;
        return;
    }

    debounceTimeout = setTimeout(async () => {
        isLoading.value = true;
        try {
            const res = await fetch(
                `/api/search-suggestions?q=${encodeURIComponent(val)}`,
            );
            if (res.ok) {
                suggestions.value = await res.json();
                showDropdown.value = true;
            }
        } catch (e) {
            console.error('Search suggestion error:', e);
        } finally {
            isLoading.value = false;
        }
    }, 300);
}

// Enter to redirect
function submitSearch(): void {
    if (!searchQuery.value.trim()) return;
    showDropdown.value = false;
    router.get(shop.index(), { search: searchQuery.value.trim() });
}

function closeDropdown(): void {
    // (delay)
    setTimeout(() => {
        showDropdown.value = false;
    }, 250);
}
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-gray-100 bg-[#FFF5ED]/95 backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <button type="button"
                        class="-ml-1 inline-flex h-11 w-11 items-center justify-center rounded-lg text-[#737373] hover:bg-black/5 focus:ring-2 focus:ring-[#87E64B] focus:outline-none lg:hidden"
                        @click="openMobileMenu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <ShopLogo />
                </div>

                <nav class="hidden h-16 items-center gap-1 lg:flex" aria-label="Primary">
                    <Link :href="home()"
                        class="group relative flex h-full items-center px-4 text-sm font-medium text-[#737373] hover:text-[#B9B9B9]"
                        :class="isHomePage ? 'text-gray-700' : ''">
                        Home
                        <span class="absolute bottom-0 left-0 h-0.75 bg-[#87E64B] transition-all duration-300" :class="isHomePage ? 'w-full' : 'w-0 group-hover:w-full'
                            "></span>
                    </Link>

                    <Link :href="shop.index()"
                        class="group relative flex h-full items-center px-4 text-sm font-medium text-[#737373] hover:text-[#B9B9B9]"
                        :class="isShopPage ? 'text-gray-700' : ''">
                        Shop
                        <span class="absolute bottom-0 left-0 h-0.75 bg-[#87E64B] transition-all duration-300" :class="isShopPage ? 'w-full' : 'w-0 group-hover:w-full'
                            "></span>
                    </Link>

                    <Link :href="category.list()"
                        class="group relative flex h-full items-center px-4 text-sm font-medium text-[#737373] hover:text-[#B9B9B9]"
                        :class="page.component === 'shop/Category' ? 'text-gray-700' : ''">
                        Categories
                        <span class="absolute bottom-0 left-0 h-0.75 bg-[#87E64B] transition-all duration-300"
                            :class="page.component === 'shop/Category' ? 'w-full' : 'w-0 group-hover:w-full'"></span>
                    </Link>

                    <Link :href="brand.list()"
                        class="group relative flex h-full items-center px-4 text-sm font-medium text-[#737373] hover:text-[#B9B9B9]"
                        :class="page.component === 'shop/Brand' ? 'text-gray-700' : ''">
                        Brands
                        <span class="absolute bottom-0 left-0 h-0.75 bg-[#87E64B] transition-all duration-300"
                            :class="page.component === 'shop/Brand' ? 'w-full' : 'w-0 group-hover:w-full'"></span>
                    </Link>
                </nav>

                <!-- Desktop Search Bar With Dropdown Suggestions -->
                <div class="relative hidden max-w-md flex-1 md:block">
                    <form @submit.prevent="submitSearch">
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#737373]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                </svg>
                            </span>
                            <input id="searchDesktop" type="search" :value="searchQuery || (isShopPage ? search : '')
                                " placeholder="Search for products…"
                                class="w-full rounded-lg border border-gray-300 bg-white/50 py-2.5 pr-4 pl-10 text-sm text-gray-700 transition placeholder:text-gray-400 focus:border-[#87E64B] focus:bg-white focus:ring-2 focus:ring-[#87E64B] focus:outline-none"
                                autocomplete="off" @input="handleSearchInput" @focus="showDropdown = true"
                                @blur="closeDropdown" @keydown.enter="submitSearch" />
                        </div>
                    </form>

                    <!-- Suggestions Dropdown Menu -->
                    <div v-if="
                        showDropdown &&
                        (suggestions.categories.length > 0 ||
                            suggestions.products.length > 0 ||
                            isLoading)
                    "
                        class="absolute top-full right-0 left-0 z-50 mt-1 max-h-96 overflow-y-auto rounded-xl border border-gray-100 bg-white p-2 shadow-xl">
                        <div v-if="isLoading" class="p-3 text-center text-xs text-gray-500">
                            Searching...
                        </div>

                        <template v-else>
                            <!-- Category Suggestions -->
                            <div v-if="suggestions.categories.length > 0" class="mb-2">
                                <div class="px-3 py-1 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                                    Categories
                                </div>
                                <Link v-for="cat in suggestions.categories" :key="cat.slug" :href="cat.url"
                                    class="flex items-center justify-between rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                    @click="showDropdown = false">
                                    <span>{{ cat.name }}</span>
                                    <span class="text-xs text-gray-400">Category</span>
                                </Link>
                            </div>

                            <!-- Product Suggestions -->
                            <div v-if="suggestions.products.length > 0">
                                <div class="px-3 py-1 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                                    Products
                                </div>
                                <Link v-for="prod in suggestions.products" :key="prod.id" :href="prod.url"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                    @click="showDropdown = false">
                                    <img v-if="prod.img" :src="prod.img" :alt="prod.name"
                                        class="h-9 w-9 rounded-md object-cover" />
                                    <div class="flex-1 overflow-hidden">
                                        <div class="truncate text-sm font-medium text-gray-700">
                                            {{ prod.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ৳ {{ prod.price }}
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Right Action Icons (Account, Wishlist, Cart) -->
                <div class="flex items-center gap-1">
                    <button type="button"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-[#737373] hover:bg-black/5 focus:ring-2 focus:ring-[#87E64B] focus:outline-none md:hidden"
                        @click="toggleMobileSearch">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                        </svg>
                    </button>

                    <Link :href="accountHref"
                        class="hidden h-11 w-11 items-center justify-center rounded-lg text-[#737373] hover:bg-black/5 focus:ring-2 focus:ring-[#87E64B] focus:outline-none sm:inline-flex">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </Link>

                    <Link :href="shop.wishlist()"
                        class="relative inline-flex h-11 w-11 items-center justify-center rounded-lg focus:ring-2 focus:ring-[#87E64B] focus:outline-none"
                        :class="isWishlistPage
                            ? 'bg-red-50 text-red-600'
                            : 'text-[#737373] hover:bg-black/5'
                            ">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span
                            class="absolute -top-0.5 -right-0.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-shop-accent-500 px-1 text-xs font-semibold text-white">
                            {{ wishCount }}
                        </span>
                    </Link>

                    <Link :href="shop.cart()"
                        class="relative inline-flex h-11 w-11 items-center justify-center rounded-lg focus:ring-2 focus:ring-[#87E64B] focus:outline-none"
                        :class="isCartPage || isCheckoutPage
                            ? 'bg-shop-primary-50 text-shop-primary-600'
                            : 'text-[#737373] hover:bg-black/5'
                            ">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span
                            class="absolute -top-0.5 -right-0.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-shop-accent-500 px-1 text-xs font-semibold text-white">
                            {{ cartQty }}
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Mobile Search Bar With Dropdown Suggestions -->
            <div v-show="showMobileSearch" class="relative border-t border-gray-100/50 pt-2 pb-3 md:hidden">
                <form @submit.prevent="submitSearch">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#737373]">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                            </svg>
                        </span>
                        <input id="searchMobileHeader" type="search" :value="searchQuery || (isShopPage ? search : '')"
                            placeholder="Search for products…"
                            class="w-full rounded-lg border border-gray-300 bg-white/80 py-2.5 pr-4 pl-10 text-sm text-gray-700 focus:border-[#87E64B] focus:bg-white focus:ring-2 focus:ring-[#87E64B] focus:outline-none"
                            autocomplete="off" @input="handleSearchInput" @focus="showDropdown = true"
                            @blur="closeDropdown" @keydown.enter="submitSearch" />
                    </div>
                </form>

                <!-- Mobile Suggestions Dropdown -->
                <div v-if="
                    showDropdown &&
                    (suggestions.categories.length > 0 ||
                        suggestions.products.length > 0 ||
                        isLoading)
                "
                    class="absolute top-full right-0 left-0 z-50 mt-1 max-h-80 overflow-y-auto rounded-xl border border-gray-100 bg-white p-2 shadow-xl">
                    <div v-if="isLoading" class="p-3 text-center text-xs text-gray-500">
                        Searching...
                    </div>

                    <template v-else>
                        <!-- Mobile Category Suggestions -->
                        <div v-if="suggestions.categories.length > 0" class="mb-2">
                            <div class="px-3 py-1 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                                Categories
                            </div>
                            <Link v-for="cat in suggestions.categories" :key="cat.slug" :href="cat.url"
                                class="flex items-center justify-between rounded-lg px-3 py-2 text-sm text-gray-700 active:bg-gray-100"
                                @click="showDropdown = false">
                                <span>{{ cat.name }}</span>
                                <span class="text-xs text-gray-400">Category</span>
                            </Link>
                        </div>

                        <!-- Mobile Product Suggestions -->
                        <div v-if="suggestions.products.length > 0">
                            <div class="px-3 py-1 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                                Products
                            </div>
                            <Link v-for="prod in suggestions.products" :key="prod.id" :href="prod.url"
                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 active:bg-gray-100"
                                @click="showDropdown = false">
                                <img v-if="prod.img" :src="prod.img" :alt="prod.name"
                                    class="h-9 w-9 rounded-md object-cover" />
                                <div class="flex-1 overflow-hidden">
                                    <div class="truncate text-sm font-medium text-gray-700">
                                        {{ prod.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ৳ {{ prod.price }}
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>
