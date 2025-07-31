<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import BaseButton from '@/Components/BaseButton.vue'
import InfoBlock from '@/Components/InfoBlock.vue'
import Map from '@/Components/Map.vue'
import type { RawEvent } from '@/types/events'
import api from '@/services/api'
import { formatDate, formatTime, formatDay } from '@/utilities/formatDate'
import { useAuth } from '@/composables/useAuth'
import { useInteractions } from '@/composables/useInteractions'
import {
  CheckCircleIcon,
  ArrowRightCircleIcon,
  CalendarIcon,
  UsersIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps<{ eventId: number }>()

const { authUserId } = useAuth()

const {
  toggleFollow,
  participateEvent,
  useIsFollowing,
  useIsParticipating,
  fetchFollowings,
} = useInteractions()

const event = ref<RawEvent | null>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get<{ data: RawEvent }>(`/events/${props.eventId}`)
    event.value = res.data.data
  } catch (error) {
    alert('Błąd pobierania wydarzenia')
  } finally {
    loading.value = false
  }
  if (authUserId.value) {
    await fetchFollowings()
  }
})

const ownerIdRef   = computed(() => event.value?.owner_id ?? 0)
const eventIdRef   = computed(() => props.eventId)

const isOwnerFollowed  = useIsFollowing('user', ownerIdRef)
const isParticipating  = useIsParticipating('event', eventIdRef)

async function handleToggleFollow() {
  if (ownerIdRef.value) {
    await toggleFollow('user', ownerIdRef.value)
  }
}

async function handleParticipate() {
  if (!event.value) return
  await participateEvent(event.value.id)

  const wasParticipating = isParticipating.value
  event.value.participation_count = wasParticipating
    ? Math.max((event.value.participation_count ?? 1) - 1, 0)
    : (event.value.participation_count ?? 0) + 1
}

const ownerInfo = computed(() => ({
  imageUrl: event.value?.owner?.avatar_url ?? event.value?.owner?.group_url ?? '',
  title: [
    event.value?.owner?.first_name,
    event.value?.owner?.last_name,
    (event.value?.owner as any)?.name,
  ]
    .filter(Boolean)
    .join(' ')
    .trim() ?? 'Nieznany',
  ownerType: event.value?.owner_type ?? '',
}))

const participantsMessage = computed(() => {
  const count = event.value?.participation_count ?? 0
  return count > 0
    ? `Liczba uczestników: ${count}`
    : 'Nikt nie weźmie udziału.'
})

</script>

