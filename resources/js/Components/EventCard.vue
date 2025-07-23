<script setup lang="ts">
import { computed } from 'vue'
import type { RawEvent } from '@/types/events'
import { formatDate } from '@/utilities/formatDate'

const props = defineProps<{
  event: RawEvent
}>()

const { event: evt } = props

const bannerSrc = computed(
  () => evt.image_url ?? 'https://picsum.photos/200/300',
)

const formattedDate = computed(() => formatDate(evt.start))

const isPaid = computed(() => evt.is_paid)
const title = computed(() => evt.title)
const venueName = computed(() => evt.location ?? 'Brak lokalizacji')
const ageCategory= computed(() => evt.age_category)
</script>

<template>
  <InertiaLink
    :href="`/event/${evt.id}`"
    class="block max-w-sm bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
  >
    <img
      :src="bannerSrc"
      alt="Event Banner"
      class="w-full h-48 object-cover"
    >

    <div class="p-4 space-y-2">
      <div class="flex items-center justify-between">
        <time class="text-sm text-gray-500">{{ formattedDate }}</time>
        <span
          class="text-xs font-semibold px-2 py-1 rounded-full"
          :class="isPaid ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600'"
        >
          {{ isPaid ? 'Płatny' : 'Darmowy' }}
        </span>
      </div>

      <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>

      <p class="text-sm text-gray-600 flex items-center">
        {{ venueName }}
      </p>

      <p class="text-sm text-gray-600">
        Ograniczenie wiekowe: <span class="font-medium">{{ ageCategory ?? 'Brak' }}</span>
      </p>
    </div>
  </InertiaLink>
</template>
