<script setup lang="ts">
import type { AdminOrder } from '@/types/admin';

import { ORDER_STEPS, STEP_CONFIG, stepClasses, stepState } from '@/lib/admin/order';

const props = defineProps<{
    order: AdminOrder;
}>();
</script>

<template>
    <div
        v-if="order.status !== 'cancelled'"
        class="rounded-xl border bg-card shadow-sm"
    >
        <div class="p-6">
            <div class="relative flex items-center justify-between">
                <!-- Progress Line -->
                <div
                    class="absolute top-5 right-0 left-0 z-0 mx-[calc(12.5%+20px)] h-0.5 bg-border"
                />

                <div
                    v-for="step in ORDER_STEPS"
                    :key="step"
                    class="relative z-10 flex flex-1 flex-col items-center gap-2 text-center"
                >
                    <div
                        :class="[
                            'flex size-10 items-center justify-center rounded-full border-2 transition-all duration-300',
                            stepClasses(order.status, step),
                        ]"
                    >
                        <component
                            :is="STEP_CONFIG[step].icon"
                            class="size-4"
                        />
                    </div>

                    <span
                        :class="[
                            'text-xs font-medium capitalize',
                            stepState(order.status, step) === 'upcoming'
                                ? 'text-muted-foreground'
                                : 'font-semibold text-foreground',
                        ]"
                    >
                        {{ STEP_CONFIG[step].label }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
