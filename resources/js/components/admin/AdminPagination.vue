<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import type { PaginationLink } from '@/types/admin';

defineProps<{
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
}>();

function isPrev(label: string): boolean {
    return label.includes('Previous') || label === '&laquo; Previous';
}

function isNext(label: string): boolean {
    return label.includes('Next') || label === 'Next &raquo;';
}

function isEllipsis(label: string): boolean {
    return label === '...';
}
</script>

<template>
    <div
        class="flex flex-col items-center justify-between gap-3 sm:flex-row"
    >
        <p class="text-xs text-muted-foreground">
            Showing <span class="font-medium text-foreground">{{ from ?? 0 }}</span>–<span
                class="font-medium text-foreground"
                >{{ to ?? 0 }}</span
            >
            of <span class="font-medium text-foreground">{{ total }}</span>
        </p>

        <div class="flex items-center gap-1">
            <template v-for="(link, i) in links" :key="i">
                <!-- Ellipsis -->
                <span
                    v-if="isEllipsis(link.label)"
                    class="px-2 text-xs text-muted-foreground/50"
                >
                    …
                </span>

                <!-- Prev button -->
                <Link
                    v-else-if="isPrev(link.label)"
                    :href="link.url ?? '#'"
                    preserve-scroll
                    preserve-state
                    :class="[
                        'inline-flex size-8 items-center justify-center rounded-lg border text-muted-foreground transition-colors',
                        link.url
                            ? 'hover:border-primary/40 hover:bg-muted hover:text-foreground'
                            : 'pointer-events-none opacity-40',
                    ]"
                    aria-label="Previous page"
                >
                    <ChevronLeft class="size-4" />
                </Link>

                <!-- Next button -->
                <Link
                    v-else-if="isNext(link.label)"
                    :href="link.url ?? '#'"
                    preserve-scroll
                    preserve-state
                    :class="[
                        'inline-flex size-8 items-center justify-center rounded-lg border text-muted-foreground transition-colors',
                        link.url
                            ? 'hover:border-primary/40 hover:bg-muted hover:text-foreground'
                            : 'pointer-events-none opacity-40',
                    ]"
                    aria-label="Next page"
                >
                    <ChevronRight class="size-4" />
                </Link>

                <!-- Page number -->
                <Link
                    v-else
                    :href="link.url ?? '#'"
                    preserve-scroll
                    preserve-state
                    :class="[
                        'inline-flex size-8 items-center justify-center rounded-lg text-xs font-medium transition-colors',
                        link.active
                            ? 'bg-primary text-primary-foreground shadow-sm'
                            : 'text-muted-foreground hover:bg-muted hover:text-foreground',
                    ]"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>