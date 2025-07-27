<script setup lang="ts">
import { defineProps, defineEmits, computed } from 'vue'

interface Props {
  page: number
  lastPage: number
}

const props = defineProps<Props>()

const emit = defineEmits<(e: 'update:page', newPage: number) => void>()

const canPrev = computed(() => props.page > 1)
const canNext = computed(() => props.page < props.lastPage)

function goPrev() {
  if (canPrev.value) emit('update:page', props.page - 1)
}

function goNext() {
  if (canNext.value) emit('update:page', props.page + 1)
}
</script>

<template>
  <div v-if="props.lastPage > 1" class="flex justify-center items-center gap-4 mt-4">
    <button
      class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
      :disabled="!canPrev"
      @click="goPrev"
    >
      Poprzednia
    </button>
    <span>Strona {{ props.page }} z {{ props.lastPage }}</span>
    <button
      class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
      :disabled="!canNext"
      @click="goNext"
    >
      Następna
    </button>
  </div>
</template>
