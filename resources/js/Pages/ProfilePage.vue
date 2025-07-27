<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/services/api'
import { router } from '@inertiajs/vue3'
import BaseButton from '@/Components/BaseButton.vue'
import type { UserDetail } from '@/types/types'
import { useAuth } from '@/composables/useAuth'
import { useInteractions } from '@/composables/useInteractions'
import { formatDate } from '@/utilities/formatDate'
import InfoBlock from '@/Components/InfoBlock.vue'
import { useEvents } from '@/composables/useEvents'
import PaginationComponent from '@/Components/PaginationComponent.vue'

const props = defineProps<{ userId?: number }>()
const { events, page, meta } = useEvents()

const user = ref<UserDetail | null>(null)

async function fetchProfile() {
  try {
    const response = await api.get(
      props.userId ? `/profile/${props.userId}` : '/profile',
      { validateStatus: s => s < 400 || s === 302 },
    )
    if (response.status === 302 && response.data?.message === 'profile.redirected') {
      const target = response.data.redirect.replace(/^\/api/, '')
      router.visit(target, { replace: true })
      return
    }
    user.value = response.data.data
  } catch (error) {
    console.error('Błąd pobierania profilu:', error)
  }
}
onMounted(fetchProfile)

const { authUser, authUserId, logout } = useAuth()

const { isFollowing, followUser } = useInteractions()
function onFollow() {
  if (!props.userId) return
  followUser(props.userId)
}
</script>

<template>
  <div class="bg-gradient-to-tr from-brand-light to-brand-dark min-h-screen w-full flex items-start justify-center py-10">
    <div class="relative max-w-md w-full">
      <div class="absolute left-1/2 -translate-x-1/2">
        <img
          :src="user?.avatar_url || 'https://via.placeholder.com/150'"
          alt="Avatar"
          class="size-32 rounded-3xl border border-white object-cover shadow-lg"
        >
      </div>

      <div class="mt-16 bg-white rounded-[50px] shadow-lg p-6 space-y-6">
        <div class="space-y-4 mt-16 text-center">
          <h2 class="text-2xl font-bold">
            {{ user?.first_name || '' }} {{ user?.last_name || '' }}
          </h2>

          <div class="flex justify-around text-sm">
            <p class="font-bold">{{ user?.followers_count ?? 0 }} <span class="font-medium text-[#777777]">Obserwujący</span></p>
            <p class="font-bold">{{ user?.events_count ?? 0 }} <span class="font-medium text-[#777777]">Wydarzenia</span></p>
          </div>

          <div>
            <BaseButton
              v-if="authUserId === user?.id"
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
              {{ isFollowing ? 'Odobserwuj' : 'Obserwuj' }}
            </BaseButton>
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
          </div>

          <div class="pt-4 border-t border-gray-200 text-left text-sm space-y-1">
            <p><span class="font-semibold">ID:</span> {{ user?.id ?? '' }}</p>
            <p><span class="font-semibold">E-mail:</span> {{ user?.email || '' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
