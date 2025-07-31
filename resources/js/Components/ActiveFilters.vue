<script setup lang="ts">
const props = withDefaults(defineProps<{
  modelValue?: string[]
}>(), {
  modelValue: () => [],
})

const emit = defineEmits<(e: 'update:modelValue', newList: string[]) => void>()

function remove(field: string) {
  emit('update:modelValue', props.modelValue.filter(f => f !== field))
}

function formatLabel(label: string) {
  return label.replace(/_/g, ' ').replace(/^./, c => c.toUpperCase())
}
</script>

<template>
  <div v-if="modelValue.length" class="absolute flex flex-wrap gap-2 mt-2">
    <span
      v-for="f in modelValue"
      :key="f"
      class="bg-gray-200 px-2 py-1 rounded-full flex items-center text-xs"
    >
      {{ formatLabel(f) }}
      <button
        class="ml-1 text-gray-600 hover:text-gray-800"
        aria-label="Usuń filtr"
        @click="remove(f)"
      >
        ×
      </button>
    </span>
  </div>
</template>
