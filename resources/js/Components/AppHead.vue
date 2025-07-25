<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

withDefaults(
  defineProps<{
    title: string
    description?: string
  }>(),
  { description: 'Default description' },
)
</script>

<template>
  <Head>
    <title>{{ title }}</title>
    <meta name="description" :content="description">
  </Head>
</template>
