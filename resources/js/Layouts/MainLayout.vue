<script setup lang="ts">
defineSlots<{
  header: () => any
  content: () => any
  footer: () => any
}>()
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <slot name="header" />
    <slot name="content" />
    <slot name="footer" />
  </div>
</template>
