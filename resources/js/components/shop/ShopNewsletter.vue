<script setup lang="ts">
import { useShopUi } from '@/composables/shop/useShopUi';

const { showToast } = useShopUi();

function handleSubmit(event: Event): void {
    event.preventDefault();
    showToast('Subscribed! Check your inbox');
}
</script>

<template>
    <!-- আল্ট্রা-মিনিমাল লাইট থিম (সলিড হোয়াইট ও সফট বেস কালার ইনপুট) -->
    <section class="border-y border-neutral-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 md:py-16 lg:px-8">
            <div
                class="flex flex-col items-center gap-8 text-center md:flex-row md:justify-between md:text-left"
            >
                <!-- টেক্সট কন্টেন্ট এরিয়া -->
                <div class="max-w-lg space-y-1.5">
                    <h2
                        class="text-xl font-black tracking-tight text-neutral-900 uppercase md:text-2xl"
                    >
                        Get
                        <span
                            class="mx-1 inline-block rounded bg-neutral-950 px-2 py-0.5 align-middle text-xs font-bold tracking-wide text-[#87E64B] md:text-sm"
                            >10% OFF</span
                        >
                        your first order
                    </h2>
                    <p
                        class="text-xs leading-relaxed font-medium text-neutral-500 md:text-sm"
                    >
                        Subscribe for new arrivals, exclusive deals, and weekly
                        hand-picked collections.
                    </p>
                </div>

                <!-- সাবস্ক্রিপশন ফর্ম -->
                <form
                    class="flex w-full max-w-md flex-col gap-2.5 sm:flex-row sm:items-center"
                    @submit="handleSubmit"
                >
                    <label for="newsletterEmail" class="sr-only"
                        >Email address</label
                    >
                    <div class="relative w-full">
                        <input
                            id="newsletterEmail"
                            type="email"
                            required
                            placeholder="Your email address"
                            class="w-full rounded-xl border border-transparent bg-[#FFF5ED] px-4 py-3 text-sm font-semibold text-neutral-800 placeholder-neutral-400 transition-all focus:border-[#87E64B] focus:bg-[#FFF5ED] focus:ring-1 focus:ring-[#87E64B] focus:outline-none"
                        />
                    </div>

                    <!-- সলিড ডার্ক মিনিমাল বাটন -->
                    <button
                        type="submit"
                        class="shrink-0 rounded-xl bg-neutral-950 px-6 py-3 text-sm font-bold tracking-wider text-white uppercase transition-all duration-200 hover:bg-neutral-800 focus:ring-2 focus:ring-neutral-950 focus:ring-offset-2 focus:ring-offset-white focus:outline-none"
                    >
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
