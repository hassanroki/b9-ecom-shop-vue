<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Trash2, HelpCircle } from 'lucide-vue-next';

import FaqController from '@/actions/App/Http/Controllers/Admin/FaqController';

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
} from '@/routes/admin/faqs';

import type { AdminFaq } from '@/types/admin';

defineProps<{
    faqs: AdminFaq[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'FAQs',
                href: index(),
            },
        ],
    },
});
</script>

<template>

    <Head title="FAQs" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading title="FAQs" description="Manage frequently asked questions" />

            <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add FAQ
                </Link>
            </Button>
        </div>

        <!-- Empty State -->
        <div v-if="faqs.length === 0" class="rounded-xl border border-border bg-card py-16 text-center shadow-sm">
            <HelpCircle class="mx-auto size-10 text-muted-foreground" />

            <p class="mt-4 text-sm font-medium text-foreground">
                No FAQs yet
            </p>

            <p class="mt-1 text-sm text-muted-foreground">
                Create your first FAQ to get started.
            </p>

            <Button as-child class="mt-4 bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add FAQ
                </Link>
            </Button>
        </div>

        <template v-else>

            <!-- Mobile: Card List -->
            <div class="flex flex-col gap-3 md:hidden">
                <div v-for="faq in faqs" :key="faq.id" class="rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-foreground">
                                {{ faq.question }}
                            </p>

                            <p v-if="faq.category" class="mt-0.5 text-xs text-muted-foreground">
                                {{ faq.category.name }}
                            </p>
                        </div>

                        <span
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="faq.is_active
                                ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                : 'bg-muted text-muted-foreground'
                                ">
                            <span class="size-1.5 rounded-full" :class="faq.is_active
                                ? 'bg-[#00BC7D]'
                                : 'bg-muted-foreground/50'
                                " />

                            {{ faq.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mt-2 flex gap-4 text-xs text-muted-foreground">
                        <span>Sort: {{ faq.sort_order }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-3 flex items-center gap-2 border-t border-border pt-3">
                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="show(faq.id)">
                                <Eye class="size-4" />
                                View
                            </Link>
                        </Button>

                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="edit(faq.id)">
                                <Pencil class="size-4" />
                                Edit
                            </Link>
                        </Button>

                        <Dialog>
                            <DialogTrigger as-child>
                                <Button variant="ghost" size="icon"
                                    class="size-9 shrink-0 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    :aria-label="`Delete ${faq.question}`">
                                    <Trash2 class="size-4" />
                                </Button>
                            </DialogTrigger>

                            <DialogContent>
                                <Form v-bind="FaqController.destroy.form(faq.id)" :options="{ preserveScroll: true }"
                                    v-slot="{ processing }">
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle>
                                            Delete FAQ?
                                        </DialogTitle>

                                        <DialogDescription>
                                            This will remove
                                            <strong>{{ faq.question }}</strong>
                                            from your FAQs.
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
                                    Question
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Category
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
                            <tr v-for="faq in faqs" :key="faq.id"
                                class="border-b border-border transition-colors last:border-b-0 hover:bg-muted/30">

                                <td class="max-w-96 px-5 py-3.5 font-medium text-foreground">
                                    <span class="line-clamp-2">{{ faq.question }}</span>
                                </td>

                                <td class="px-5 py-3.5">
                                    <Badge v-if="faq.category" variant="outline">
                                        {{ faq.category.name }}
                                    </Badge>
                                    <span v-else class="text-muted-foreground">—</span>
                                </td>

                                <td class="px-5 py-3.5 text-muted-foreground">
                                    {{ faq.sort_order }}
                                </td>

                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="faq.is_active
                                            ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                            : 'bg-muted text-muted-foreground'
                                            ">
                                        <span class="size-1.5 rounded-full" :class="faq.is_active
                                            ? 'bg-[#00BC7D]'
                                            : 'bg-muted-foreground/50'
                                            " />

                                        {{ faq.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="show(faq.id)" :aria-label="`View FAQ`">
                                                <Eye class="size-4" />
                                            </Link>
                                        </Button>

                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="edit(faq.id)" :aria-label="`Edit FAQ`">
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>

                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <Button variant="ghost" size="icon"
                                                    class="size-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                                    :aria-label="`Delete FAQ`">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </DialogTrigger>

                                            <DialogContent>
                                                <Form v-bind="FaqController.destroy.form(faq.id)"
                                                    :options="{ preserveScroll: true }" v-slot="{ processing }">
                                                    <DialogHeader class="space-y-3">
                                                        <DialogTitle>
                                                            Delete FAQ?
                                                        </DialogTitle>

                                                        <DialogDescription>
                                                            This will remove
                                                            <strong>{{ faq.question }}</strong>
                                                            from your FAQs.
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
