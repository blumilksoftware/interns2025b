<script setup lang="ts">
import { ref, computed } from 'vue'

type InputType = 'text' | 'email' | 'password' | 'number' | 'search' | 'date';

const model = defineModel<string>()

const props = defineProps<{
  id: string
  name: string
  label?: string
  type?: InputType
  placeholder?: string
  focusPlaceholder?: string
  error?: string | null
}>()

const isFocused = ref(false)

const currentPlaceholder = computed(() => {
  if (isFocused.value && props.focusPlaceholder) {
    return props.focusPlaceholder
  }
  return props.placeholder
})
</script>

<template>
  <label :for="id" class="block text-gray-500 mb-1">
    {{ label }}
  </label>

  <div class="relative">
    <input
      :id="id"
      v-model="model"
      :name="name"
      :type="type"
      :placeholder="currentPlaceholder"
      :class="[
        'w-full h-12 px-4 pr-10 font-medium rounded-lg transition duration-100 ease-in-out',
        'text-brand-light focus:outline-none focus:ring-1 focus:ring-brand-light focus:border-brand-light hover:bg-gray-100 focus:bg-gray-100',
        error ? 'border border-red-500' : 'border border-brand',
      ]"
      @focus="isFocused = true"
      @blur="isFocused = false"
    >
    <slot name="append" />
  </div>
  <small v-if="error" class="text-red-600 text-sm">
    {{ error }}
  </small>
</template>
