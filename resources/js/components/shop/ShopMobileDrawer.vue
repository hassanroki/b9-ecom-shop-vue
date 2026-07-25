<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useShopCatalog } from '@/composables/shop/useShopCatalog';
import { useShopUi } from '@/composables/shop/useShopUi';
import { home, login } from '@/routes';
import shop from '@/routes/shop';
import customer from '@/routes/customer';

const page = usePage();
const { isMobileMenuOpen, closeMobileMenu } = useShopUi();
const { search, setSearch } = useShopCatalog();

const isShopPage = computed(() => page.component === 'shop/Shop');

// লগিন স্ট্যাটাস চেক
const isAuthenticated = computed(() => !!page.props.auth?.user);

// My Account link href: লগিন থাকলে dashboard, না থাকলে login পেজ
const accountHref = computed(() =>
    isAuthenticated.value ? customer.dashboard() : login()
);

function handleSearchInput(event: Event): void {
    if (!isShopPage.value) {
        return;
    }

    setSearch((event.target as HTMLInputElement).value);
}
</script>

<template>
    <div>
        <!-- ব্যাকড্রপ ওভারলে: লাইট থিমের সাথে মিলিয়ে সফট ডার্কনেস (bg-black/25) -->
        <div
            class="fixed inset-0 z-50 bg-black/25 backdrop-blur-sm transition-opacity duration-300 lg:hidden"
            :class="isMobileMenuOpen ? 'block opacity-100' : 'hidden opacity-0'"
            aria-hidden="true"
            @click="closeMobileMenu"
        />

        <!-- মোবাইল ড্রয়ার: হেডারের মতো প্রফেশনাল #FFF5ED ব্যাকগ্রাউন্ড দেওয়া হয়েছে -->
        <aside
            id="mobileDrawer"
            class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85%] border-r border-gray-200/60 bg-[#FFF5ED] shadow-2xl transition-transform duration-300 ease-in-out lg:hidden"
            :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            role="dialog"
            aria-modal="true"
            aria-label="Menu"
        >
            <!-- হেডার অংশ -->
            <div
                class="flex h-16 items-center justify-between border-b border-gray-200/60 px-5"
            >
                <span
                    class="text-sm font-bold tracking-wider text-[#737373] uppercase"
                >
                    Navigation
                </span>
                <button
                    type="button"
                    aria-label="Close menu"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-[#737373] transition-colors hover:bg-black/5 hover:text-[#B9B9B9] focus:ring-2 focus:ring-[#87E64B] focus:outline-none"
                    @click="closeMobileMenu"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- বডি / নেভিগেশন লিংকসমূহ -->
            <div class="h-[calc(100vh-4rem)] overflow-y-auto p-5">
                <!-- সার্চ বক্স: ক্লিন হোয়াইট ইনপুট এবং লাইম গ্রিন ফোকাস বর্ডার -->
                <label for="searchMobile" class="sr-only"
                    >Search products</label
                >
                <div class="relative mb-6">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-[#737373]"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"
                            />
                        </svg>
                    </span>
                    <input
                        id="searchMobile"
                        type="search"
                        :value="isShopPage ? search : ''"
                        placeholder="Search products..."
                        class="w-full rounded-lg border border-gray-300 bg-white/80 py-2.5 pr-4 pl-10 text-sm text-gray-900 placeholder-gray-400 transition-all focus:border-[#87E64B] focus:bg-white focus:ring-2 focus:ring-[#87E64B] focus:outline-none"
                        @input="handleSearchInput"
                    />
                </div>

                <!-- মেনু লিংকস গ্রুপ -->
                <nav class="flex flex-col gap-1" aria-label="Mobile">
                    <Link
                        :href="home()"
                        class="rounded-lg px-4 py-3 text-sm font-medium text-[#737373] transition-all hover:bg-black/5 hover:text-[#B9B9B9]"
                        @click="closeMobileMenu"
                    >
                        Home
                    </Link>

                    <!-- অ্যাক্টিভ পেজে লাইম গ্রিন (#87E64B) ব্যাকগ্রাউন্ড ও ডার্ক টেক্সট দেওয়া হয়েছে -->
                    <Link
                        :href="shop.index()"
                        class="rounded-lg px-4 py-3 text-sm font-semibold transition-all"
                        :class="
                            isShopPage
                                ? 'bg-[#87E64B] text-gray-900 shadow-sm'
                                : 'text-[#737373] hover:bg-black/5 hover:text-[#B9B9B9]'
                        "
                        @click="closeMobileMenu"
                    >
                        Shop
                    </Link>

                    <Link
                        href="/#categories"
                        class="rounded-lg px-4 py-3 text-sm font-medium text-[#737373] transition-all hover:bg-black/5 hover:text-[#B9B9B9]"
                        @click="closeMobileMenu"
                    >
                        Categories
                    </Link>

                    <Link
                        href="/#bestselling"
                        class="rounded-lg px-4 py-3 text-sm font-medium text-[#737373] transition-all hover:bg-black/5 hover:text-[#B9B9B9]"
                        @click="closeMobileMenu"
                    >
                        Best Selling
                    </Link>

                    <Link
                        href="/#newcollection"
                        class="rounded-lg px-4 py-3 text-sm font-medium text-[#737373] transition-all hover:bg-black/5 hover:text-[#B9B9B9]"
                        @click="closeMobileMenu"
                    >
                        New Collection
                    </Link>

                    <!-- ডিভাইডার লাইন -->
                    <div class="my-4 border-t border-gray-200/60" />

                    <Link
                        :href="accountHref"
                        class="flex items-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-[#737373] transition-all hover:bg-black/5 hover:text-[#B9B9B9]"
                        @click="closeMobileMenu"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                        My Account
                    </Link>
                </nav>
            </div>
        </aside>
    </div>
</template>
