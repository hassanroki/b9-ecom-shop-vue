<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/layouts/shop/ShopLayout.vue';
import ShopPageBreadcrumb from '@/components/shop/ShopPageBreadcrumb.vue';
import { update } from '@/routes/customer/profile';

const props = defineProps<{
    user: {
        name: string;
        email: string;
        phone: string | null;
    };
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone ?? '',
});

function submit(): void {
    form.patch(update.url(), {
        preserveScroll: true,
    });
}
</script>

<template>
    <ShopLayout>
        <Head title="Edit Profile" />

        <div class="min-h-screen bg-gray-50/50 py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <ShopPageBreadcrumb
                    :items="[
                        { label: 'Dashboard', href: '/customer/dashboard' },
                        { label: 'Edit Profile' },
                    ]"
                />

                <div
                    class="border-gray-150 mt-6 rounded-2xl border bg-white p-6 shadow-sm"
                >
                    <h3
                        class="border-b border-gray-100 pb-3 text-lg font-bold text-gray-900"
                    >
                        Edit Account Details
                    </h3>

                    <form @submit.prevent="submit" class="mt-6 space-y-5">
                        <div>
                            <label
                                for="name"
                                class="text-sm font-medium text-gray-700"
                                >Full Name</label
                            >
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="focus:border-shop-primary-500 focus:ring-shop-primary-500/15 mt-1.5 h-11 w-full rounded-lg border border-gray-200 px-3.5 text-sm outline-none focus:ring-2"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="email"
                                class="text-sm font-medium text-gray-700"
                                >Email Address</label
                            >
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="focus:border-shop-primary-500 focus:ring-shop-primary-500/15 mt-1.5 h-11 w-full rounded-lg border border-gray-200 px-3.5 text-sm outline-none focus:ring-2"
                            />
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="phone"
                                class="text-sm font-medium text-gray-700"
                                >Phone Number</label
                            >
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                class="focus:border-shop-primary-500 focus:ring-shop-primary-500/15 mt-1.5 h-11 w-full rounded-lg border border-gray-200 px-3.5 text-sm outline-none focus:ring-2"
                            />
                            <p
                                v-if="form.errors.phone"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <div
                            class="flex justify-end border-t border-gray-100 pt-5"
                        >
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-shop-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-shop-primary-700 disabled:opacity-60"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
