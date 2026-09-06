<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useShopCatalog } from '@/composables/shop/useShopCatalog';
import { useShopUi } from '@/composables/shop/useShopUi';
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
const { isMobileMenuOpen, closeMobileMenu } = useShopUi();
const { search, setSearch } = useShopCatalog();

const isShopPage = computed(() => page.component === 'shop/Shop');
const isCategoryPage = computed(() => page.component === 'shop/Category');
const isBrandPage = computed(() => page.component === 'shop/Brand');

// লগিন স্ট্যাটাস চেক
const isAuthenticated = computed(() => !!page.props.auth?.user);

// My Account link href
const accountHref = computed(() =>
    isAuthenticated.value ? customer.dashboard() : login(),
);

// লাইভ সার্চ এবং ড্রপডাউন স্টেট
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

// ইনপুট টাইপ করার সময় সাজেশন ফেচ করার লজিক (Debounced)
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
            console.error('Mobile search suggestion error:', e);
        } finally {
            isLoading.value = false;
        }
    }, 300);
}

// এন্টার বা সাবমিট চাপলে শপ পেজে রিডাইরেক্ট
function submitSearch(): void {
    if (!searchQuery.value.trim()) return;
    showDropdown.value = false;
    closeMobileMenu();
    router.get(shop.index(), { search: searchQuery.value.trim() });
}

// ড্রপডাউন আইটেমে ক্লিক করলে ড্রয়ার বন্ধ করা
function handleItemClick(): void {
    showDropdown.value = false;
    closeMobileMenu();
}
</script>

