<script setup lang="ts">
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'

const props = withDefaults(defineProps<{
  fields: string[]
  modelValue?: string[]
}>(), {
  modelValue: () => [],
})

const emit = defineEmits<(e: 'update:modelValue', v: string[]) => void>()

const isOpen = ref(false)
function toggleDropdown() {
  isOpen.value = !isOpen.value
}
function toggleField(field: string) {
  const idx = props.modelValue.indexOf(field)
  const next = [...props.modelValue]
  if (idx >= 0) next.splice(idx, 1)
  else next.push(field)
  emit('update:modelValue', next)
}

const dropdownEl = ref<HTMLElement>()
onClickOutside(dropdownEl, () => (isOpen.value = false))
</script>

<template>
  <div ref="dropdownEl" class="relative inline-block">
    <button class="px-3 py-2 border rounded" @click="toggleDropdown">
      Filtry
      <svg class="inline-block size-4 ml-1" viewBox="0 0 20 20">
        <path d="M5 8l5 5 5-5H5z" />
      </svg>
    </button>

    <ul
      v-if="isOpen"
      class="absolute mt-2 bg-white border rounded shadow w-40 z-10 max-h-60 overflow-auto"
    >
      <li
        v-for="f in props.fields"
        :key="f"
        class="px-2 py-1 hover:bg-gray-100 flex items-center cursor-pointer"
        @click="toggleField(f)"
      >
        <input
          type="checkbox"
          :checked="props.modelValue.includes(f)"
          class="mr-2"
          readonly
        >
        {{ f }}
      </li>
    </ul>
  </div>
</template>
