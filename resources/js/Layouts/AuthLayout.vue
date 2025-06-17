<template>
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-r from-emerald-400 to-cyan-400">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-sm">
      <slot />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'AuthLayout',
})
</script>
//
