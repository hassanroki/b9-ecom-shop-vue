<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import ShopLayout from '@/layouts/shop/ShopLayout.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: ShopLayout,
});

defineProps<{
    status?: string;
}>();
</script>

<template>

    <Head title="Forgot password">
        <meta name="description" content="Enter your email to receive a password reset link" />
    </Head>

    <div class="flex min-h-[75vh] items-center justify-center bg-gray-50 px-4 py-16">
        <div class="w-full max-w-md">
            <!-- Brand mark -->
            <div class="mb-8 flex flex-col items-center gap-2 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-shop-primary-600">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Forgot password?</h1>
                <p class="text-sm text-gray-500">Enter your email to receive a password reset link</p>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
                <div v-if="status"
                    class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700">
                    {{ status }}
                </div>

                <Form v-bind="email.form()" v-slot="{ errors, processing }" class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-sm font-medium text-gray-700">
                            Email address
                        </label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute top-1/2 left-3.5 h-5 w-5 -translate-y-1/2 text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input id="email" type="email" name="email" autocomplete="off" autofocus
                                placeholder="you@example.com"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none transition-shadow placeholder:text-gray-400 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20" />
                        </div>
                        <InputError :message="errors.email" />
                    </div>

                    <button type="submit" :disabled="processing" data-test="email-password-reset-link-button"
                        class="mt-1 flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-shop-primary-600 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-shop-primary-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <svg v-if="processing" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        Email password reset link
                    </button>
                </Form>
            </div>

            <p class="mt-6 text-center text-sm text-gray-500">
                Or, return to
                <TextLink :href="login()" class="font-semibold text-shop-primary-600 hover:text-shop-primary-700">
                    log in
                </TextLink>
            </p>
        </div>
    </div>
</template>
