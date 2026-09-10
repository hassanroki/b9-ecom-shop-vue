<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import ShopLayout from '@/layouts/shop/ShopLayout.vue';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: ShopLayout,
});
</script>

<template>

    <Head title="Register">
        <meta name="description" content="Create your ShopBD account." />
    </Head>

    <div class="flex min-h-[75vh] items-center justify-center bg-gray-50 px-4 py-16">
        <div class="w-full max-w-md">
            <!-- Brand mark -->
            <div class="mb-8 flex flex-col items-center gap-2 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-shop-primary-600">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Create your account</h1>
                <p class="text-sm text-gray-500">Join ShopBD and start shopping</p>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
                <Form v-bind="store.form()" :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }" class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label for="name" class="text-sm font-medium text-gray-700">
                            Full name
                        </label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute top-1/2 left-3.5 h-5 w-5 -translate-y-1/2 text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <input id="name" type="text" required autofocus :tabindex="1" autocomplete="name"
                                name="name" placeholder="John Doe"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none transition-shadow placeholder:text-gray-400 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20" />
                        </div>
                        <InputError :message="errors.name" />
                    </div>

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
                            <input id="email" type="email" required :tabindex="2" autocomplete="email" name="email"
                                placeholder="you@example.com"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none transition-shadow placeholder:text-gray-400 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20" />
                        </div>
                        <InputError :message="errors.email" />
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute top-1/2 left-3.5 z-10 h-5 w-5 -translate-y-1/2 text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
                            </svg>
                            <PasswordInput id="password" required :tabindex="3" autocomplete="new-password"
                                name="password" placeholder="••••••••" :passwordrules="passwordRules"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none transition-shadow placeholder:text-gray-400 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20" />
                        </div>
                        <InputError :message="errors.password" />
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="password_confirmation" class="text-sm font-medium text-gray-700">
                            Confirm password
                        </label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute top-1/2 left-3.5 z-10 h-5 w-5 -translate-y-1/2 text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
                            </svg>
                            <PasswordInput id="password_confirmation" required :tabindex="4" autocomplete="new-password"
                                name="password_confirmation" placeholder="••••••••" :passwordrules="passwordRules"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-11 pr-4 text-sm text-gray-700 outline-none transition-shadow placeholder:text-gray-400 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20" />
                        </div>
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <button type="submit" tabindex="5" :disabled="processing" data-test="register-user-button"
                        class="mt-1 flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-shop-primary-600 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-shop-primary-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <svg v-if="processing" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        Create account
                    </button>
                </Form>
            </div>

            <p class="mt-6 text-center text-sm text-gray-500">
                Already have an account?
                <TextLink :href="login()" class="font-semibold text-shop-primary-600 hover:text-shop-primary-700"
                    :tabindex="6">
                    Log in
                </TextLink>
            </p>
        </div>
    </div>
</template>
