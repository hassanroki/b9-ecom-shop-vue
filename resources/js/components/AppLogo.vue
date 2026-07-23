<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const { variant = 'light' } = defineProps<{
    variant?: 'light' | 'dark';
}>();
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center">
        <AppLogoIcon class="size-8" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span
            class="text-xl font-extrabold tracking-tight"
            :class="variant === 'dark' ? 'text-white' : 'text-[#163A2B]'"
        >
            Shop<span class="text-[#C08A3E]">BD</span>
        </span>
    </div>
</template>
