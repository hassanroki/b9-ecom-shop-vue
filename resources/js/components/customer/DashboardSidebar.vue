<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    user: any;
    activeTab: string;
}>();

const emit = defineEmits<{
    (e: 'update:activeTab', tab: string): void;
}>();

const tabs = [
    {
        key: 'overview',
        label: 'Overview',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    },
    {
        key: 'orders',
        label: 'My Orders',
        icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
    },
    {
        key: 'profile',
        label: 'My Profile',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    },
    {
        key: 'settings',
        label: 'Settings',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    },
];
</script>

<template>
    <aside class="mb-6 space-y-2 md:col-span-1 md:mb-0">
        <div class="border-gray-150 space-y-1 rounded-2xl border bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center gap-3 border-b border-gray-100 px-3 py-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-shop-primary-50 text-lg font-semibold text-shop-primary-600">
                    {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div class="overflow-hidden">
                    <h4 class="truncate font-semibold text-gray-700">
                        {{ user.name }}
                    </h4>
                    <p class="truncate text-xs text-gray-400 capitalize">
                        {{ user.role }} Account
                    </p>
                </div>
            </div>

            <button v-for="tab in tabs" :key="tab.key" type="button" @click="emit('update:activeTab', tab.key)"
                class="flex w-full cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition duration-150"
                :class="activeTab === tab.key
                        ? 'bg-shop-primary-50 text-shop-primary-600'
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700'
                    ">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="tab.icon" />
                </svg>
                {{ tab.label }}
            </button>

            <Link href="/logout" method="post" as="button"
                class="flex w-full cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-600 transition duration-150 hover:bg-red-50 hover:text-red-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Log Out
            </Link>
        </div>
    </aside>
</template>
