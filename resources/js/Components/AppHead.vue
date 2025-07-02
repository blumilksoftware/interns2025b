<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'

defineProps<{
  title: string
  description?: string
}>()
</script>

<template>
  <Head>
    <title>{{ title }}</title>
    <meta name="description" :content="description || 'Default description'">
  </Head>
</template>
