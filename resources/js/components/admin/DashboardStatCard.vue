<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { Component } from 'vue';

type Props = {
    title: string;
    value: string;
    description?: string;
    changePercent?: number | null;
    icon?: Component;
    iconClass?: string;
};

const props = defineProps<Props>();

function changeLabel(percent: number): string {
    const prefix = percent > 0 ? '+' : '';

    return `${prefix}${percent}% vs last month`;
}

function changeClass(percent: number | null | undefined): string {
    if (percent === null || percent === undefined) {
        return 'text-muted-foreground';
    }

    if (percent > 0) {
        return 'text-emerald-600 dark:text-emerald-400';
    }

    if (percent < 0) {
        return 'text-red-600 dark:text-red-400';
    }

    return 'text-muted-foreground';
}
</script>

<template>
    <Card>
        <CardHeader
            class="flex flex-row items-start justify-between gap-2 px-3 pt-3 pb-2 sm:gap-4 sm:px-6 sm:pt-6"
        >
            <div class="space-y-1">
                <CardDescription class="text-xs sm:text-sm">{{
                    title
                }}</CardDescription>
                <CardTitle class="text-lg font-bold tracking-tight sm:text-2xl">
                    {{ value }}
                </CardTitle>
            </div>
            <div
                v-if="icon"
                class="flex size-8 shrink-0 items-center justify-center rounded-lg sm:size-10"
                :class="iconClass ?? 'bg-primary/10 text-primary'"
            >
                <component :is="icon" class="size-4 sm:size-5" />
            </div>
        </CardHeader>
        <CardContent class="px-3 pt-0 pb-3 sm:px-6 sm:pb-6">
            <p
                v-if="changePercent !== undefined"
                class="text-sm"
                :class="changeClass(changePercent)"
            >
                <template v-if="changePercent !== null">
                    {{ changeLabel(changePercent) }}
                </template>
                <template v-else> No data for last month </template>
            </p>
            <p v-else-if="description" class="text-sm text-muted-foreground">
                {{ description }}
            </p>
        </CardContent>
    </Card>
</template>
