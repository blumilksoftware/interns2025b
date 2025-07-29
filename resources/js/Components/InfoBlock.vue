<script setup lang="ts">
import { UserIcon as DefaultIcon } from '@heroicons/vue/24/outline'
withDefaults(
  defineProps<{
    icon?: object | null
    imageUrl?: string | null
    title?: string | null
    infoItems?: string[] | null
  }>(),
  {
    icon: DefaultIcon,
    imageUrl: undefined,
    title: '',
    infoItems: () => [],
  },
)
</script>

<template>
  <div class="flex sm:gap-x-6 gap-x-3 items-center">
    <div
      class="flex shrink-0 bg-brand/10 sm:size-20 size-10 rounded-2xl items-center justify-center aspect-square overflow-hidden"
    >
      <img
        v-if="imageUrl"
        :src="imageUrl"
        alt="Obrazek"
        class="object-cover size-full"
      >
      <component
        :is="icon ?? DefaultIcon"
        v-else
        class="sm:size-10 size-5 text-brand"
      />
    </div>
    <div class="flex flex-col">
      <h2 class="sm:text-3xl text-left text-sm font-bold">{{ title }}</h2>
      <p
        v-for="(info, index) in infoItems"
        :key="index"
        class="sm:text-sm text-xs text-left text-gray-500"
      >
        {{ info }}
      </p>
    </div>
  </div>
</template>
