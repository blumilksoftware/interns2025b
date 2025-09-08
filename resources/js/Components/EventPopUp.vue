<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3'
import { formatDate, formatTime } from '@/utilities/formatDate'
import BaseButton from '@/Components/BaseButton.vue'
import { useI18n } from 'vue-i18n'
import type { RawEvent } from '@/types/events'

defineProps<{ event: RawEvent }>()

const { t, locale } = useI18n()
</script>

<template>
  <div class="bg-white rounded-xl shadow-lg size-full overflow-hidden">
    <img
      :src="event.image_url ?? '/images/placeholder.png'"
      :alt="event.title"
      class="w-full h-24 object-cover"
    >

    <div class="px-3 py-2">
      <div class="space-y-2">
        <div class="flex items-center justify-between text-sm text-gray-500">
          <span>{{ formatDate(event.start, locale) }} - {{ formatTime(event.start, locale) }} </span>
          <span
            class="text-xs font-semibold px-2 py-1 rounded-full"
            :class="event.is_paid ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600'"
          >
            {{ event.is_paid ? t('event.paid') : t('event.free') }}
          </span>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 leading-tight">
            {{ event.title }}
          </h3>
        </div>
        <div class="">
          <p class="text-base font-medium text-gray-800">
            {{ event.location ?? t('event.no_location') }}
          </p>
          <p class="text-sm font-normal text-gray-500">
            {{ event.age_category ? t(`home.age_category.${event.age_category}`) : t('event.no_age_restriction') }}
          </p>
        </div>
        <div>
          <InertiaLink :href="`/events/${event.id}`">
            <BaseButton
              type="button"
              class="w-full bg-zinc-800 text-white justify-center font-bold px-10"
            >
              {{ t('event.details') }}
            </BaseButton>
          </InertiaLink>
        </div>
      </div>
    </div>
  </div>
</template>
