<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { Menu } from "lucide-vue-next"
import { cn } from "@/lib/utils"
import { Button } from '@/components/ui/button'
import { useSidebar } from "./utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { toggleSidebar } = useSidebar()
</script>

<template>
  <Button
    data-sidebar="trigger"
    data-slot="sidebar-trigger"
    variant="ghost"
    size="icon"
    :class="cn('h-8 w-8 text-gray-500 hover:bg-gray-100 hover:text-[#163A2B]', props.class)"
    @click="toggleSidebar"
  >
    <Menu class="h-4.5 w-4.5" />
    <span class="sr-only">Toggle sidebar</span>
  </Button>
</template>
