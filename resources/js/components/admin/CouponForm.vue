<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AdminCoupon, CouponFormData } from '@/types/admin';

type Props = {
    form: InertiaForm<CouponFormData>;
    coupon?: AdminCoupon;
};

defineProps<Props>();

const inputClass =
    'border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-24 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50';
</script>

<template>
    <div class="grid gap-6">
        <div class="grid gap-2">
            <Label for="name">Coupon Name</Label>
            <Input id="name" v-model="form.name" required placeholder="Summer Sale" />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="code">Coupon Code</Label>
            <Input id="code" v-model="form.code" class="uppercase" required placeholder="SUMMER20" />
            <p class="text-xs text-muted-foreground">
                Example: SAVE100, EID25, NEWUSER
            </p>
            <InputError :message="form.errors.code" />
        </div>

        <div class="grid gap-2">
            <Label for="description">Description</Label>
            <textarea id="description" v-model="form.description" rows="4" :class="inputClass"
                placeholder="Short description for this coupon" />
            <InputError :message="form.errors.description" />
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Discount Type</Label>
                <Select v-model="form.type">
                    <SelectTrigger>
                        <SelectValue placeholder="Select Type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="fixed">Fixed Amount</SelectItem>
                        <SelectItem value="percentage">Percentage</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.type" />
            </div>

            <div class="grid gap-2">
                <Label for="value">Discount Value</Label>
                <Input id="value" v-model="form.value" type="number" step="0.01" min="0"
                    :placeholder="form.type === 'percentage' ? '20' : '100'" />
                <p class="text-xs text-muted-foreground">
                    {{ form.type === 'percentage' ? 'Enter percentage (1-100)' : 'Enter fixed discount amount' }}
                </p>
                <InputError :message="form.errors.value" />
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="minimum_amount">Minimum Order Amount</Label>
                <Input id="minimum_amount" v-model="form.minimum_amount" type="number" step="0.01" min="0"
                    placeholder="1000" />
                <InputError :message="form.errors.minimum_amount" />
            </div>

            <div v-if="form.type === 'percentage'" class="grid gap-2">
                <Label for="maximum_discount">Maximum Discount</Label>
                <Input id="maximum_discount" v-model="form.maximum_discount" type="number" step="0.01" min="0"
                    placeholder="500" />
                <p class="text-xs text-muted-foreground">
                    Leave empty for no maximum limit.
                </p>
                <InputError :message="form.errors.maximum_discount" />
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="usage_limit">Total Usage Limit</Label>
                <Input id="usage_limit" v-model="form.usage_limit" type="number" min="1" placeholder="100" />
                <p class="text-xs text-muted-foreground">
                    Leave empty for unlimited usage.
                </p>
                <InputError :message="form.errors.usage_limit" />
            </div>

            <div class="grid gap-2">
                <Label for="usage_per_user">Usage Per User</Label>
                <Input id="usage_per_user" v-model="form.usage_per_user" type="number" min="1" />
                <InputError :message="form.errors.usage_per_user" />
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="starts_at">Starts At</Label>
                <Input id="starts_at" v-model="form.starts_at" type="datetime-local" />
                <InputError :message="form.errors.starts_at" />
            </div>

            <div class="grid gap-2">
                <Label for="expires_at">Expires At</Label>
                <Input id="expires_at" v-model="form.expires_at" type="datetime-local" />
                <InputError :message="form.errors.expires_at" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input id="is_active" v-model="form.is_active" type="checkbox" class="size-4 rounded border border-input" />
            <Label for="is_active" class="cursor-pointer font-normal">
                Active Coupon
            </Label>
            <InputError :message="form.errors.is_active" />
        </div>
    </div>
</template>