<template>
    <div>
        <!-- ব্যাকড্রপ ওভারলে -->
        <div class="fixed inset-0 z-50 bg-black/25 backdrop-blur-sm transition-opacity duration-300 lg:hidden"
            :class="isMobileMenuOpen ? 'block opacity-100' : 'hidden opacity-0'" aria-hidden="true"
            @click="closeMobileMenu" />

        <!-- মোবাইল ড্রয়ার -->
        <aside id="mobileDrawer"
            class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85%] border-r border-gray-200/60 bg-[#FFF5ED] shadow-2xl transition-transform duration-300 ease-in-out lg:hidden"
            :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'" role="dialog" aria-modal="true"
            aria-label="Menu">
            <!-- হেডার অংশ -->
            <div class="flex h-16 items-center justify-between border-b border-gray-200/60 px-5">
                <span class="text-sm font-bold tracking-wider text-[#737373] uppercase">
                    Navigation
                </span>
                <button type="button" aria-label="Close menu"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-[#737373] transition-colors hover:bg-black/5 hover:text-[#B9B9B9] focus:ring-2 focus:ring-[#87E64B] focus:outline-none"
                    @click="closeMobileMenu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- বডি / নেভিগেশন লিংকসমূহ -->
            <div class="h-[calc(100vh-4rem)] overflow-y-auto p-5">
                <!-- মোবাইল সার্চ বক্স এবং লাইভ সাজেশন ড্রপডাউন -->
                <div class="relative mb-6">
                    <form @submit.prevent="submitSearch">
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-[#737373]">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                </svg>
                            </span>
                            <input id="searchMobile" type="search" :value="searchQuery || (isShopPage ? search : '')
                                " placeholder="Search products..."
                                class="w-full rounded-lg border border-gray-300 bg-white/80 py-2.5 pr-4 pl-10 text-sm text-gray-700 placeholder-gray-400 transition-all focus:border-[#87E64B] focus:bg-white focus:ring-2 focus:ring-[#87E64B] focus:outline-none"
                                autocomplete="off" @input="handleSearchInput" @focus="showDropdown = true"
                                @keydown.enter="submitSearch" />
                        </div>
                    </form>

                    <!-- ড্রপডাউন ফলাফল (Mobile) -->
                    <div v-if="
                        showDropdown &&
                        (suggestions.categories.length > 0 ||
                            suggestions.products.length > 0 ||
                            isLoading)
                    "
                        class="absolute top-full right-0 left-0 z-50 mt-2 max-h-80 overflow-y-auto rounded-xl border border-gray-100 bg-white p-2 shadow-2xl">
                        <div v-if="isLoading" class="p-3 text-center text-xs text-gray-500">
                            Searching...
                        </div>

                        <template v-else>
                            <!-- Category Suggestions -->
                            <div v-if="suggestions.categories.length > 0" class="mb-2">
                                <div class="px-3 py-1 text-[10px] font-bold tracking-wider text-gray-400 uppercase">
                                    Categories
                                </div>
                                <Link v-for="cat in suggestions.categories" :key="cat.slug" :href="cat.url"
                                    class="flex items-center justify-between rounded-lg px-3 py-2 text-xs text-gray-700 hover:bg-gray-50"
                                    @click="handleItemClick">
                                    <span>{{ cat.name }}</span>
                                    <span class="text-[10px] text-gray-400">Category</span>
                                </Link>
                            </div>

                            <!-- Product Suggestions -->
                            <div v-if="suggestions.products.length > 0">
                                <div class="px-3 py-1 text-[10px] font-bold tracking-wider text-gray-400 uppercase">
                                    Products
                                </div>
                                <Link v-for="prod in suggestions.products" :key="prod.id" :href="prod.url"
                                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs text-gray-700 hover:bg-gray-50"
                                    @click="handleItemClick">
                                    <img v-if="prod.img" :src="prod.img" :alt="prod.name"
                                        class="h-8 w-8 rounded-md object-cover" />
                                    <div class="flex-1 overflow-hidden">
                                        <div class="truncate font-medium text-gray-700">
                                            {{ prod.name }}
                                        </div>
                                        <div class="text-[10px] text-gray-500">
                                            ৳ {{ prod.price }}
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- মেনু লিংকস গ্রুপ -->
                <nav class="flex flex-col gap-1" aria-label="Mobile">
                    <Link :href="home()"
                        class="rounded-lg px-4 py-3 text-sm font-medium text-[#737373] transition-all hover:bg-black/5 hover:text-[#B9B9B9]"
                        @click="closeMobileMenu">
                        Home
                    </Link>

                    <Link :href="shop.index()" class="rounded-lg px-4 py-3 text-sm font-semibold transition-all" :class="isShopPage
                        ? 'bg-[#87E64B] text-gray-700 shadow-sm'
                        : 'text-[#737373] hover:bg-black/5 hover:text-[#B9B9B9]'
                        " @click="closeMobileMenu">
                        Shop
                    </Link>

                    <Link :href="category.list()" class="rounded-lg px-4 py-3 text-sm font-medium transition-all"
                        :class="isCategoryPage
                            ? 'bg-[#87E64B] text-gray-700 shadow-sm'
                            : 'text-[#737373] hover:bg-black/5 hover:text-[#B9B9B9]'
                            " @click="closeMobileMenu">
                        Categories
                    </Link>

                    <Link :href="brand.list()" class="rounded-lg px-4 py-3 text-sm font-medium transition-all" :class="isBrandPage
                        ? 'bg-[#87E64B] text-gray-700 shadow-sm'
                        : 'text-[#737373] hover:bg-black/5 hover:text-[#B9B9B9]'
                        " @click="closeMobileMenu">
                        Brands
                    </Link>

                    <Link href="/#newcollection"
                        class="rounded-lg px-4 py-3 text-sm font-medium text-[#737373] transition-all hover:bg-black/5 hover:text-[#B9B9B9]"
                        @click="closeMobileMenu">
                        New Collection
                    </Link>

                    <!-- ডিভাইডার লাইন -->
                    <div class="my-4 border-t border-gray-200/60" />

                    <Link :href="accountHref"
                        class="flex items-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-[#737373] transition-all hover:bg-black/5 hover:text-[#B9B9B9]"
                        @click="closeMobileMenu">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        My Account
                    </Link>
                </nav>
            </div>
        </aside>
    </div>
</template>
