<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: '',
        description: '',
    },
});
</script>

<template>
    <Head title="Register" />

    <div class="mx-auto w-full max-w-sm rounded-[32px] bg-[#FFF5ED] p-8">
        <!-- Avatar / brand mark -->
        <div class="mb-8 flex justify-center">
            <h2 class="text-xl font-semibold text-stone-800">Register Page</h2>
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label
                        for="name"
                        class="text-sm font-medium text-stone-700"
                    >
                        Name
                    </label>
                    <input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Full name"
                        class="h-12 w-full rounded-2xl border-none bg-[#F7DDCB] px-4 text-sm text-stone-900 transition-shadow outline-none placeholder:text-stone-500 focus:ring-2 focus:ring-stone-900/20"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="flex flex-col gap-2">
                    <label
                        for="email"
                        class="text-sm font-medium text-stone-700"
                    >
                        Email address
                    </label>
                    <input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                        class="h-12 w-full rounded-2xl border-none bg-[#F7DDCB] px-4 text-sm text-stone-900 transition-shadow outline-none placeholder:text-stone-500 focus:ring-2 focus:ring-stone-900/20"
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
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                        :passwordrules="passwordRules"
                        class="h-12 w-full rounded-2xl border-none bg-[#F7DDCB] px-4 text-sm text-stone-900 transition-shadow outline-none focus:ring-2 focus:ring-stone-900/20"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex flex-col gap-2">
                    <label
                        for="password_confirmation"
                        class="text-sm font-medium text-stone-700"
                    >
                        Confirm password
                    </label>
                    <PasswordInput
                        id="password_confirmation"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                        :passwordrules="passwordRules"
                        class="h-12 w-full rounded-2xl border-none bg-[#F7DDCB] px-4 text-sm text-stone-900 transition-shadow outline-none focus:ring-2 focus:ring-stone-900/20"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <button
                    type="submit"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                    class="mt-2 flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-stone-900 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:cursor-not-allowed disabled:opacity-60"
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
                    Create account
                </button>
            </div>

            <div class="text-center text-sm text-stone-500">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="font-medium text-stone-800 hover:text-stone-900"
                    :tabindex="6"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </div>
</template>
