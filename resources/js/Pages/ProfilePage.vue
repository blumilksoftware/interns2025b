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
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

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
  <div class="bg-gradient-to-tr from-brand-light to-brand-dark min-h-screen w-full flex items-start justify-center">
    <div class="w-11/12 max-md:w-full">
      <div class="mt-16 sm:8 bg-white rounded-[2.5rem] min-h-[50rem] shadow-lg p-6 md:p-12 space-y-6">
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
          <div class="md:space-y-4 space-y-2 content-center min-w-80 max-md:mt-14 text-center">
            <h2 class="text-2xl font-bold">
              {{ user?.first_name }} {{ user?.last_name }}
            </h2>

            <div class="flex w-full justify-center gap-x-4 text-sm">
              <p class="font-bold mb-2">
                {{ user?.followers_count ?? 0 }}<br>
                <span class="font-medium text-[#777777]">{{ t('profile.followers') }}</span>
              </p>
              <p class="font-bold">
                {{ user?.events_count ?? 0 }}<br>
                <span class="font-medium text-[#777777]">{{ t('profile.events') }}</span>
              </p>
            </div>
            <InertiaLink v-if="isMyProfile" href="/settings">
              <BaseButton
                class="w-3/4 bg-black text-white"
              >
                {{ t('profile.editProfile') }}
              </BaseButton>
            </InertiaLink>
            <BaseButton
              v-else
              class="w-3/4 bg-black text-white"
              @click="onFollow"
            >
              {{ isFollowingTarget ? t('profile.unfollow') : t('profile.follow') }}
            </BaseButton>
          </div>
        </div>
        <div>
          <h3 class="text-xl text-left text-[#120D26] font-semibold mt-5 mb-2">
            {{ t('profile.events') }}
          </h3>
          <div class="space-y-4 ">
            <InertiaLink
              v-for="event in eventsByOwner"
              :key="event.id"
              :href="`/events/${event.id}`"
            >
              <InfoBlock
                :id="event.id"
                :key="event.id"
                :header="`${formatDate(event.start)} - ${formatTime(event.start)}`"
                :title="event.title"
                :image-url="event.image_url"
                :info-items="[
                  event.location ?? t('event.noLocation'),
                  event.age_category ?? t('event.noAgeLimit')
                ]"
                class="bg-white w-1/3 p-4 hover:scale-105 transition-transform"
              />
            </InertiaLink>
            <div v-if="!eventsByOwner.length" class="flex flex-col size-full space-y-20 mt-10 align-bottom place-content-center ">
              <magnifying-glass-icon class="h-64 " />
              <p class="col-span-full  text-center text-gray-500">
                {{ t('profile.noEvents') }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex flex-col items-center gap-2 mt-6">
          <InertiaLink v-if="isMyProfile" href="/settings">
            <BaseButton class="px-6 py-2 bg-black text-white">
              {{ t('profile.editProfile') }}
            </BaseButton>
          </InertiaLink>

          <BaseButton
            v-else
            class="px-6 py-2 bg-black text-white"
            @click="onFollow"
          >
            {{ isFollowingTarget ? t('profile.unfollow') : t('profile.follow') }}
          </BaseButton>

          <InertiaLink href="/">
            <BaseButton class="px-6 py-2 bg-brand-light text-white">
              {{ t('profile.goHome') }}
            </BaseButton>
          </InertiaLink>
        </div>
      </div>
    </div>
  </div>
</template>
