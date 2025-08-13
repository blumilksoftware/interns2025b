<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import BaseInput from '@/Components/BaseInput.vue'
import PasswordInput from '@/Components/PasswordInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import Footer from '@/Components/Footer.vue'
import { useApiForm } from '@/composables/useApiForm'
import api from '@/services/api'
import type { UserDetail } from '@/types/types'
import { ref, onMounted } from 'vue'

const activeTab = ref<'profile' | 'password'>('profile')
const loadError = ref<string | null>(null)
const user = ref<UserDetail | null>(null)

const { formData: profileForm, fieldErrors: profileErrors, isSubmitting: isProfileSubmitting, isSuccess: isProfileSuccess, globalMessage: profileMessage, submitForm: submitProfile } = useApiForm<{ first_name: string; last_name: string }, { message: string }>(
  { first_name: '', last_name: '' },
  {
    endpoint: '/api/profile',
    method: 'put',
    onSuccess: (res) => { profileMessage.value = res.data.message },
  }
)

const { formData: passwordForm, fieldErrors: passwordErrors, isSubmitting: isPasswordSubmitting, isSuccess: isPasswordSuccess, globalMessage: passwordMessage, submitForm: submitPassword } = useApiForm<{ current_password: string; new_password: string; confirm_password: string }, { message: string }>(
  { current_password: '', new_password: '', confirm_password: '' },
  {
    endpoint: '/api/auth/change-password',
    method: 'put',
    transform: (d) => ({
      current_password: d.current_password,
      new_password: d.new_password,
      new_password_confirmation: d.confirm_password,
    }),
    onSuccess: (res) => { passwordMessage.value = res.data.message },
  }
)

const { isSubmitting: isDeleteSubmitting, isSuccess: isDeleteSuccess, globalMessage: deleteMessage, submitForm: submitDelete } = useApiForm<{}, { message: string }>(
  {},
  {
    endpoint: '/api/profile/delete-request',
    method: 'post',
    onSuccess: (res) => { deleteMessage.value = res.data.message },
  }
)

async function fetchProfile() {
  try {
    const { data } = await api.get<{ data: UserDetail }>('/profile')
    user.value = data.data
    profileForm.first_name = data.data.first_name
    profileForm.last_name = data.data.last_name
  } catch (e: any) {
    loadError.value = e.message || 'Błąd ładowania profilu'
  }
}

onMounted(fetchProfile)
</script>

<template>
  <AppHead title="Ustawienia profilu" />
  <div class="w-full flex flex-col min-h-screen md:items-center justify-between">
    <div class="flex w-full mb-12">
      <Navbar>
        <h1 class="text-4xl font-bold">Ustawienia profilu</h1>
      </Navbar>
    </div>

    <div class="flex-grow w-full md:w-3/4 space-y-6 p-6 bg-white rounded-xl shadow-md">
      <div v-if="loadError" class="text-red-600">{{ loadError }}</div>
      <div v-else class="space-y-6">
        <div class="flex space-x-4 mb-6">
          <BaseButton :class="activeTab==='profile' ? 'bg-brand-light text-white px-6 py-3 rounded-md' : 'bg-gray-100 text-gray-800 px-6 py-3 rounded-md'" @click="activeTab='profile'">Profil</BaseButton>
          <BaseButton :class="activeTab==='password' ? 'bg-brand-light text-white px-6 py-3 rounded-md' : 'bg-gray-100 text-gray-800 px-6 py-3 rounded-md'" @click="activeTab='password'">Zmień hasło</BaseButton>
        </div>

        <form v-if="activeTab==='profile'" class="space-y-4" @submit.prevent="submitProfile">
          <BaseInput id="first_name" v-model="profileForm.first_name" name="first_name" label="Imię" :error="profileErrors.first_name" />
          <BaseInput id="last_name" v-model="profileForm.last_name" name="last_name" label="Nazwisko" :error="profileErrors.last_name" />
          <BaseInput id="email" name="email" label="E-mail" :model-value="user?.email || ''" disabled />
          <p v-if="isProfileSuccess" class="text-green-600">{{ profileMessage }}</p>
          <p v-else-if="profileMessage" class="text-red-600">{{ profileMessage }}</p>
          <BaseButton class="mt-4 w-full bg-brand-light text-white px-6 py-3 rounded-md" :disabled="isProfileSubmitting">{{ isProfileSubmitting ? 'Proszę czekać…' : 'Zapisz dane' }}</BaseButton>
        </form>

        <div v-if="activeTab==='profile'" class="mt-6 border-t pt-4">
          <p class="text-sm text-gray-600 mb-2">Chcesz usunąć konto? Otrzymasz maila z linkiem do potwierdzenia.</p>
          <form @submit.prevent="submitDelete">
            <BaseButton class="w-full bg-red-500 text-white px-6 py-3 rounded-md" :disabled="isDeleteSubmitting">{{ isDeleteSubmitting ? 'Proszę czekać…' : 'Poproś o usunięcie konta' }}</BaseButton>
          </form>
          <p v-if="isDeleteSuccess" class="text-green-600 mt-2">{{ deleteMessage }}</p>
          <p v-else-if="deleteMessage" class="text-red-600 mt-2">{{ deleteMessage }}</p>
        </div>

        <form v-else class="space-y-4" @submit.prevent="submitPassword">
          <PasswordInput id="current_password" v-model="passwordForm.current_password" name="current_password" label="Aktualne hasło" :error="passwordErrors.current_password" />
          <PasswordInput id="new_password" v-model="passwordForm.new_password" name="new_password" label="Nowe hasło" :error="passwordErrors.new_password" />
          <PasswordInput id="confirm_password" v-model="passwordForm.confirm_password" name="confirm_password" label="Potwierdź nowe hasło" :error="passwordErrors.confirm_password" />
          <p v-if="isPasswordSuccess" class="text-green-600">{{ passwordMessage }}</p>
          <p v-else-if="passwordMessage" class="text-red-600">{{ passwordMessage }}</p>
          <BaseButton class="mt-4 w-full bg-brand-light text-white px-6 py-3 rounded-md" :disabled="isPasswordSubmitting">{{ isPasswordSubmitting ? 'Proszę czekać…' : 'Zapisz nowe hasło' }}</BaseButton>
        </form>
      </div>
    </div>

    <Footer class="mt-16" />
  </div>
</template>
