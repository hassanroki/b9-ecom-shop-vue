<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Trash2, HelpCircle } from 'lucide-vue-next';

import FaqCategoryController from '@/actions/App/Http/Controllers/Admin/FaqCategoryController';

import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
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
import {
    create,
    edit,
    index,
    show,
} from '@/routes/admin/faq-categories';

import type { AdminFaqCategory } from '@/types/admin';

defineProps<{
    faqCategories: AdminFaqCategory[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'FAQ Categories',
                href: index(),
            },
        ],
    },
});
</script>

<template>

    <Head title="FAQ Categories" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading title="FAQ Categories" description="Manage FAQ category groups" />

            <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add Category
                </Link>
            </Button>
        </div>

        <!-- Empty State -->
        <div v-if="faqCategories.length === 0"
            class="rounded-xl border border-border bg-card py-16 text-center shadow-sm">
            <HelpCircle class="mx-auto size-10 text-muted-foreground" />

            <p class="mt-4 text-sm font-medium text-foreground">
                No FAQ categories yet
            </p>

            <p class="mt-1 text-sm text-muted-foreground">
                Create your first FAQ category to get started.
            </p>

            <Button as-child class="mt-4 bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add Category
                </Link>
            </Button>
        </div>

        <template v-else>

            <!-- Mobile: Card List -->
            <div class="flex flex-col gap-3 md:hidden">
                <div v-for="category in faqCategories" :key="category.id"
                    class="rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-foreground">
                                {{ category.name }}
                            </p>

                            <p class="mt-0.5 truncate text-xs text-muted-foreground">
                                {{ category.slug }}
                            </p>
                        </div>

                        <span
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="category.is_active
                                ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                : 'bg-muted text-muted-foreground'
                                ">
                            <span class="size-1.5 rounded-full" :class="category.is_active
                                ? 'bg-[#00BC7D]'
                                : 'bg-muted-foreground/50'
                                " />

                            {{ category.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mt-2 flex gap-4 text-xs text-muted-foreground">
                        <span>Sort: {{ category.sort_order }}</span>
                        <span>{{ category.faqs_count }} FAQs</span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-3 flex items-center gap-2 border-t border-border pt-3">
                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="show(category.id)">
                                <Eye class="size-4" />
                                View
                            </Link>
                        </Button>

                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="edit(category.id)">
                                <Pencil class="size-4" />
                                Edit
                            </Link>
                        </Button>

                        <Dialog>
                            <DialogTrigger as-child>
                                <Button variant="ghost" size="icon"
                                    class="size-9 shrink-0 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    :aria-label="`Delete ${category.name}`">
                                    <Trash2 class="size-4" />
                                </Button>
                            </DialogTrigger>

                            <DialogContent>
                                <Form v-bind="FaqCategoryController.destroy.form(category.id)"
                                    :options="{ preserveScroll: true }" v-slot="{ processing }">
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle>
                                            Delete category?
                                        </DialogTitle>

                                        <DialogDescription>
                                            This will remove
                                            <strong>{{ category.name }}</strong>
                                            and cannot be undone.
                                        </DialogDescription>
                                    </DialogHeader>

                                    <DialogFooter class="gap-2">
                                        <DialogClose as-child>
                                            <Button type="button" variant="secondary">
                                                Cancel
                                            </Button>
                                        </DialogClose>

                                        <Button type="submit" variant="destructive" :disabled="processing">
                                            Delete
                                        </Button>
                                    </DialogFooter>
                                </Form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>
            </div>

            <!-- Desktop: Table -->
            <div class="hidden overflow-hidden rounded-xl border border-border bg-card shadow-sm md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-200 text-sm">

                        <thead class="border-b border-border bg-muted/50 text-left">
                            <tr>
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
                                    FAQs
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
                            <tr v-for="category in faqCategories" :key="category.id"
                                class="border-b border-border transition-colors last:border-b-0 hover:bg-muted/30">

                                <td class="px-5 py-3.5 font-medium text-foreground">
                                    {{ category.name }}
                                </td>

                                <td class="px-5 py-3.5 text-muted-foreground">
                                    {{ category.slug }}
                                </td>

                                <td class="px-5 py-3.5">
                                    <Badge variant="outline">
                                        {{ category.faqs_count }}
                                    </Badge>
                                </td>

                                <td class="px-5 py-3.5 text-muted-foreground">
                                    {{ category.sort_order }}
                                </td>

                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="category.is_active
                                            ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                            : 'bg-muted text-muted-foreground'
                                            ">
                                        <span class="size-1.5 rounded-full" :class="category.is_active
                                            ? 'bg-[#00BC7D]'
                                            : 'bg-muted-foreground/50'
                                            " />

                                        {{ category.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="show(category.id)" :aria-label="`View ${category.name}`">
                                                <Eye class="size-4" />
                                            </Link>
                                        </Button>

                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="edit(category.id)" :aria-label="`Edit ${category.name}`">
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>

                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <Button variant="ghost" size="icon"
                                                    class="size-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                                    :aria-label="`Delete ${category.name}`">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </DialogTrigger>

                                            <DialogContent>
                                                <Form v-bind="FaqCategoryController.destroy.form(category.id)"
                                                    :options="{ preserveScroll: true }" v-slot="{ processing }">
                                                    <DialogHeader class="space-y-3">
                                                        <DialogTitle>
                                                            Delete category?
                                                        </DialogTitle>

                                                        <DialogDescription>
                                                            This will remove
                                                            <strong>{{ category.name }}</strong>
                                                            and cannot be undone.
                                                        </DialogDescription>
                                                    </DialogHeader>

                                                    <DialogFooter class="gap-2">
                                                        <DialogClose as-child>
                                                            <Button type="button" variant="secondary">
                                                                Cancel
                                                            </Button>
                                                        </DialogClose>

                                                        <Button type="submit" variant="destructive"
                                                            :disabled="processing">
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
