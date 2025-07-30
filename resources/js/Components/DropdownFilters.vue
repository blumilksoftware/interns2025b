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
    <button class="w-full h-12 px-3 mt-6 font-medium rounded-lg transition duration-100 ease-in-out focus:outline-none border bg-white text-gray-400 hover:bg-gray-50 border-none focus:ring-2 focus:ring-brand-light focus:border-indigo-500" @click="toggleDropdown">
      Filtry
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
