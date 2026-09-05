<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: '',
        description: '',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div class="mx-auto w-full max-w-sm rounded-[32px] bg-[#FFF5ED] p-8">
        <div
            v-if="status"
            class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700"
        >
            {{ status }}
        </div>

        <!-- Avatar / brand mark -->
        <div class="mb-8 flex justify-center">
            <h2 class="text-xl font-semibold text-stone-800">Login Page</h2>
        </div>
        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label
                        for="email"
                        class="text-sm font-medium text-stone-700"
                    >
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="email@example.com"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        class="h-12 w-full rounded-2xl border-none bg-[#F7DDCB] px-4 text-sm text-stone-700 transition-shadow outline-none focus:ring-2 focus:ring-stone-700/20"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="flex flex-col gap-2">
                    <label
                        for="password"
                        class="text-sm font-medium text-stone-700"
                    >
                        Password
                    </label>
                    <PasswordInput
                        id="password"
                        name="password"
                        placeholder="Password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        class="h-12 w-full rounded-2xl border-none bg-[#F7DDCB] px-4 text-sm text-stone-700 transition-shadow outline-none focus:ring-2 focus:ring-stone-700/20"
                    />
                    <InputError :message="errors.password" />
                </div>

                <button
                    type="submit"
                    :tabindex="3"
                    :disabled="processing"
                    data-test="login-button"
                    class="mt-2 flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-stone-700 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <svg
                        v-if="processing"
                        class="h-4 w-4 animate-spin text-white"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        />
                    </svg>
                    Log In
                </button>
            </div>

            <div class="flex items-center justify-center">
                <TextLink
                    v-if="canResetPassword"
                    :href="request()"
                    class="text-sm text-stone-500 hover:text-stone-700"
                    :tabindex="4"
                >
                    Forgot password?
                </TextLink>
            </div>

            <p class="text-center text-sm text-stone-500">
                Don't have an account?
                <TextLink
                    :href="register()"
                    :tabindex="5"
                    class="font-medium text-stone-800 hover:text-stone-700"
                >
                    Sign up
                </TextLink>
            </p>
        </Form>
    </div>
</template>
