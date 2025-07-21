<script setup lang="ts">
import { ref, computed, useSlots } from 'vue'

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

  appendPosition?: 'left' | 'right'
}>()

const isFocused = ref(false)
const slots = useSlots()

const paddingClasses = computed(() => {
  const base = ['pl-4', 'pr-4']
  if (slots.append) {
    if (props.appendPosition === 'left') {
      base[0] = 'pl-10'
    } else {
      base[1] = 'pr-10'
    }
  }
  return base.join(' ')
})

const currentPlaceholder = computed(() => {
  return isFocused.value && props.focusPlaceholder
    ? props.focusPlaceholder
    : props.placeholder
})
</script>

<template>
  <label
    :for="props.id"
    class="block text-gray-500 mb-1 !bg-transparent"
  >
    {{ props.label }}
  </label>

  <div class="relative">
    <input
      :id="props.id"
      v-model="model"
      :name="props.name"
      :type="props.type"
      :placeholder="currentPlaceholder"
      :class="[
        'w-full h-12 font-medium rounded-lg transition duration-100 ease-in-out text-brand-light focus:outline-none focus:ring-1 focus:ring-brand-light focus:border-brand-light hover:bg-gray-100 focus:bg-gray-100 border',
        paddingClasses,
        props.error ? 'border-red-500' : 'border-brand'
      ]"
      @focus="isFocused = true"
      @blur="isFocused = false"
    >
    <slot name="append" />
  </div>

  <small v-if="props.error" class="text-red-600 text-sm">
    {{ props.error }}
  </small>
</template>

<style scoped>
input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-calendar-picker-indicator {
  display: none;
  -webkit-appearance: none;
}
</style>
