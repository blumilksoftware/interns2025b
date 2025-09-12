<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import api from '@/services/api'
import { Link as InertiaLink, router } from '@inertiajs/vue3'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/Footer.vue'
import BaseButton from '@/Components/BaseButton.vue'
import EventCard from '@/Components/EventCard.vue'
import BaseImage from '@/Components/BaseImage.vue'
import placeholderAvatar from '@/assets/PlaceholderAvatar.png'
import type { UserDetail } from '@/types/types'
import { useAuth } from '@/composables/useAuth'
import { useInteractions } from '@/composables/useInteractions'
import { useEvents } from '@/composables/useEvents'
import { formatDate } from '@/utilities/formatDate'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const props = defineProps<{ userId?: number }>()
const { authUserId } = useAuth()
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
const { events } = useEvents({ all: true })

onMounted(async () => {
  await fetchProfile()
  await fetchFollowings()
})

const targetUserId = computed<number>(() => props.userId ?? authUserId.value ?? 0)
const isMyProfile = computed(() => targetUserId.value === authUserId.value)
const isFollowingTarget = useIsFollowing('user', targetUserId)

const myEvents = computed(() =>
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
  <AppHead :title="t('profile.title')" />

  <div class="w-full flex flex-col min-h-screen">
    <div class="flex w-full mb-12">
      <Navbar>
        <h1 class="text-4xl font-bold">
          {{ user?.first_name }} {{ user?.last_name }}
        </h1>
      </Navbar>
    </div>

    <div class="flex flex-col items-center space-y-4">
      <BaseImage
        :src="user?.avatar_url ?? null"
        :placeholder="placeholderAvatar"
        alt="Avatar"
        class="size-32 rounded-3xl border border-white object-cover shadow-lg"
        width="128"
        height="128"
      />
      <div class="flex gap-x-8 text-center">
        <p class="font-bold">
          {{ user?.followers_count ?? 0 }}<br>
          <span class="font-medium text-gray-500">{{ t('profile.followers') }}</span>
        </p>
        <p class="font-bold">
          {{ user?.events_count ?? 0 }}<br>
          <span class="font-medium text-gray-500">{{ t('profile.events') }}</span>
        </p>
      </div>

      <div class="flex gap-x-4">
        <InertiaLink v-if="isMyProfile" href="/settings">
          <BaseButton class="bg-brand-light text-white px-6 py-3 rounded-xl shadow-md hover:scale-105 transition-transform">
            {{ t('profile.editProfile') }}
          </BaseButton>
        </InertiaLink>
        <BaseButton
          v-else
          class="bg-black text-white"
          @click="onFollow"
        >
          {{ isFollowingTarget ? t('profile.unfollow') : t('profile.follow') }}
        </BaseButton>
      </div>
    </div>

    <div class="md:w-5/6 mx-auto mt-12">
      <h3 class="text-xl font-semibold mb-4">{{ t('event.myEvents') }}</h3>
      <div v-if="myEvents.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <EventCard
          v-for="e in myEvents"
          :id="e.id"
          :key="e.id"
          :image-url="e.image_url"
          :start="formatDate(e.start)"
          :is-paid="e.is_paid"
          :title="e.title"
          :location="e.location"
          :age-category="e.age_category"
        />
      </div>
      <p v-else class="text-gray-500">{{ t('profile.noEvents') }}</p>
    </div>
    <Footer class="mt-16" />
  </div>
</template>
