<script setup lang="ts">
import { ref, computed } from 'vue'

interface SelectOption {
  label: string
  value: string
}

const props = defineProps<{
  id: string
  name: string
  label?: string
  options: SelectOption[]
  error?: string | null
  placeholderKey?: string
}>()

const model = defineModel<string>()
const isFocused = ref(false)

const classes = computed(() =>
  [
    'w-full h-12 font-medium bg-white rounded-lg transition duration-100 ease-in-out focus:outline-none border pl-4 pr-4 text-brand-light',
    props.error
      ? 'border-red-500 hover:bg-red-50 focus:bg-red-50 focus:ring-1 focus:ring-red-300 focus:border-red-500'
      : 'border-brand hover:bg-gray-100 focus:bg-gray-100 focus:ring-1 focus:ring-brand-light focus:border-brand-light',
  ].join(' '),
)
</script>

<template>
  <label v-if="props.label" :for="props.id" class="block text-gray-600 mb-1.5 font-medium">
    {{ props.label }}
  </label>
  <select
    :id="props.id"
    v-model="model"
    :name="props.name"
    :class="classes"
    @focus="isFocused = true"
    @blur="isFocused = false"
  >
    <option v-for="opt in props.options" :key="opt.value" :value="opt.value">
      {{ opt.label }}
    </option>
  </select>
  <small v-if="props.error" class="text-red-600 text-sm">
    {{ props.error }}
  </small>
</template>
