<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    index as confirmOptions,
    store as confirmStore,
} from '@/actions/Laravel/Passkeys/Http/Controllers/PasskeyConfirmationController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Confirm password',
                href: store(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Confirm password" />

    <h1 class="sr-only">Confirm password</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Confirm password"
            description="This is a secure area. Please confirm your password before continuing."
        />

        <div class="max-w-md">
            <PasskeyVerify
                :routes="{
                    options: confirmOptions(),
                    submit: confirmStore(),
                }"
                label="Confirm with passkey"
                loading-label="Confirming..."
                separator="Or confirm with password"
            />

            <Form
                v-bind="store.form()"
                reset-on-success
                v-slot="{ errors, processing }"
                class="mt-6 space-y-6"
            >
                <div class="grid gap-2">
                    <Label htmlFor="password">Password</Label>
                    <PasswordInput
                        id="password"
                        name="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Button
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="processing" />
                        Confirm password
                    </Button>
                </div>
            </Form>
        </div>
    </div>
</template>
