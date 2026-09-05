<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import BrandController from '@/actions/App/Http/Controllers/Admin/BrandController';
import BrandForm from '@/components/admin/BrandForm.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { edit, index } from '@/routes/admin/brands';
import type { AdminBrand, BrandFormData } from '@/types/admin';

const props = defineProps<{
    brand: AdminBrand;
}>();

setLayoutProps({
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Brands', href: index() },
        { title: `Edit ${props.brand.name}`, href: edit(props.brand.id) },
    ],
});

const form = useForm<BrandFormData>({
    name: props.brand.name,
    slug: props.brand.slug,
    image_source: props.brand.image_source,
    image: props.brand.image_url,
    image_file: null,
    description: props.brand.description ?? '',
    sort_order: props.brand.sort_order,
    is_active: props.brand.is_active,
});

function submit(): void {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(BrandController.update.url(props.brand.id), {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>

    <Head :title="`Edit ${brand.name}`" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="icon"
                    class="size-9 shrink-0 text-muted-foreground hover:bg-muted hover:text-foreground">
                    <Link :href="index()" aria-label="Back to brands">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                </Button>
                <Heading :title="`Edit ${brand.name}`" description="Update brand details" />
            </div>

            <span class="inline-flex w-fit items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium" :class="brand.is_active
                    ? 'bg-[#00BC7D]/10 text-[#00754F]'
                    : 'bg-muted text-muted-foreground'
                ">
                <span class="size-1.5 rounded-full" :class="brand.is_active
                        ? 'bg-[#00BC7D]'
                        : 'bg-muted-foreground/50'
                    " />
                {{ brand.is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div class="max-w-2xl overflow-hidden rounded-xl border border-border bg-card shadow-sm">
            <form class="p-6" @submit.prevent="submit">
                <BrandForm :form="form" :brand="brand" />

                <div class="mt-8 flex items-center gap-3 border-t border-border pt-6">
                    <Button type="submit" :disabled="form.processing"
                        class="bg-primary text-primary-foreground hover:bg-primary/90 disabled:opacity-60">
                        <svg v-if="form.processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        {{ form.processing ? 'Saving…' : 'Save changes' }}
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="index()">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
