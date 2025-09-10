<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = withDefaults(defineProps<{
  modelValue?: string[]
}>(), {
  modelValue: () => [],
})

const emit = defineEmits<(e: 'update:modelValue', newList: string[]) => void>()

const { t, te } = useI18n()

const model = computed(() => props.modelValue)

function remove(field: string) {
  emit('update:modelValue', props.modelValue.filter(f => f !== field))
}

function formatLabel(label: string) {
  return label.replace(/_/g, ' ').replace(/^./, c => c.toUpperCase())
}

function labelFor(field: string) {
  const key = `filters.${field}`
  return te(key) ? t(key) : formatLabel(field)
}
</script>

<template>
  <div v-if="model.length" class="absolute flex flex-wrap gap-2 mt-2">
    <span
      v-for="f in model"
      :key="f"
      class="bg-gray-200 px-2 py-1 rounded-full flex items-center text-xs"
    >
      {{ labelFor(f) }}
      <button
        class="ml-1 text-gray-600 hover:text-gray-800"
        :aria-label="t('filters.remove')"
        @click="remove(f)"
      >
        ×
      </button>
    </span>
  </div>
</template>
