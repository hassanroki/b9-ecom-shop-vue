<script setup lang="ts">
type Props = {
    title: string;
    description?: string;
    variant?: 'default' | 'small';
};

withDefaults(defineProps<Props>(), {
    variant: 'default',
});
</script>

<template>
    <header :class="variant === 'small' ? '' : 'mb-1 space-y-1'">
        <h2
            :class="
                variant === 'small'
                    ? 'text-base font-semibold text-foreground'
                    : 'text-2xl font-semibold tracking-tight text-foreground'
            "
        >
            {{ title }}
        </h2>
        <p
            v-if="description"
            :class="
                variant === 'small'
                    ? 'text-xs text-muted-foreground'
                    : 'text-sm text-muted-foreground'
            "
        >
            {{ description }}
        </p>
    </header>
</template>
