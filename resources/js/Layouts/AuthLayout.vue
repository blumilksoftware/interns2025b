<script lang="ts" setup>
import AppHead from '@/Components/AppHead.vue'

</script>

<template>
  <AppHead title="Dashboard" description="This is the dashboard page" />
  <div class="flex flex-col items-center justify-center bg-gray-50 h-screen ">
    <div class="w-full max-w-md">
      <slot name="auth" />
    </div>
  </div>
</template>

