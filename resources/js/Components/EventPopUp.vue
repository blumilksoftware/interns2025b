<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3'
import { formatDate, formatTime } from '@/utilities/formatDate'
import BaseButton from '@/Components/BaseButton.vue'
import type { EventMarker } from '@/types/events'

const props = defineProps<{ event: EventMarker }>()
</script>

<template>
  <div class="bg-white rounded-xl shadow-lg size-full overflow-hidden">
    <img
      :src="props.event.image_url ?? '/images/placeholder.png'"
      :alt="props.event.title"
      class="w-full h-24 object-cover"
    >

    <div class="px-3 py-2">
      <div class="space-y-2">
        <div class="flex items-center justify-between text-sm text-gray-500">
          <span>{{ formatDate(props.event.start) }} - {{ formatTime(props.event.start) }} </span>
          <span
            class="text-xs font-semibold px-2 py-1 rounded-full"
            :class="props.event.is_paid ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600'"
          >
            {{ props.event.is_paid ? 'Płatny' : 'Darmowy' }}
          </span>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 leading-tight">
            {{ props.event.title }}
          </h3>
        </div>
        <div class="">
          <p class="text-base font-medium text-gray-800">
            {{ props.event.location ?? 'Brak lokalizacji' }}
          </p>
          <p class="text-sm font-normal text-gray-500">
            {{ props.event.age_category ? `Ogr. wiek.: ${props.event.age_category}` : 'Brak ograniczeń' }}
          </p>
        </div>
        <div>
          <InertiaLink :href="`/event/${props.event.id}`">
            <BaseButton
              type="button"
              class="w-full ! bg-zinc-800 !text-white justify-center font-bold px-10 "
            >
              Zobacz szczegóły
            </BaseButton>
          </InertiaLink>
        </div>
      </div>
    </div>
  </div>
</template>
