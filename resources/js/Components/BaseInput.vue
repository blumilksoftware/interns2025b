<script setup lang="ts">
import { ref, computed, useSlots, onBeforeMount } from 'vue'

onBeforeMount(() => {
  if (props.type === 'date' && !model.value) {
    model.value = new Date().toISOString().slice(0,10)
  }
})

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
  variant?: 'default' | 'event'
}>()

const isFocused = ref(false)
const slots = useSlots()

const padding = computed(() => {
  const left = slots.append && props.appendPosition === 'left' ? 'pl-10' : 'pl-4'
  const right = slots.append && props.appendPosition !== 'left' ? 'pr-10' : 'pr-4'
  return `${left} ${right}`
})

const placeholderText = computed(() =>
  isFocused.value && props.focusPlaceholder ? props.focusPlaceholder : props.placeholder,
)

const theme = computed(() => {
  if (props.variant === 'event') {
    return 'bg-white text-gray-400 hover:bg-gray-50 border-none focus:ring-2 focus:ring-brand-light focus:border-indigo-500'
  }
  return 'text-brand-light border-brand hover:bg-gray-100 focus:bg-gray-100 focus:ring-1 focus:ring-brand-light focus:border-brand-light'
})
</script>

<template>
  <label v-if="props.label" :for="props.id" class="block text-gray-500 mb-1">
    {{ props.label }}
  </label>

  <div class="relative">
    <input
      :id="props.id"
      v-model="model"
      :name="props.name"
      :type="props.type"
      :placeholder="placeholderText"
      :class="[ 'w-full h-12 font-medium rounded-lg transition duration-100 ease-in-out focus:outline-none border', padding, theme, props.error ? 'border-red-500' : '' ]"
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
input[type="date"]::-webkit-calendar-picker-indicator {
  opacity: 0;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}
</style>
