<script setup lang="ts">
import { ref } from 'vue';
import { useShopCarousel } from '@/composables/shop/useShopCarousel';
import type { ShopCarouselSlide } from '@/types/shop';
import { Link } from '@inertiajs/vue3';

const { slides } = defineProps<{
    slides: ShopCarouselSlide[];
}>();

const {
    current,
    goTo,
    next,
    prev,
    startAuto,
    stopAuto,
    handleTouchStart,
    handleTouchEnd,
} = useShopCarousel(slides.length);

const touchStartX = ref(0);

function onTouchStart(event: TouchEvent): void {
    touchStartX.value = handleTouchStart(event);
}

function onTouchEnd(event: TouchEvent): void {
    handleTouchEnd(event, touchStartX.value);
}
</script>

<template>
    <section v-if="slides.length > 0" class="relative" aria-roledescription="carousel" aria-label="Featured promotions">
        <div class="relative w-full overflow-hidden aspect-[16/5]" @mouseenter="stopAuto" @mouseleave="startAuto"
            @touchstart.passive="onTouchStart" @touchend.passive="onTouchEnd">
            <!-- Slides -->
            <div v-for="(slide, index) in slides" :key="index" class="shop-slide absolute inset-0"
                :class="{ active: current === index }" role="group" aria-roledescription="slide"
                :aria-label="`${index + 1} of ${slides.length}`">
                <!-- Full image - No Crop -->
                <img :src="slide.src" :alt="slide.alt" class="absolute inset-0 h-full w-full object-contain" />

                <!-- Overlay -->
                <div v-if="slide.title || slide.subtitle || slide.buttonText" class="absolute inset-0 flex flex-col items-center justify-end
           bg-black/20 px-5 pb-5
           sm:pb-10
           lg:pb-12">
                    <!-- Title -->
                    <h2 v-if="slide.title" class="max-w-[85%] text-center text-xl font-bold leading-tight
               text-white drop-shadow
               sm:max-w-xl sm:text-3xl
               lg:text-5xl">
                        {{ slide.title }}
                    </h2>

                    <!-- Subtitle -->
                    <p v-if="slide.subtitle" class="mt-2 max-w-[85%] text-center text-xs leading-relaxed
               text-white/90 drop-shadow
               sm:max-w-md sm:text-sm
               lg:text-lg">
                        {{ slide.subtitle }}
                    </p>

                    <!-- Shop Now Button -->
                    <Link v-if="slide.buttonText && slide.link" :href="slide.link" class="mt-3 inline-flex items-center rounded-full
               bg-shop-primary-600 px-4 py-2
               text-sm font-semibold text-white shadow-lg
               transition hover:bg-shop-primary-700
               sm:px-4 sm:py-2 sm:text-base">
                        {{ slide.buttonText }}
                    </Link>
                </div>

            </div>

            <!-- Previous -->
            <button type="button" aria-label="Previous slide" class="absolute top-1/2 left-3 hidden h-9 w-9
                   -translate-y-1/2 items-center justify-center
                   rounded-full bg-white/80 text-gray-700 shadow
                   transition hover:bg-white
                   md:flex lg:left-4 lg:h-11 lg:w-11" @click="
                    prev();
                startAuto();
                ">
                <svg class="h-5 w-5 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Next -->
            <button type="button" aria-label="Next slide" class="absolute top-1/2 right-3 hidden h-9 w-9
                   -translate-y-1/2 items-center justify-center
                   rounded-full bg-white/80 text-gray-700 shadow
                   transition hover:bg-white
                   md:flex lg:right-4 lg:h-11 lg:w-11" @click="
                    next();
                startAuto();
                ">
                <svg class="h-5 w-5 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Indicators -->
            <div class="absolute bottom-2 left-1/2 flex
                   -translate-x-1/2 items-center gap-1.5
                   sm:bottom-4 sm:gap-2">
                <button v-for="(_, index) in slides" :key="index" type="button" :aria-label="`Go to slide ${index + 1}`"
                    class="h-2 w-2 rounded-full transition
                       sm:h-2.5 sm:w-2.5" :class="current === index
                        ? 'bg-white'
                        : 'bg-white/50'
                        " :aria-current="current === index ? 'true' : 'false'
                            " @click="
                        goTo(index);
                    startAuto();
                    " />
            </div>
        </div>
    </section>

</template>
