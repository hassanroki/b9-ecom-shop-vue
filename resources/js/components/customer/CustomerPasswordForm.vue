<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import password from '@/routes/customer/password';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { useShopUi } from '@/composables/shop/useShopUi';

const { showToast } = useShopUi();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function submit(): void {
    form.put(password.update().url, {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Password updated successfully.');
            form.reset();
        },
        onError: () => {
            showToast('Please check the form for errors.');
        },
    });
}
</script>

<template>
    <div class="border-gray-150 rounded-2xl border bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-700">Change Password</h3>
        <p class="mt-1 text-sm text-gray-500">
            Enter your current password and choose a new one.
        </p>

        <form class="mt-6 max-w-md space-y-5" @submit.prevent="submit">
            <div>
                <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Current Password
                </label>
                <PasswordInput id="current_password" v-model="form.current_password" autocomplete="current-password"
                    required
                    class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition-shadow placeholder:text-gray-400 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20" />
                <InputError :message="form.errors.current_password" />
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                    New Password
                </label>
                <PasswordInput id="password" v-model="form.password" autocomplete="new-password" required
                    class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition-shadow placeholder:text-gray-400 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20" />
                <InputError :message="form.errors.password" />
            </div>

            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Confirm New Password
                </label>
                <PasswordInput id="password_confirmation" v-model="form.password_confirmation"
                    autocomplete="new-password" required
                    class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition-shadow placeholder:text-gray-400 focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20" />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <button type="submit" :disabled="form.processing"
                class="flex h-11 items-center justify-center gap-2 rounded-xl bg-shop-primary-600 px-6 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-shop-primary-700 disabled:cursor-not-allowed disabled:opacity-60">
                <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                </svg>
                {{ form.processing ? 'Updating…' : 'Update Password' }}
            </button>
        </form>
    </div>
</template>
