<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

const img = '/assets/img/shopbd.svg';

defineProps<Props>();
</script>

<template>
    <img :src="img" alt="ShopBD" :class="className" v-bind="$attrs" />
</template>
