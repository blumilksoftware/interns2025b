<script lang="ts" setup>
import AppHead from '@/Components/AppHead.vue'

</script>

<template>
  <AppHead title="Dashboard" description="This is the dashboard page" />
  <div class="flex-auto grid grid-cols-2 gap-4 auto-rows-fr justify-center bg-gradient-to-tr from-[#44A08D] to-[#093637] h-screen">
    <div class="flex justify-center items-center text-center p-4">
      <slot name="left" />
    </div>
    <div class="flex justify-center items-center text-center p-4">
      <slot name="right" />
    </div>
  </div>
</template>

<style scoped>

</style>


