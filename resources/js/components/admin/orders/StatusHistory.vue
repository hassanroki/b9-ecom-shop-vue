<script setup lang="ts">
import type { AdminOrder } from '@/types/admin';

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import { Separator } from '@/components/ui/separator';

import { Clock } from '@lucide/vue';

import { formatDate, statusBadgeClasses } from '@/lib/admin/order';

defineProps<{
    order: AdminOrder;
}>();
</script>

<template>
    <Card>
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-base font-semibold">
                <Clock class="size-4 text-primary" />

                Status History
            </CardTitle>

            <CardDescription>
                Activity log of status updates for this order
            </CardDescription>
        </CardHeader>

        <Separator />

        <CardContent class="pt-6">
            <div
                v-if="order.status_histories.length"
                class="relative space-y-6 pl-6 before:absolute before:top-2 before:bottom-2 before:left-2 before:w-0.5 before:bg-border"
            >
                <div
                    v-for="(history, index) in order.status_histories"
                    :key="history.id"
                    class="relative"
                >
                    <div
                        :class="[
                            'absolute top-1 left-[1.65rem] size-3 rounded-full border-2 border-background',
                            index === 0
                                ? 'bg-primary ring-4 ring-primary/20'
                                : 'bg-muted-foreground/40',
                        ]"
                    />

                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                :class="[
                                    'rounded-md border px-2 py-0.5 text-xs font-semibold uppercase',
                                    statusBadgeClasses(history.status),
                                ]"
                            >
                                {{ history.status }}
                            </span>

                            <span class="text-xs text-muted-foreground">
                                {{ formatDate(history.created_at) }}
                            </span>
                        </div>

                        <p
                            v-if="history.note"
                            class="rounded-md border bg-muted/30 p-3 text-sm"
                        >
                            {{ history.note }}
                        </p>

                        <p
                            v-if="history.changed_by"
                            class="text-xs text-muted-foreground"
                        >
                            By

                            <span class="font-medium text-foreground">
                                {{ history.changed_by.name }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div v-else class="py-10 text-center text-sm text-muted-foreground">
                No history records found.
            </div>
        </CardContent>
    </Card>
</template>
