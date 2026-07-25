<template>
    <section
        class="border-b border-gray-100 bg-[#FFF5ED]/30 py-6 lg:py-10"
        aria-label="Why shop with us"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- মোবাইলে ২ কলাম (grid-cols-2) এবং মাঝারি বা বড় স্ক্রিনে ৪ কলাম (md:grid-cols-4) করা হয়েছে -->
            <div
                class="grid grid-cols-2 gap-x-3 gap-y-5 sm:gap-6 md:grid-cols-4 md:gap-8"
            >
                <!-- ফিচার ১: ডেলিভারি -->
                <div
                    class="group flex flex-col items-start gap-2.5 rounded-xl p-2 transition-all duration-300 hover:bg-white hover:shadow-sm sm:flex-row sm:gap-4 sm:p-3"
                >
                    <span
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#87E64B]/10 text-gray-800 transition-colors group-hover:bg-[#87E64B] sm:h-12 sm:w-12"
                    >
                        <svg
                            class="h-5 w-5 transition-transform duration-300 group-hover:scale-110 sm:h-6 sm:w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H3m10 11h2.5a1 1 0 001-.7l1.4-4.2a1 1 0 00-.95-1.3H13"
                            />
                        </svg>
                    </span>
                    <div class="space-y-0.5">
                        <h3
                            class="line-clamp-1 text-xs font-bold tracking-tight text-gray-900 sm:line-clamp-none sm:text-sm"
                        >
                            Nationwide Delivery
                        </h3>
                        <p
                            class="text-[11px] font-medium text-[#737373] sm:text-xs"
                        >
                            Inside &amp; outside Dhaka
                        </p>
                    </div>
                </div>

                <!-- ফিচার ২: ক্যাশ অন ডেলিভারি -->
                <div
                    class="group flex flex-col items-start gap-2.5 rounded-xl p-2 transition-all duration-300 hover:bg-white hover:shadow-sm sm:flex-row sm:gap-4 sm:p-3"
                >
                    <span
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#87E64B]/10 text-gray-800 transition-colors group-hover:bg-[#87E64B] sm:h-12 sm:w-12"
                    >
                        <svg
                            class="h-5 w-5 transition-transform duration-300 group-hover:scale-110 sm:h-6 sm:w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m0-6c1.11 0 2.08.402 2.599 1M12 18c-1.11 0-2.08-.402-2.599-1"
                            />
                        </svg>
                    </span>
                    <div class="space-y-0.5">
                        <h3
                            class="line-clamp-1 text-xs font-bold tracking-tight text-gray-900 sm:line-clamp-none sm:text-sm"
                        >
                            Cash on Delivery
                        </h3>
                        <p
                            class="text-[11px] font-medium text-[#737373] sm:text-xs"
                        >
                            Pay when you receive
                        </p>
                    </div>
                </div>

                <!-- ফিচার ৩: সিকিউর পেমেন্ট -->
                <div
                    class="group flex flex-col items-start gap-2.5 rounded-xl p-2 transition-all duration-300 hover:bg-white hover:shadow-sm sm:flex-row sm:gap-4 sm:p-3"
                >
                    <span
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#87E64B]/10 text-gray-800 transition-colors group-hover:bg-[#87E64B] sm:h-12 sm:w-12"
                    >
                        <svg
                            class="h-5 w-5 transition-transform duration-300 group-hover:scale-110 sm:h-6 sm:w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                            />
                        </svg>
                    </span>
                    <div class="space-y-0.5">
                        <h3
                            class="line-clamp-1 text-xs font-bold tracking-tight text-gray-900 sm:line-clamp-none sm:text-sm"
                        >
                            Secure Payment
                        </h3>
                        <p
                            class="text-[11px] font-medium text-[#737373] sm:text-xs"
                        >
                            SSLCommerz protected
                        </p>
                    </div>
                </div>

                <!-- ফিচার ৪: জেনুইন প্রোডাক্ট -->
                <div
                    class="group flex flex-col items-start gap-2.5 rounded-xl p-2 transition-all duration-300 hover:bg-white hover:shadow-sm sm:flex-row sm:gap-4 sm:p-3"
                >
                    <span
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#87E64B]/10 text-gray-800 transition-colors group-hover:bg-[#87E64B] sm:h-12 sm:w-12"
                    >
                        <svg
                            class="h-5 w-5 transition-transform duration-300 group-hover:scale-110 sm:h-6 sm:w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M18.364 5.636a9 9 0 010 12.728m-3.536-3.536a4 4 0 010-5.656M12 12h.01"
                            />
                        </svg>
                    </span>
                    <div class="space-y-0.5">
                        <h3
                            class="line-clamp-1 text-xs font-bold tracking-tight text-gray-900 sm:line-clamp-none sm:text-sm"
                        >
                            Genuine Products
                        </h3>
                        <p
                            class="text-[11px] font-medium text-[#737373] sm:text-xs"
                        >
                            Quality guaranteed
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
