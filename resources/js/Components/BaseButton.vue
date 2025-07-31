<script setup lang="ts">
defineProps<{
  type?: 'button' | 'submit' | 'reset'
}>()

const emit = defineEmits<(e: 'click', event: MouseEvent) => void>()

const handleClick = (event: MouseEvent) => {
  emit('click', event)
}
</script>

<template>
  <button
    :type="type"
    class="h-12 rounded-lg shadow-sm transition delay-100 duration-300 hover:scale-105"
    @click="handleClick"
  >
    <slot />
  </button>
</template>
