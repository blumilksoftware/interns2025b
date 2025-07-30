<script setup lang="ts">
import { computed } from 'vue'

const { page, lastPage } = defineProps<{
  page: number
  lastPage: number
}>()

const emit = defineEmits<(e: 'update:page', newPage: number) => void>()

const canPrev = computed(() => page > 1)
const canNext = computed(() => page < lastPage)

function goPrev() {
  if (canPrev.value) emit('update:page', page - 1)
}

function goNext() {
  if (canNext.value) emit('update:page', page + 1)
}
</script>

<template>
  <div v-if="lastPage > 1" class="flex justify-center items-center gap-4 mt-4">
    <button
      class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
      :disabled="!canPrev"
      @click="goPrev"
    >
      Poprzednia
    </button>
    <span>Strona {{ page }} z {{ lastPage }}</span>
    <button
      class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
      :disabled="!canNext"
      @click="goNext"
    >
      Następna
    </button>
  </div>
</template>
