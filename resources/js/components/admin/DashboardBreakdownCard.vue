<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface BreakdownItem {
    label: string;
    count: number;
}

const props = withDefaults(
    defineProps<{
        title: string;
        description: string;
        items: BreakdownItem[];
        total: number;
        barClass?: string;
    }>(),
    {
        barClass: 'bg-primary',
    },
);

function breakdownPercent(count: number): number {
    if (props.total <= 0) {
        return 0;
    }

    return Math.round((count / props.total) * 100);
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ title }}</CardTitle>
            <CardDescription>{{ description }}</CardDescription>
        </CardHeader>
        <CardContent class="grid gap-3">
            <div v-for="item in items" :key="item.label" class="grid gap-1.5">
                <div class="flex items-center justify-between text-sm">
                    <span>{{ item.label }}</span>
                    <span class="font-medium">{{ item.count }}</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-muted">
                    <div
                        class="h-full rounded-full transition-all"
                        :class="barClass"
                        :style="{ width: `${breakdownPercent(item.count)}%` }"
                    />
                </div>
            </div>
        </CardContent>
    </Card>
</template>
