<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';
import AppLogoIcon from '../AppLogoIcon.vue';

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
            <AppLogoIcon />
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
