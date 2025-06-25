<template>
  <button
    :type="type"
    class="h-12 bg-black rounded-lg shadow-sm shadow-[#375DFB] text-white font-bold"
    @click="handleClick"
  >
    <slot />
  </button>
</template>

<script lang="ts" setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps<{
  type?: 'button' | 'submit' | 'reset'
}>()

const emit = defineEmits<{
  (e: 'click', event: MouseEvent): void
}>()

const handleClick = (event: MouseEvent) => {
  emit('click', event)
}
</script>
