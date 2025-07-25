<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue'
import BaseButton from '@/Components/BaseButton.vue'
import InfoBlock from '@/Components/InfoBlock.vue'
import Map from '@/Components/Map.vue'
import Navbar from '@/Components/Navbar.vue'
import type { RawEvent } from '@/types/events'
import { formatDate, formatTime, formatDay } from '@/utilities/formatDate'
import {
  ArrowRightCircleIcon,
  CheckCircleIcon,
  EnvelopeIcon,
  ShareIcon,
  StarIcon,
  CalendarIcon,
  UsersIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'
import api from '@/services/api'
import { computed, onMounted, ref } from 'vue'

const props = defineProps<{ eventId: number }>()

const rawEvent = ref<RawEvent | null>(null)
const loading = ref(true)

const event = computed(() => {
  if (!rawEvent.value) return null
  const r = rawEvent.value

  let name = 'Nieznany organizator'
  let role = ''

  if (r.owner_type?.includes('Models\\User') && r.owner) {
    name = r.owner.first_name ?? 'Nieznany'
    if (r.owner.last_name) name += ' ' + r.owner.last_name
    role = 'Użytkownik'
  } else if (r.owner_type?.includes('Models\\Organization') && r.owner?.name) {
    name = r.owner.name
    role = 'Organizacja'
  }

  return {
    bannerSrc: r.image_url ?? 'https://picsum.photos/640/480',
    eventDay: r.start ? formatDay(r.start) : 'Brak dnia',
    eventDate: r.start ? formatDate(r.start) : 'Brak daty',
    eventTime: r.start ? formatTime(r.start) : '',
    title: r.title,
    venueName: r.location ?? '',
    venueAddress: r.address ?? '',
    isPaid: r.is_paid,
    description: r.description ?? '',
    organizer: { name, role },
    participants: Array.isArray(r.participants) ? r.participants.length : 0,
  }
})

onMounted(async () => {
  try {
    const res = await api.get<{ data: RawEvent }>(`/events/${props.eventId}`)
    rawEvent.value = res.data.data
  } catch (err) {
    console.error('Błąd pobierania wydarzenia:', err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <AppHead title="Event" />
  <div v-if="!loading && event" class="w-full mb-16 sm:mb-12 flex-col">
    <Navbar class="mb-[72px]" />

    <div class="w-full h-[400px] bg-red-200 relative">
      <img :src="event.bannerSrc" alt="Event Banner" class="size-full object-cover">

      <div
        class="sm:hidden absolute left-1/2 -translate-x-1/2 bottom-[-32px] flex justify-between items-center bg-white rounded-full shadow px-6 py-3 w-fit max-w-full"
      >
        <p class="text-brand-dark font-medium whitespace-nowrap">
          {{ event.participants }} osób weźmie udział
        </p>
        <base-button class="ml-4 h-[28px] bg-brand-dark text-white px-4 py-1 rounded-lg text-sm whitespace-nowrap">
          Invite
        </base-button>
      </div>
    </div>

    <div class="flex flex-col items-center justify-center">
      <div class="w-full py-16 p-8 max-sm:pb-2 max-sm:pt-8 bg-white sm:shadow-lg sm:border sm:border-gray-300 flex justify-center items-center">
        <div class="sm:w-11/12 w-full space-y-2">
          <div class="flex max-sm:hidden justify-between items-center w-full text-center">
            <h2 class="text-2xl text-brand-light font-semibold">
              {{ event.eventDate }} - {{ event.eventTime }}
            </h2>
            <p v-if="event.isPaid" class="bg-[#0A84FF33] px-8 py-2 rounded-2xl font-semibold text-blue-600">
              Płatny
            </p>
            <p v-else class="bg-green-100 px-8 py-2 rounded-2xl font-semibold text-green-600">
              Darmowy
            </p>
          </div>

          <h1 class="sm:text-5xl text-3xl font-bold">{{ event.title }}</h1>

          <div class="flex max-sm:hidden max-xl:flex-col sm:justify-between items-start">
            <div>
              <h3 class="text-2xl font-medium">{{ event.venueName }}</h3>
              <h4 class="text-lg text-gray-400 font-normal">
                {{ rawEvent?.age_category ?? 'Brak ograniczenia wiekowego' }}
              </h4>
            </div>

            <div>
              <div class="hidden max-sm:flex-wrap max-sm:flex max-sm:gap-2 sm:flex lg:justify-end font-semibold text-xl flex-wrap gap-3">
                <base-button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">
                  <span class="inline-flex font-semibold items-center space-x-2">
                    <StarIcon class="size-6" />
                    <span>Zainteresowany(a)</span>
                  </span>
                </base-button>

                <base-button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">
                  <span class="inline-flex font-semibold items-center space-x-2">
                    <CheckCircleIcon class="size-6" />
                    <span>Wezmę udział</span>
                  </span>
                </base-button>

                <base-button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">
                  <span class="inline-flex font-semibold items-center space-x-2">
                    <EnvelopeIcon class="size-6" />
                    <span>Zaproś</span>
                  </span>
                </base-button>

                <base-button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">
                  <span class="inline-flex font-semibold items-center space-x-2">
                    <ShareIcon class="size-6" />
                  </span>
                </base-button>

                <base-button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">
                  <span class="inline-flex font-semibold items-center space-x-2">
                    <span>...</span>
                  </span>
                </base-button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex sm:w-11/12 sm:mt-6">
        <div class="flex w-full max-lg:flex-col lg:space-x-6 max-lg:space-y-6">
          <div class="flex w-full flex-col rounded-lg shadow-lg bg-white lg:py-16 p-8 max-sm:pt-2 lg:px-32 lg:w-4/6 max-w-full">
            <div class="flex flex-col sm:gap-y-8 gap-y-3 w-full">
              <InfoBlock :icon="UsersIcon" :title="`${event.participants} osób weźmie udział`" class="max-sm:hidden" />
              <InfoBlock :icon="CalendarIcon" :title="event.eventDate" :line2="`${event?.eventDay} ${event?.eventTime}`" />
              <InfoBlock :title="event.venueName" :line2="event.venueAddress" />

              <div class="w-full flex justify-between gap-4">
                <InfoBlock :icon="UserIcon" :title="event.organizer.name" :line1="event.organizer.role" />
                <div class="flex items-center justify-end">
                  <button class="bg-brand/10 text-brand px-3 text-sm sm:text-base py-1 rounded-xl">Obserwuj</button>
                </div>
              </div>

              <h1 class="font-medium sm:text-3xl text-xl text-[#120D26]">Informacje</h1>
              <p class="font-normal sm:text-xl text-sm text-[#120D26]">{{ event.description }}</p>
            </div>
          </div>

          <div class="flex flex-col space-y-6 lg:w-2/6 justify-start">
            <div v-if="event.isPaid" class="sm:hidden fixed bottom-0 left-0 w-full z-[1001] bg-white p-4 shadow-t">
              <base-button class="w-full text-base h-16 p-[15px] bg-brand-dark text-white rounded-2xl">
                <span class="inline-flex font-semibold items-center justify-center space-x-2">
                  <span>KUP BILET</span>
                  <ArrowRightCircleIcon class="size-6" />
                </span>
              </base-button>
            </div>

            <div v-if="event.isPaid" class="max-sm:hidden w-full rounded-lg shadow-lg !mt-0 bg-white lg:py-11 lg:px-16 p-8 lg:space-y-8 space-y-4">
              <p class="text-2xl font-medium">Bilety</p>
              <div class="text-center">
                <base-button class="w-full mb-1 text-base p-[15px] bg-brand-dark text-white rounded-2xl">
                  <span class="inline-flex font-semibold items-center justify-center space-x-2">
                    <span>KUP BILET</span>
                    <ArrowRightCircleIcon class="size-6" />
                  </span>
                </base-button>
              </div>
            </div>

            <div class="rounded-lg shadow-lg bg-white space-y-6 lg:py-11 lg:px-16 p-8">
              <div class="lg:-mx-16 lg:-mt-11 -mt-8 -mx-8 bg-red-200 h-96 rounded-2xl">
                <Map
                  v-if="rawEvent"
                  :center="[ rawEvent.latitude ?? 0, rawEvent.longitude ?? 0 ]"
                  disable-fetch
                />
              </div>
              <div class="content-center">
                <p class="font-medium sm:text-xl text-sm">{{ event.venueName }}</p>
                <p class="font-normal text-gray-400 sm:text-xl text-sm">{{ event.venueAddress }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="flex items-center justify-center h-full py-20">
    <p>Ładowanie...</p>
  </div>
</template>
