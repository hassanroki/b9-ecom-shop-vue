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
} from '@/routes/admin/faq-categories';

import type { AdminFaqCategory } from '@/types/admin';

const props = defineProps<{
    faqCategory: AdminFaqCategory;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
        {
            title: 'FAQ Categories',
            href: index(),
        },
        {
            title: props.faqCategory.name,
            href: show(props.faqCategory.id),
        },
    ],
});
</script>

<template>

    <Head :title="faqCategory.name" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading :title="faqCategory.name" description="FAQ category details" />

            <Button as-child>
                <Link :href="edit(faqCategory.id)">
                    <Pencil class="size-4" />
                    Edit Category
                </Link>
            </Button>
        </div>

        <!-- Details -->
        <Card>
            <CardHeader>
                <CardTitle>Details</CardTitle>
                <CardDescription>
                    Overview of this FAQ category
                </CardDescription>
            </CardHeader>

            <CardContent class="grid gap-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Name</p>
                        <p class="font-medium">{{ faqCategory.name }}</p>
                    </div>

                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Slug</p>
                        <p class="font-medium">{{ faqCategory.slug }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">FAQs</p>
                        <Badge variant="outline" class="w-fit">
                            {{ faqCategory.faqs_count }}
                        </Badge>
                    </div>

                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Sort Order
                        </p>
                        <p class="font-medium">
                            {{ faqCategory.sort_order }}
                        </p>
                    </div>

                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">Status</p>
                        <Badge :variant="faqCategory.is_active
                                ? 'default'
                                : 'secondary'
                            ">
                            {{
                                faqCategory.is_active
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
                                    faqCategory.created_at,
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
                                    faqCategory.updated_at,
                                ).toLocaleString()
                            }}
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
