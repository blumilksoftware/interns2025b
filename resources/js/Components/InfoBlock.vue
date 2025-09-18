<script setup lang="ts">
import type { Component } from 'vue'
import BaseImage from '@/Components/BaseImage.vue'
import placeholderAvatar from '@/assets/PlaceholderAvatar.png'

withDefaults(
  defineProps<{
    icon?: Component | ((...args: any[]) => any) | null
    imageUrl?: string | null
    title?: string | null
    header?: string | null
    infoItems?: string[] | null
    placeholder?: string | null
  }>(),
  {
    icon: null,
    imageUrl: undefined,
    title: '',
    header: '',
    infoItems: () => [],
    placeholder: undefined,
  },
)
</script>

<template>
  <div class="flex sm:gap-x-6 gap-x-3 items-center">
    <div
      class="flex shrink-0 bg-brand/10 sm:size-20 size-10 rounded-2xl items-center justify-center aspect-square overflow-hidden"
    >
      <BaseImage
        v-if="imageUrl"
        :src="imageUrl"
        :alt="title ?? header ?? 'image'"
        class="object-cover size-full"
        width="80"
        height="80"
      />

      <component
        :is="icon"
        v-else-if="icon"
        class="sm:size-10 size-5 text-brand"
      />
      <BaseImage
        v-else
        :src="placeholder ?? placeholderAvatar"
        :alt="title ?? header ?? 'placeholder'"
        class="object-cover size-full"
        width="80"
        height="80"
      />
    </div>

    <div class="flex flex-col">
      <p v-if="header" class="sm:text-sm text-xs text-left text-gray-500">{{ header }}</p>
      <h2 v-if="title" class="sm:text-3xl text-left text-sm font-bold">{{ title }}</h2>

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
