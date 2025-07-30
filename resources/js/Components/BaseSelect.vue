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
}>()

const model = defineModel<string>()
const isFocused = ref(false)

const classes = computed(() =>
  [
    'block w-full rounded-md border px-4 py-2 text-sm transition focus:outline-none',
    props.error ? 'border-red-500 focus:border-red-500 focus:ring-red-300' : 'border-gray-300 focus:border-brand-light focus:ring focus:ring-brand-light',
  ].join(' '),
)
</script>

<template>
  <label v-if="props.label" :for="props.id" class="block text-gray-500 mb-1">
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
    <option disabled value="">-- wybierz --</option>
    <option v-for="opt in props.options" :key="opt.value" :value="opt.value">
      {{ opt.label }}
    </option>
  </select>
  <small v-if="props.error" class="text-red-600 text-sm">
    {{ props.error }}
  </small>
</template>
