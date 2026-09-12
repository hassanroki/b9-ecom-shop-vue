<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { FaqFormData } from '@/types/admin';

type CategoryOption = {
    id: number;
    name: string;
};

defineProps<{
    form: InertiaForm<FaqFormData>;
    categories: CategoryOption[];
}>();

const selectClass =
    'border-input bg-background ring-offset-background focus-visible:ring-ring flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50';

const textareaClass =
    'border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-32 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50';
</script>

<template>
    <div class="grid gap-6">
        <div class="grid gap-2">
            <Label for="faq_category_id">Category</Label>
            <select id="faq_category_id" v-model="form.faq_category_id" :class="selectClass" required>
                <option disabled value="">Select a category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
            <InputError :message="form.errors.faq_category_id" />
        </div>

        <div class="grid gap-2">
            <Label for="question">Question</Label>
            <Input id="question" v-model="form.question" type="text" placeholder="সারাদেশে কি হোম ডেলিভারি দেওয়া হয়?"
                required />
            <InputError :message="form.errors.question" />
        </div>

        <div class="grid gap-2">
            <Label for="answer">Answer</Label>
            <textarea id="answer" v-model="form.answer" rows="5" :class="textareaClass"
                placeholder="Write the answer here" required />
            <InputError :message="form.errors.answer" />
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