<template>
  <AppHead :title="event?.title ?? 'Wydarzenie'" />
  <div v-if="!loading && event" class="w-full mb-16 sm:mb-12 flex-col">
    <Navbar class="mb-[72px]" />

    <div class="w-full h-[400px] relative bg-gray-200">
      <img :src="event.image_url ?? 'https://picsum.photos/640/480'" alt="Event Banner" class="size-full object-cover">

      <div
        class="sm:hidden absolute left-1/2 -translate-x-1/2 bottom-[-32px] flex justify-between items-center bg-white rounded-full shadow px-6 py-3 w-fit max-w-full"
      >
        <p class="text-brand-dark font-medium whitespace-nowrap">
          {{ participantsMessage }}
        </p>
        <BaseButton v-if="authUserId"
                    class="ml-4 h-7 bg-brand-dark text-white px-4 py-1 rounded-lg text-sm whitespace-nowrap"
                    @click="handleParticipate"
        >
          {{ isParticipating ? 'Rezygnuj' : 'Wezmę udział' }}
        </BaseButton>
      </div>
    </div>

    <div class="flex flex-col items-center justify-center">
      <div class="w-full py-16 p-8 bg-white sm:shadow-lg sm:border sm:border-gray-300 flex justify-center items-center">
        <div class="sm:w-11/12 w-full space-y-2">
          <div class="flex max-sm:hidden justify-between items-center w-full text-center">
            <h2 class="text-2xl text-brand-light font-semibold">
              {{ formatDate(event.start) }} - {{ formatTime(event.start) }}
            </h2>
            <p
              class="inline-block px-8 py-2 rounded-2xl font-semibold"
              :class="event.is_paid ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600'"
            >
              {{ event.is_paid ? 'Płatny' : 'Darmowy' }}
            </p>
          </div>

          <h1 class="sm:text-5xl text-3xl font-bold">{{ event.title }}</h1>

          <div class="flex max-sm:hidden max-xl:flex-col sm:justify-between items-start w-full">
            <div>
              <h3 class="text-2xl font-medium">{{ event.location || 'Brak lokalizacji' }}</h3>
              <h4 class="text-lg text-gray-400 font-normal">
                {{ event.age_category ?? 'Brak ograniczenia wiekowego' }}
              </h4>
            </div>

            <div>
              <div class="max-sm:flex-wrap justify-end font-semibold text-xl flex-wrap gap-3">
                <BaseButton v-if="authUserId"
                            class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded"
                            @click="handleParticipate"
                >
                  <span class="inline-flex items-center space-x-2">
                    <CheckCircleIcon class="size-6" />
                    <span>{{ isParticipating ? 'Rezygnuj' : 'Wezmę udział' }}</span>
                  </span>
                </BaseButton>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex sm:w-11/12 sm:mt-6">
        <div class="flex w-full max-lg:flex-col lg:space-x-6 max-lg:space-y-6">
          <div class="flex w-full flex-col rounded-lg shadow-lg bg-white lg:py-16 p-8 max-sm:pt-2 lg:px-32 lg:w-4/6 max-w-full">
            <div class="flex flex-col sm:gap-y-8 gap-y-3 w-full">
              <InfoBlock
                :icon="UsersIcon"
                :title="`${ participantsMessage }`" class="max-sm:hidden"
              />
              <InfoBlock
                :icon="CalendarIcon"
                :title="formatDate(event.start)"
                :info-items="[`${formatDay(event.start)} ${formatTime(event.start)}`]"
              />
              <InfoBlock
                :title="event.location"
                :info-items="[event.address]"
              />

              <div class="w-full flex justify-between gap-4">
                <InertiaLink :href="`/profile/${ownerIdRef}`" class="hover:scale-105 transition-transform">
                  <InfoBlock
                    :image-url="ownerInfo.imageUrl"
                    :title="ownerInfo.title"
                    :info-items="[ownerInfo.ownerType]"
                  />
                </InertiaLink>
                <div v-if="authUserId" class="flex items-center justify-end">
                  <BaseButton
                    class="bg-brand/10 h-10 text-brand px-3 text-sm sm:text-base py-1 rounded-xl"
                    @click="handleToggleFollow"
                  >
                    <span>{{ isOwnerFollowed ? 'Przestań Obserwować' : 'Obserwuj' }}</span>
                  </basebutton>
                </div>
              </div>

              <h1 class="font-medium sm:text-3xl text-xl text-[#120D26]">Informacje</h1>
              <p class="font-normal sm:text-xl text-sm text-[#120D26]">{{ event.description }}</p>
            </div>
          </div>

          <div class="flex flex-col space-y-6 lg:w-2/6 justify-start">
            <div v-if="event.is_paid" class="sm:hidden fixed bottom-0 left-0 w-full z-[1001] bg-white p-4 shadow-t">
              <base-button class="w-full text-base h-16 p-[15px] bg-brand-dark text-white rounded-2xl">
                <span class="inline-flex font-semibold items-center justify-center space-x-2">
                  <span>KUP BILET</span>
                  <ArrowRightCircleIcon class="size-6" />
                </span>
              </base-button>
            </div>

            <div v-if="event.is_paid" class="max-sm:hidden w-full rounded-lg shadow-lg !mt-0 bg-white lg:py-11 lg:px-16 p-8 lg:space-y-8 space-y-4">
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
                  :events="[event]"
                  :center="[event.latitude ?? 0, event.longitude ?? 0]"
                  disable-fetch
                  class="min-h-96"
                />
              </div>
              <div class="content-center">
                <p class="font-medium sm:text-xl text-sm">{{ event.location }}</p>
                <p class="font-normal text-gray-400 sm:text-xl text-sm">{{ event.address }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
