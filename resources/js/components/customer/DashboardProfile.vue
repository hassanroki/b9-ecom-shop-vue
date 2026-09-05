<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { formatDate } from '@/lib/customer/orderStatus';

defineProps<{
    user: any;
}>();
</script>

<template>
    <div class="space-y-6">
        <div class="border-gray-150 rounded-2xl border bg-white p-6 shadow-sm">
            <h3 class="border-b border-gray-100 pb-3 text-lg font-bold text-gray-700">
                My Profile Details
            </h3>

            <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-6 text-sm sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Full Name</p>
                    <p class="mt-1.5 mb-1 text-base font-bold text-gray-700">{{ user.name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Email Address</p>
                    <p class="mt-1.5 mb-1 text-base font-medium text-gray-700">{{ user.email }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Phone Number</p>
                    <p class="mt-1.5 mb-1 text-base font-medium text-gray-700">{{ user.phone || 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Member Since</p>
                    <p class="mt-1.5 mb-1 text-base font-medium text-gray-700">{{ formatDate(user.created_at) }}</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end border-t border-gray-100 pt-6">
                <Link
                    href="/customer/profile/edit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-shop-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-shop-primary-700"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Edit Account details
                </Link>
            </div>
        </div>
    </div>
</template>
