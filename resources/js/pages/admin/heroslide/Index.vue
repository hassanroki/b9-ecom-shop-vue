<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Trash2, Images } from 'lucide-vue-next';

import HeroSlideController from '@/actions/App/Http/Controllers/Admin/HeroSlideController';

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
import {
    create,
    edit,
    index,
    show,
} from '@/routes/admin/slides';

import type { AdminHeroSlide } from '@/types/admin';

defineProps<{
    heroSlides: AdminHeroSlide[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Hero Slides',
                href: index(),
            },
        ],
    },
});
</script>

<template>

    <Head title="Hero Slides" />

    <div class="flex h-full flex-1 flex-col gap-5 p-4 md:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading title="Hero Slides" description="Manage homepage hero slides" />

            <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add Hero Slide
                </Link>
            </Button>
        </div>

        <!-- Empty State -->
        <div v-if="heroSlides.length === 0" class="rounded-xl border border-border bg-card py-16 text-center shadow-sm">
            <Images class="mx-auto size-10 text-muted-foreground" />

            <p class="mt-4 text-sm font-medium text-foreground">
                No hero slides yet
            </p>

            <p class="mt-1 text-sm text-muted-foreground">
                Create your first hero slide to get started.
            </p>

            <Button as-child class="mt-4 bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add Hero Slide
                </Link>
            </Button>
        </div>

        <template v-else>

            <!-- Mobile: Card List -->
            <div class="flex flex-col gap-3 md:hidden">
                <div v-for="heroSlide in heroSlides" :key="heroSlide.id"
                    class="rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-start gap-3">

                        <!-- Image -->
                        <div class="size-16 shrink-0 overflow-hidden rounded-lg border border-border bg-muted">
                            <img v-if="heroSlide.image" :src="heroSlide.image"
                                :alt="heroSlide.title ?? 'Hero slide'" class="size-full object-cover" />

                            <div v-else class="flex size-full items-center justify-center text-muted-foreground">
                                <Images class="size-5" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">

                            <div class="flex items-start justify-between gap-2">
                                <p class="truncate font-medium text-foreground">
                                    {{ heroSlide.title ?? '—' }}
                                </p>

                                <!-- Status -->
                                <span
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="heroSlide.is_active
                                        ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                        : 'bg-muted text-muted-foreground'
                                        ">
                                    <span class="size-1.5 rounded-full" :class="heroSlide.is_active
                                        ? 'bg-[#00BC7D]'
                                        : 'bg-muted-foreground/50'
                                        " />

                                    {{
                                        heroSlide.is_active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </span>
                            </div>

                            <!-- Subtitle -->
                            <p v-if="heroSlide.subtitle" class="mt-0.5 truncate text-xs text-muted-foreground">
                                {{ heroSlide.subtitle }}
                            </p>

                            <!-- Sort -->
                            <div class="mt-2 flex gap-4 text-xs text-muted-foreground">
                                <span>
                                    Sort: {{ heroSlide.sort_order }}
                                </span>

                                <span v-if="heroSlide.button_text">
                                    Button: {{ heroSlide.button_text }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-3 flex items-center gap-2 border-t border-border pt-3">
                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="show(heroSlide.id)">
                                <Eye class="size-4" />
                                View
                            </Link>
                        </Button>

                        <Button as-child variant="outline" size="sm" class="flex-1">
                            <Link :href="edit(heroSlide.id)">
                                <Pencil class="size-4" />
                                Edit
                            </Link>
                        </Button>

                        <Dialog>
                            <DialogTrigger as-child>
                                <Button variant="ghost" size="icon"
                                    class="size-9 shrink-0 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    :aria-label="`Delete ${heroSlide.title ?? 'hero slide'}`">
                                    <Trash2 class="size-4" />
                                </Button>
                            </DialogTrigger>

                            <DialogContent>
                                <Form v-bind="HeroSlideController.destroy.form(
                                    heroSlide.id,
                                )
                                    " :options="{
                                        preserveScroll: true,
                                    }" v-slot="{ processing }">
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle>
                                            Delete hero slide?
                                        </DialogTitle>

                                        <DialogDescription>
                                            This will remove
                                            <strong>
                                                {{ heroSlide.title ?? 'this hero slide' }}
                                            </strong>
                                            from your hero slides.
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
                                    Image
                                </th>

                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Title
                                </th>

                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                    Subtitle
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
                            <tr v-for="heroSlide in heroSlides" :key="heroSlide.id"
                                class="border-b border-border transition-colors last:border-b-0 hover:bg-muted/30">
                                <!-- Image -->
                                <td class="px-5 py-3.5">
                                    <div class="size-20 overflow-hidden rounded-lg border border-border bg-muted">
                                        <img v-if="heroSlide.image" :src="heroSlide.image"
                                            :alt="heroSlide.title ?? 'Hero slide'" class="size-full object-cover" />

                                        <div v-else
                                            class="flex size-full items-center justify-center text-muted-foreground">
                                            <Images class="size-5" />
                                        </div>
                                    </div>
                                </td>

                                <!-- Title -->
                                <td class="px-5 py-3.5 font-medium text-foreground">
                                    {{ heroSlide.title ?? '—' }}
                                </td>

                                <!-- Subtitle -->
                                <td class="max-w-64 px-5 py-3.5 text-muted-foreground">
                                    <span class="line-clamp-2">
                                        {{ heroSlide.subtitle || '—' }}
                                    </span>
                                </td>

                                <!-- Sort -->
                                <td class="px-5 py-3.5 text-muted-foreground">
                                    {{ heroSlide.sort_order }}
                                </td>

                                <!-- Status -->
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="heroSlide.is_active
                                            ? 'bg-[#00BC7D]/10 text-[#00754F]'
                                            : 'bg-muted text-muted-foreground'
                                            ">
                                        <span class="size-1.5 rounded-full" :class="heroSlide.is_active
                                            ? 'bg-[#00BC7D]'
                                            : 'bg-muted-foreground/50'
                                            " />

                                        {{
                                            heroSlide.is_active
                                                ? 'Active'
                                                : 'Inactive'
                                        }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- View -->
                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="show(heroSlide.id)"
                                                :aria-label="`View ${heroSlide.title ?? 'hero slide'}`">
                                                <Eye class="size-4" />
                                            </Link>
                                        </Button>

                                        <!-- Edit -->
                                        <Button as-child variant="ghost" size="icon"
                                            class="size-8 text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <Link :href="edit(heroSlide.id)"
                                                :aria-label="`Edit ${heroSlide.title ?? 'hero slide'}`">
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>

                                        <!-- Delete -->
                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <Button variant="ghost" size="icon"
                                                    class="size-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                                    :aria-label="`Delete ${heroSlide.title ?? 'hero slide'}`">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </DialogTrigger>

                                            <DialogContent>
                                                <Form v-bind="HeroSlideController.destroy.form(
                                                    heroSlide.id,
                                                )
                                                    " :options="{
                                                        preserveScroll: true,
                                                    }" v-slot="{
                                                        processing,
                                                    }">
                                                    <DialogHeader class="space-y-3">
                                                        <DialogTitle>
                                                            Delete hero slide?
                                                        </DialogTitle>

                                                        <DialogDescription>
                                                            This will remove
                                                            <strong>
                                                                {{
                                                                    heroSlide.title ?? 'this hero slide'
                                                                }}
                                                            </strong>
                                                            from your hero
                                                            slides.
                                                        </DialogDescription>
                                                    </DialogHeader>

                                                    <DialogFooter class="gap-2">
                                                        <DialogClose as-child>
                                                            <Button type="button" variant="secondary">
                                                                Cancel
                                                            </Button>
                                                        </DialogClose>

                                                        <Button type="submit" variant="destructive" :disabled="processing
                                                            ">
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
