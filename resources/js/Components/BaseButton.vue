<script setup lang="ts">
import { type PropType } from 'vue'

const props = defineProps({
  type: {
    type: String as PropType<'button' | 'submit' | 'reset'>,
    default: 'button',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits<(e: 'click', event: MouseEvent) => void>()

function handleClick(event: MouseEvent) {
  if (!props.disabled) {
    emit('click', event)
  }
}
</script>

<template>
  <button
    :type="props.type"
    :disabled="props.disabled"
    class="h-12 rounded-lg shadow-sm transition delay-100 duration-300 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
    @click="handleClick"
  >
    <slot />
  </button>
</template>
