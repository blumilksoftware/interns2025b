<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import BaseButton from '@/Components/BaseButton.vue'
import { useLogout } from '@/composables/useLogout'
import type { UserDetail } from '@/types/types'

const user = ref<UserDetail | null>(null)
const isLoading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get<{ data: UserDetail }>('/profile')
    user.value = response.data.data
  } catch (error) {
    console.error('Blad pobierania prfilu:', error)
  } finally {
    isLoading.value = false
  }
})

const { logout } = useLogout()
</script>

<template>
  <div class="max-w-md mx-auto p-6 bg-white rounded-2xl shadow space-y-6">
    <h1 class="text-3xl font-bold">profile</h1>

    <div v-if="user" class="space-y-2 text-lg">
      <p><span class="font-semibold">Imie:</span> {{ user.first_name }}</p>
      <p><span class="font-semibold">Nazwisko:</span> {{ user.last_name }}</p>
      <p><span class="font-semibold">E-mail:</span> {{ user.email }}</p>
    </div>

    <BaseButton v-if="user" class="w-full bg-red-600 text-white" @click="logout">
      Wyloguj się
    </BaseButton>
  </div>
</template>
