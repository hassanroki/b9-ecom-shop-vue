<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { Pencil } from '@lucide/vue';

import Heading from '@/components/Heading.vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import { dashboard } from '@/routes';

import {
    edit,
    index,
    show,
} from '@/routes/admin/slides';

import type { AdminHeroSlide } from '@/types/admin';

const props = defineProps<{
    heroSlide: AdminHeroSlide;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
        {
            title: 'Hero Slides',
            href: index(),
        },
        {
            title: `Hero Slide #${props.heroSlide.id}`,
            href: show(props.heroSlide.id),
        },
    ],
});
</script>

<template>

    <Head title="Hero Slide Details" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading title="Hero Slide Details" description="Hero slide details" />

            <Button as-child>
                <Link :href="edit(heroSlide.id)">
                    <Pencil class="size-4" />
                    Edit Hero Slide
                </Link>
            </Button>
        </div>

        <!-- Content -->
        <div class="grid gap-4 lg:grid-cols-[420px_1fr]">
            <!-- Image -->
            <Card>
                <CardHeader>
                    <CardTitle>Image</CardTitle>
                </CardHeader>

                <CardContent>
                    <div class="aspect-video overflow-hidden rounded-lg border bg-muted">
                        <img v-if="heroSlide.image" :src="heroSlide.image" :alt="heroSlide.title ?? 'Hero slide'"
                            class="size-full object-cover" />

                        <div v-else class="flex size-full items-center justify-center text-sm text-muted-foreground">
                            No image
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Details -->
            <Card>
                <CardHeader>
                    <CardTitle>Details</CardTitle>

                    <CardDescription>
                        Overview of this hero slide
                    </CardDescription>
                </CardHeader>

                <CardContent class="grid gap-4">
                    <!-- Title -->
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Title
                        </p>

                        <p class="font-medium">
                            {{ heroSlide.title || '—' }}
                        </p>
                    </div>

                    <!-- Subtitle -->
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Subtitle
                        </p>

                        <p class="font-medium">
                            {{ heroSlide.subtitle || '—' }}
                        </p>
                    </div>

                    <!-- Button Text -->
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Button Text
                        </p>

                        <p class="font-medium">
                            {{ heroSlide.button_text || '—' }}
                        </p>
                    </div>

                    <!-- Image Source -->
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Image Source
                        </p>

                        <Badge variant="outline" class="w-fit">
                            {{ heroSlide.image_source }}
                        </Badge>
                    </div>

                    <!-- Link -->
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Link
                        </p>


                        <Link v-if="heroSlide.link" :href="heroSlide.link" target="_blank" rel="noopener noreferrer"
                            class="font-medium text-primary hover:underline break-all">
                            {{ heroSlide.link }}
                        </Link>

                        <p v-else class="font-medium text-muted-foreground">
                            No link provided.
                        </p>
                    </div>

                    <!-- Sort Order / Status -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">
                                Sort Order
                            </p>

                            <p class="font-medium">
                                {{ heroSlide.sort_order }}
                            </p>
                        </div>

                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">
                                Status
                            </p>

                            <Badge :variant="heroSlide.is_active
                                ? 'default'
                                : 'secondary'
                                ">
                                {{
                                    heroSlide.is_active
                                        ? 'Active'
                                        : 'Inactive'
                                }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Created / Updated -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">
                                Created
                            </p>

                            <p class="font-medium">
                                {{
                                    new Date(
                                        heroSlide.created_at,
                                    ).toLocaleString()
                                }}
                            </p>
                        </div>

                        <div class="grid gap-1">
                            <p class="text-sm text-muted-foreground">
                                Updated
                            </p>

                            <p class="font-medium">
                                {{
                                    new Date(
                                        heroSlide.updated_at,
                                    ).toLocaleString()
                                }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
