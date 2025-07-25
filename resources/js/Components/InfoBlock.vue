<script setup lang="ts">
import { MapPinIcon as DefaultIcon } from '@heroicons/vue/24/solid'

defineProps<{
  icon?: any
  title: string
  line1?: string
  line2?: string
  line3?: string
}>()
</script>

<template>
  <div class="flex sm:gap-x-6 gap-x-3 items-center">
    <div
      class="flex bg-brand/10 sm:size-20 size-10 rounded-2xl items-center justify-center aspect-square"
    >
      <component :is="icon || DefaultIcon" class="sm:size-10 size-5 text-brand" />
    </div>
    <div class="flex flex-col">
      <h2 class="sm:text-3xl text-sm font-bold">{{ title }}</h2>
      <p v-if="line1" class="sm:text-sm text-xs text-gray-500">{{ line1 }}</p>
      <p v-if="line2" class="sm:text-sm text-xs text-gray-500">{{ line2 }}</p>
      <p v-if="line3" class="sm:text-sm text-xs text-gray-500">{{ line3 }}</p>
    </div>
  </div>
</template>
