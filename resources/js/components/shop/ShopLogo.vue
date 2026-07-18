<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

const { variant = 'light' } = defineProps<{
    variant?: 'light' | 'dark';
}>();
</script>

<template>
    <Link
        :href="home()"
        class="group flex items-center gap-2.5"
        aria-label="ShopEase home"
    >
        <!-- আইকন বক্স: নতুন থিমের সাথে ম্যাচ করে bg-[#87E64B] এবং টেক্সট কালার ডার্ক গ্রে করা হয়েছে -->
        <span
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#87E64B] text-gray-900 shadow-sm transition-transform duration-200 group-hover:scale-105"
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
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                />
            </svg>
        </span>

        <!-- টেক্সট লোগো: লাইট ভ্যারিয়েন্টে মূল টেক্সট কালার আপনার দেওয়া #737373 এবং হাইলাইট #87E64B -->
        <span
            class="text-xl font-extrabold tracking-tight"
            :class="variant === 'dark' ? 'text-white' : 'text-[#737373]'"
        >
            Shop<span class="text-[#87E64B]">BD</span>
        </span>
    </Link>
</template>
