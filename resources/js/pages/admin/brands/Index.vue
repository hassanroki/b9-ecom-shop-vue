<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import BrandController from '@/actions/App/Http/Controllers/Admin/BrandController';
import Heading from '@/components/Heading.vue';
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
import { dashboard } from '@/routes';
import { create, edit, index, show } from '@/routes/admin/brands';
import type { AdminBrand } from '@/types/admin';

defineProps<{
    brands: AdminBrand[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Brands', href: index() },
        ],
    },
});
</script>

<template>

    <Head title="Brands" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading title="Brands" description="Manage product brands for your store" />

            <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add brand
                </Link>
            </Button>
        </div>

        <!-- Empty state (shared) -->
        <div v-if="brands.length === 0" class="rounded-xl border border-border bg-card py-16 text-center shadow-sm">
            <p class="text-sm font-medium text-foreground">No brands yet</p>
            <p class="mt-1 text-sm text-muted-foreground">
                Create your first brand to get started.
            </p>
            <Button as-child class="mt-4 bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add brand
                </Link>
            </Button>
        </div>

        <template v-else>
            <!-- Mobile: card list (below md) -->
            <div class="flex flex-col gap-3 md:hidden">
                <div v-for="brand in brands" :key="brand.id"
                    class="rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="size-12 shrink-0 overflow-hidden rounded-lg border border-border bg-muted">
                            <img v-if="brand.image" :src="brand.image" :alt="brand.name"
                                class="size-full object-cover" />
                            <div v-else
                                class="flex size-full items-center justify-center text-xs text-muted-foreground">
                                —
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="truncate font-medium text-foreground">
                                    {{ brand.name }}
                                </p>
                                <span
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="brand.is_active
                                            ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                            : 'bg-muted text-muted-foreground'
                                        ">
                                    <span class="size-1.5 rounded-full" :class="brand.is_active
                                            ? 'bg-[#00BC7D]'
                                            : 'bg-muted-foreground/50'
                                        " />
                                    {{
                                        brand.is_active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </span>
                            </div>
                            <p class="mt-0.5 truncate font-mono text-xs text-muted-foreground">
                                {{ brand.slug }}
                            </p>

                            <div class="mt-2 flex gap-4 text-xs text-muted-foreground">
                                <span>{{
                                    brand.products_count
                                }}
                                    products</span>
                                <span>Sort: {{ brand.sort_order }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center gap-2 border-t border-border pt-3">
                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="show(brand.id)">
                                <Eye class="size-4" />
                                View
                            </Link>
                        </Button>
                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="edit(brand.id)">
                                <Pencil class="size-4" />
                                Edit
                            </Link>
                        </Button>
                        <Dialog>
                            <DialogTrigger as-child>
                                <Button variant="ghost" size="icon"
                                    class="size-9 shrink-0 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    :aria-label="`Delete ${brand.name}`" :data-test="`delete-brand-${brand.id}`">
                                    <Trash2 class="size-4" />
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <Form v-bind="BrandController.destroy.form(
                                    brand.id,
                                )
                                    " :options="{ preserveScroll: true }" v-slot="{ processing }">
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle>Delete brand?</DialogTitle>
                                        <DialogDescription>
                                            This will remove
                                            <strong>{{ brand.name }}</strong>
                                            from your store. This action can be
                                            undone from the database if needed.
                                        </DialogDescription>
                                    </DialogHeader>

                                    <DialogFooter class="gap-2">
                                        <DialogClose as-child>
                                            <Button type="button" variant="secondary">Cancel</Button>
                                        </DialogClose>
                                        <Button type="submit" variant="destructive" :disabled="processing"
                                            :data-test="`confirm-delete-brand-${brand.id}`">
                                            Delete
                                        </Button>
                                    </DialogFooter>
                                </Form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>
            </div>

            <!-- Desktop: table (md and up) -->
            <div class="hidden overflow-hidden rounded-xl border border-border bg-card shadow-sm md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-200 text-sm">
                        <thead class="border-b border-border bg-muted/50 text-left">
                            <tr>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Image
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Name
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Slug
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Products
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Sort
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Status
                                </th>
                                <th
                                    class="px-5 py-3.5 text-right text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="brand in brands" :key="brand.id"
                                class="border-b border-border transition-colors last:border-b-0 hover:bg-muted/30">
                                <td class="px-5 py-3.5">
                                    <div class="size-11 overflow-hidden rounded-lg border border-border bg-muted">
                                        <img v-if="brand.image" :src="brand.image" :alt="brand.name"
                                            class="size-full object-cover" />
                                        <div v-else
                                            class="flex size-full items-center justify-center text-xs text-muted-foreground">
                                            —
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-foreground">
                                    {{ brand.name }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-muted-foreground">
                                    {{ brand.slug }}
                                </td>
                                <td class="px-5 py-3.5 text-foreground">
                                    {{ brand.products_count }}
                                </td>
                                <td class="px-5 py-3.5 text-muted-foreground">
                                    {{ brand.sort_order }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="brand.is_active
                                                ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                                : 'bg-muted text-muted-foreground'
                                            ">
                                        <span class="size-1.5 rounded-full" :class="brand.is_active
                                                ? 'bg-[#00BC7D]'
                                                : 'bg-muted-foreground/50'
                                            " />
                                        {{
                                            brand.is_active
                                                ? 'Active'
                                                : 'Inactive'
                                        }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="show(brand.id)" :aria-label="`View ${brand.name}`">
                                                <Eye class="size-4" />
                                            </Link>
                                        </Button>
                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="edit(brand.id)" :aria-label="`Edit ${brand.name}`">
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>
                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <Button variant="ghost" size="icon"
                                                    class="size-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                                    :aria-label="`Delete ${brand.name}`"
                                                    :data-test="`delete-brand-${brand.id}`">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <Form v-bind="BrandController.destroy.form(
                                                    brand.id,
                                                )
                                                    " :options="{
                                                        preserveScroll: true,
                                                    }" v-slot="{ processing }">
                                                    <DialogHeader class="space-y-3">
                                                        <DialogTitle>Delete
                                                            brand?</DialogTitle>
                                                        <DialogDescription>
                                                            This will remove
                                                            <strong>{{
                                                                brand.name
                                                                }}</strong>
                                                            from your store.
                                                            This action can be
                                                            undone from the
                                                            database if needed.
                                                        </DialogDescription>
                                                    </DialogHeader>

                                                    <DialogFooter class="gap-2">
                                                        <DialogClose as-child>
                                                            <Button type="button" variant="secondary">Cancel</Button>
                                                        </DialogClose>
                                                        <Button type="submit" variant="destructive" :disabled="processing
                                                            " :data-test="`confirm-delete-brand-${brand.id}`">
                                                            Delete
                                                        </Button>
                                                    </DialogFooter>
                                                </Form>
                                            </DialogContent>
                                        </Dialog>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
</template>
