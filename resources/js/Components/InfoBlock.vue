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
  <div class="flex gap-x-6 items-center">
    <div
      class="flex bg-brand/10 size-20 rounded-2xl items-center justify-center"
    >
      <component :is="icon || DefaultIcon" class="size-10 text-brand" />
    </div>
    <div class="flex flex-col">
      <h2 class="text-3xl font-bold">{{ title }}</h2>
      <p v-if="line1" class="text-gray-500">{{ line1 }}</p>
      <p v-if="line2" class="text-gray-500">{{ line2 }}</p>
      <p v-if="line3" class="text-gray-500">{{ line3 }}</p>
    </div>
  </div>
</template>
