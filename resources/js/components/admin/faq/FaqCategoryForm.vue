<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { FaqCategoryFormData } from '@/types/admin';

defineProps<{
    form: InertiaForm<FaqCategoryFormData>;
}>();
</script>

<template>
    <div class="grid gap-6">
        <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" v-model="form.name" type="text" placeholder="Order & Delivery" required />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="slug">Slug</Label>
            <Input id="slug" v-model="form.slug" type="text" placeholder="order-delivery" />
            <p class="text-xs text-muted-foreground">
                Leave blank to generate from the name.
            </p>
            <InputError :message="form.errors.slug" />
        </div>

        <div class="grid gap-2 sm:max-w-xs">
            <Label for="sort_order">Sort order</Label>
            <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" required />
            <InputError :message="form.errors.sort_order" />
        </div>

        <div class="flex items-center gap-3">
            <input id="is_active" v-model="form.is_active" type="checkbox" class="size-4 rounded border border-input" />
            <Label for="is_active" class="cursor-pointer font-normal">
                Active
            </Label>
            <InputError :message="form.errors.is_active" />
        </div>
    </div>
</template>
