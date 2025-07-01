<template>
  <button
    :type="type"
    class="h-12 rounded-lg shadow-sm"
    @click="handleClick"
  >
    <slot />
  </button>
</template>

<script lang="ts" setup>
import { defineProps, defineEmits } from 'vue'

defineProps<{
  type?: 'button' | 'submit' | 'reset'
}>()

const emit = defineEmits<(e: 'click', event: MouseEvent) => void>()

const handleClick = (event: MouseEvent) => {
  emit('click', event)
}
</script>
