<template>
  <div>
    <label v-if="label">{{ label }}</label>
    <input
      class="bg-gray-100 rounded-md "
      :type="type"
      :placeholder="placeholder"
      :value="modelValue"
      v-bind="attrs"
      @input="$emit('update:modelValue', $event.target.value)"
    >
    <p v-if="error" class="text-red-700 size-2">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { useAttrs, defineProps } from 'vue'

const props = defineProps<{
  modelValue: string
  placeholder?: string
  type?: string
  label?: string
  error?: string
}>()

const attrs = useAttrs()
</script>
