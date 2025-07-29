<script setup lang="ts">
import { UserIcon as DefaultIcon } from '@heroicons/vue/24/outline'
const props = withDefaults(
  defineProps<{
    icon?: object | null
    imageUrl?: string | null
    title?: string | null
    info1?: string | null
    info2?: string | null
    note?: string | null
  }>(),
  {
    icon: DefaultIcon,
    imageUrl: undefined,
    title: '',
    info1: '',
    info2: '',
    note: '',
  },
)
</script>

<template>
  <div class="flex sm:gap-x-6 gap-x-3 items-center">
    <div
      class="flex shrink-0 bg-brand/10 sm:size-20 size-10 rounded-2xl items-center justify-center aspect-square overflow-hidden"
    >
      <img
        v-if="props.imageUrl"
        :src="props.imageUrl"
        alt="Obrazek"
        class="object-cover size-full"
      >
      <component
        :is="props.icon || DefaultIcon"
        v-else
        class="sm:size-10 size-5 text-brand"
      />
    </div>
    <div class="flex flex-col">
      <h2 class="sm:text-3xl text-left text-sm font-bold">{{ props.title }}</h2>
      <p v-if="props.info1" class="sm:text-sm text-xs text-left text-gray-500">{{ props.info1 }}</p>
      <p v-if="props.info2" class="sm:text-sm text-xs text-left text-gray-500">{{ props.info2 }}</p>
      <p v-if="props.note" class="sm:text-sm text-xs text-left text-gray-500">{{ props.note }}</p>
    </div>
  </div>
</template>
