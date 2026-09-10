<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { formatDate } from '@/lib/customer/orderStatus';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import InputError from '@/components/InputError.vue';
import { useShopUi } from '@/composables/shop/useShopUi';
import profile from '@/routes/customer/profile';

const props = defineProps<{
    user: any;
}>();

const { showToast } = useShopUi();

const isOpen = ref(false);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone ?? '',
});

function submit(): void {
    form.patch(profile.update().url, {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Profile updated successfully.');
            isOpen.value = false;
        },
        onError: () => {
            showToast('Please check the form for errors.');
        },
    });
}
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
                <Dialog v-model:open="isOpen">
                    <DialogTrigger as-child>
                        <Button
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-shop-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-shop-primary-700"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Account details
                        </Button>
                    </DialogTrigger>

                    <DialogContent>
                        <form @submit.prevent="submit">
                            <DialogHeader class="space-y-1.5">
                                <DialogTitle>Edit Account Details</DialogTitle>
                                <DialogDescription>
                                    Update your name, email and phone number.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="mt-6 space-y-4">
                                <div>
                                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                                        Full Name
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition-shadow focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20"
                                    />
                                    <InputError :message="form.errors.name" />
                                </div>

                                <div>
                                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                                        Email Address
                                    </label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition-shadow focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20"
                                    />
                                    <InputError :message="form.errors.email" />
                                </div>

                                <div>
                                    <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">
                                        Phone Number
                                    </label>
                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        placeholder="e.g. 01XXXXXXXXX"
                                        class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition-shadow focus:border-shop-primary-600 focus:ring-2 focus:ring-shop-primary-600/20"
                                    />
                                    <InputError :message="form.errors.phone" />
                                </div>
                            </div>

                            <DialogFooter class="mt-8 gap-2">
                                <DialogClose as-child>
                                    <Button type="button" variant="secondary">Cancel</Button>
                                </DialogClose>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-shop-primary-600 text-white hover:bg-shop-primary-700"
                                >
                                    {{ form.processing ? 'Saving…' : 'Save changes' }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    </div>
</template>
