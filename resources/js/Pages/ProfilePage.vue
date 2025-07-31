<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import api from '@/services/api'
import { Link as InertiaLink, router } from '@inertiajs/vue3'
import BaseButton from '@/Components/BaseButton.vue'
import InfoBlock from '@/Components/InfoBlock.vue'
import type { UserDetail } from '@/types/types'
import { useAuth } from '@/composables/useAuth'
import { useInteractions } from '@/composables/useInteractions'
import { useEvents } from '@/composables/useEvents'
import { formatDate, formatTime } from '@/utilities/formatDate'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid'

const props = defineProps<{ userId?: number }>()

const { authUserId, logout } = useAuth()

const user = ref<UserDetail | null>(null)
async function fetchProfile() {
  const endpoint = props.userId ? `/profile/${props.userId}` : '/profile'
  const res = await api.get(endpoint, { validateStatus: s => s < 400 || s === 302 })
  if (res.status === 302 && res.data.redirect) {
    router.visit(res.data.redirect.replace(/^\/api/, ''), { replace: true })
    return
  }
  user.value = res.data.data
}

const { fetchFollowings, toggleFollow, useIsFollowing } = useInteractions()
const { events } = useEvents()

onMounted(async () => {
  await fetchProfile()
  await fetchFollowings()
})

const targetUserId = computed(() => props.userId ?? authUserId.value)
const isMyProfile = computed(() => targetUserId.value === authUserId.value)
const isFollowingTarget = useIsFollowing('user', targetUserId)

const eventsByOwner = computed(() =>
  events.value.filter(e => e.owner_id === targetUserId.value),
)

async function onFollow() {
  if (!targetUserId.value || !user.value) return

  const wasFollowing = isFollowingTarget.value

  await toggleFollow('user', targetUserId.value)

  user.value.followers_count = (user.value.followers_count ?? 0) + (wasFollowing ? -1 : 1)
}
</script>

<template>
  <div class="bg-gradient-to-tr from-brand-light to-brand-dark min-h-screen w-full flex items-start justify-center py-10">
    <div class="w-11/12 max-md:w-full">
      <div class="mt-16 bg-white rounded-[50px] shadow-lg p-6 md:p-12 space-y-6">
        <div class="md:flex md:space-x-4">
          <div class="max-md:relative">
            <div class="max-md:absolute max-md:left-1/2 max-md:-translate-x-1/2 max-md:-translate-y-1/2">
              <img
                :src="user?.avatar_url ?? 'https://via.placeholder.com/150'"
                alt="Avatar"
                class="size-32 aspect-square max-md:-mt-36 rounded-3xl border border-white object-cover shadow-lg"
              >
            </div>
          </div>
          <div class="md:space-y-4 space-y-2 content-center max-md:mt-14 text-center">
            <h2 class="text-2xl font-bold">
              {{ user?.first_name }} {{ user?.last_name }}
            </h2>

            <div class="flex w-full justify-center gap-x-4 text-sm">
              <p class="font-bold mb-2">
                {{ user?.followers_count ?? 0 }}<br>
                <span class="font-medium text-[#777777]">Obserwujący</span>
              </p>
              <p class="font-bold">
                {{ user?.events_count ?? 0 }}<br>
                <span class="font-medium text-[#777777]">Wydarzenia</span>
              </p>
            </div>
            <InertiaLink v-if="isMyProfile" href="/settings">
              <BaseButton
                class="w-3/4 bg-black text-white"
              >
                Edytuj profil
              </BaseButton>
            </InertiaLink>
            <BaseButton
              v-else
              class="w-3/4 bg-black text-white"
              @click="onFollow"
            >
              {{ isFollowingTarget ? 'Przestań obserwować' : 'Obserwuj' }}
            </BaseButton>
          </div>
        </div>
        <div>
          <h3 class="text-xl text-left text-[#120D26] font-semibold mt-5 mb-2">
            Wydarzenia
          </h3>
          <div class="space-y-4 ">
            <InfoBlock
              v-for="event in eventsByOwner"
              :key="event.id"
              :header="`${formatDate(event.start)} - ${formatTime(event.start)}`"
              :title="event.title"
              :image-url="event.image_url"
              :info-items="[
                event.location ?? 'Brak lokalizacji',
                event.age_category ?? 'Brak kategorii',
              ]"
            />
            <div v-if="!eventsByOwner.length" class="flex flex-col size-full space-y-20 mt-10 align-bottom place-content-center ">
              <magnifying-glass-icon class="h-64 " />
              <p class="col-span-full  text-center text-gray-500">
                Ten użytkownik nie utworzył jeszcze żadnego wydarzenia.
              </p>
            </div>
          </div>
        </div>
        <BaseButton
          v-if="!props.userId && user"
          class="max-sm:w-full px-4 bg-red-600 text-white"
          @click="logout"
        >
          Wyloguj się
        </BaseButton>

      </div>
    </div>
  </div>
</template>
