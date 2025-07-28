<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import api from '@/services/api'
import { router } from '@inertiajs/vue3'
import BaseButton from '@/Components/BaseButton.vue'
import InfoBlock from '@/Components/InfoBlock.vue'
import PaginationComponent from '@/Components/PaginationComponent.vue'
import type { UserDetail } from '@/types/types'
import { useAuth } from '@/composables/useAuth'
import { useInteractions } from '@/composables/useInteractions'
import { useEvents } from '@/composables/useEvents'
import { formatDate } from '@/utilities/formatDate'

const props = defineProps<{ userId?: number }>()

const { authUser, authUserId, logout } = useAuth()

const {
  fetchFollowings,
  toggleFollow,
  useIsFollowing,
} = useInteractions()

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
onMounted(async () => {
  await fetchProfile()
  await fetchFollowings()
})

const targetUserId = computed(() => props.userId ?? authUserId.value)
const isFollowingTarget = useIsFollowing('user', targetUserId)

const { events, page, meta } = useEvents()

async function onFollow() {
  if (!targetUserId.value) return
  await toggleFollow('user', targetUserId.value)
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
                :src="user?.avatar_url || 'https://via.placeholder.com/150'"
                alt="Avatar"
                class="size-32 max-md:-mt-36 rounded-3xl border border-white object-cover shadow-lg"
              >
            </div>
          </div>
          <div class="md:space-y-4 space-y-2 max-md:mt-14 text-center">
            <h2 class="text-2xl font-bold">
              {{ user?.first_name }} {{ user?.last_name }}
            </h2>

            <div class="flex justify-around text-sm">
              <p class="font-bold">{{ user?.followers_count ?? 0 }} <span class="font-medium text-[#777777]">Obserwujący</span></p>
              <p class="font-bold">{{ user?.events_count ?? 0 }} <span class="font-medium text-[#777777]">Wydarzenia</span></p>
            </div>

            <BaseButton
              v-if="targetUserId === authUserId"
              as="a"
              href="/settings"
              class="w-3/4 bg-black text-white"
            >
              Edytuj profil
            </BaseButton>
            <BaseButton
              v-else
              class="w-3/4 bg-black text-white"
              @click="onFollow"
            >
              {{ isFollowingTarget ? 'Odobserwuj' : 'Obserwuj' }}
            </BaseButton>
          </div>
        </div>
        <div>
          <h3 class="text-xl text-left text-[#120D26] font-semibold mt-5 mb-2">Wydarzenia</h3>
          <div class="space-y-4">
            <InfoBlock
              v-for="event in events"
              :key="event.id"
              :image-url="event.image_url"
              :title="event.title"
              :line1="event.location || 'Brak lokalizacji'"
              :line2="formatDate(event.start)"
              :line3="event.age_category || 'Brak'"
            />
            <p v-if="events.length === 0" class="col-span-full text-center text-gray-500">
              Brak wydarzeń do wyświetlenia.
            </p>
          </div>
          <PaginationComponent
            v-model:page="page"
            :last-page="meta.last_page"
          />
        </div>
        <BaseButton
          v-if="!props.userId && authUser"
          class="w-full bg-red-600 text-white"
          @click="logout"
        >
          Wyloguj się
        </BaseButton>


        <div class="pt-4 border-t border-gray-200 text-left text-sm space-y-1">
          <p><span class="font-semibold">ID:</span> {{ user?.id ?? '' }}</p>
          <p><span class="font-semibold">E-mail:</span> {{ user?.email || '' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
