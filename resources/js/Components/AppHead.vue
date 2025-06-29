<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'

interface Props {
  title: string
  description?: string
}

const props = defineProps<Props>()
</script>

<template>
  <Head>
    <title>{{ title }}</title>
    <meta name="description" :content="description || 'Default description'">
  </Head>
</template>
