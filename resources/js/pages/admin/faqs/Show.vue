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
} from '@/routes/admin/faqs';

import type { AdminFaq } from '@/types/admin';

const props = defineProps<{
    faq: AdminFaq;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
        {
            title: 'FAQs',
            href: index(),
        },
        {
            title: `FAQ #${props.faq.id}`,
            href: show(props.faq.id),
        },
    ],
});
</script>

<template>

    <Head title="FAQ Details" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading title="FAQ Details" description="Question and answer overview" />

            <Button as-child>
                <Link :href="edit(faq.id)">
                    <Pencil class="size-4" />
                    Edit FAQ
                </Link>
            </Button>
        </div>

        <!-- Details -->
        <Card>
            <CardHeader>
                <CardTitle>Details</CardTitle>
                <CardDescription>
                    Overview of this FAQ
                </CardDescription>
            </CardHeader>

            <CardContent class="grid gap-4">
                <div class="grid gap-1">
                    <p class="text-sm text-muted-foreground">Category</p>
                    <Badge v-if="faq.category" variant="outline" class="w-fit">
                        {{ faq.category.name }}
                    </Badge>
                    <p v-else class="font-medium text-muted-foreground">—</p>
                </div>

                <div class="grid gap-1">
                    <p class="text-sm text-muted-foreground">Question</p>
                    <p class="font-medium">{{ faq.question }}</p>
                </div>

                <div class="grid gap-1">
                    <p class="text-sm text-muted-foreground">Answer</p>
                    <p class="font-medium whitespace-pre-line">
                        {{ faq.answer }}
                    </p>
                </div>

                <!-- Sort Order / Status -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Sort Order
                        </p>
                        <p class="font-medium">
                            {{ faq.sort_order }}
                        </p>
                    </div>

                    <div class="grid gap-1">
                        <p class="text-sm text-muted-foreground">
                            Status
                        </p>
                        <Badge :variant="faq.is_active ? 'default' : 'secondary'
                            ">
                            {{ faq.is_active ? 'Active' : 'Inactive' }}
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
                                    faq.created_at,
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
                                    faq.updated_at,
                                ).toLocaleString()
                            }}
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
