<script setup lang="ts">
import { ref, watch } from 'vue'
import defaultPlaceholder from '@/assets/Placeholder.png'

const props = withDefaults(
  defineProps<{
    src?: string | null
    placeholder?: string | null
    alt?: string
    class?: string
    width?: string | number
    height?: string | number
    loading?: 'lazy' | 'eager'
    decoding?: 'sync' | 'async' | 'auto'
  }>(),
  {
    src: undefined,
    placeholder: undefined,
    alt: '',
    class: '',
    loading: 'lazy',
    decoding: 'async',
  },
)

const imgSrc = ref<string>(props.src ?? props.placeholder ?? defaultPlaceholder)

watch(
  () => [props.src, props.placeholder],
  ([newSrc, newPlaceholder]) => {
    imgSrc.value = newSrc ?? newPlaceholder ?? defaultPlaceholder
  },
)

function onError() {
  const target = props.placeholder ?? defaultPlaceholder
  if (imgSrc.value !== target) imgSrc.value = target
}
</script>

<template>
  <img
    :src="imgSrc"
    :alt="props.alt ?? ''"
    :class="props.class"
    :width="props.width"
    :height="props.height"
    :loading="props.loading"
    :decoding="props.decoding"
    @error="onError"
  >
</template>
