<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/services/api'
import { useApiForm } from '@/composables/useApiForm'
import type { UserDetail } from '@/types/types'
import BaseInput from '@/Components/BaseInput.vue'
import PasswordInput from '@/Components/PasswordInput.vue'
import BaseButton from '@/Components/BaseButton.vue'

const activeTab = ref<'profile' | 'password'>('profile')
const loadError = ref<string | null>(null)

interface ProfilePayload { first_name: string, last_name: string }
const profileForm = useApiForm<ProfilePayload, { message: string }>(
  { first_name: '', last_name: '' },
  {
    endpoint: 'api/profile',
    method: 'put',
    onSuccess: (res) => {
      profileForm.globalMessage.value = res.data.message
    },
  },
)

interface PasswordPayload {
  current_password:        string
  new_password:            string
  confirm_password:        string
}
const passwordForm = useApiForm<PasswordPayload, { message: string }>(
  { current_password: '', new_password: '', confirm_password: '' },
  {
    endpoint: 'api/auth/change-password',
    method: 'put',
    transform: (d) => ({
      current_password:          d.current_password,
      new_password:              d.new_password,
      new_password_confirmation: d.confirm_password,
    }),
    onSuccess: (res) => {
      passwordForm.globalMessage.value = res.data.message
    },
  },
)

const deleteForm = useApiForm<{}, { message: string }>(
  {}, {
    endpoint: 'api/profile/delete-request',
    method: 'post',
    onSuccess: (res) => {
      deleteForm.globalMessage.value = res.data.message
    },
  },
)

const user = ref<UserDetail | null>(null)
async function fetchProfile() {
  try {
    const { data } = await api.get<{ data: UserDetail }>('/profile')
    user.value = data.data
    Object.assign(profileForm.formData, {
      first_name: data.data.first_name,
      last_name:  data.data.last_name,
    })
  } catch (e: any) {
    loadError.value = e.message || 'Błąd ładowania profilu'
  }
}
onMounted(fetchProfile)
</script>

<template>
  <div class="max-w-md mx-auto p-6 bg-white rounded-2xl shadow space-y-6">
    <h1 class="text-3xl font-bold">Ustawienia profilu</h1>

    <div v-if="loadError" class="text-red-600">{{ loadError }}</div>
    <div v-else class="space-y-6">
      <div class="flex space-x-4 mb-6">
        <BaseButton
          :class="activeTab==='profile' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'"
          @click="activeTab='profile'"
        >
          Profil
        </BaseButton>
        <BaseButton
          :class="activeTab==='password' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'"
          @click="activeTab='password'"
        >
          Zmień hasło
        </BaseButton>
      </div>

      <div v-if="activeTab==='profile'" class="space-y-4">
        <BaseInput
          id="first_name" v-model="profileForm.formData.first_name"
          name="first_name"
          label="Imię"
          :error="profileForm.fieldErrors.first_name"
        />
        <BaseInput
          id="last_name" v-model="profileForm.formData.last_name"
          name="last_name"
          label="Nazwisko"
          :error="profileForm.fieldErrors.last_name"
        />
        <BaseInput
          id="email" name="email"
          label="E‑mail"
          :model-value="user?.email || ''"
          disabled
        />

        <p v-if="profileForm.isSuccess" class="text-green-600">{{ profileForm.globalMessage }}</p>
        <p v-else-if="profileForm.globalMessage" class="text-red-600">{{ profileForm.globalMessage }}</p>

        <BaseButton
          class="mt-4 w-full bg-blue-600 text-white"
          :disabled="profileForm.isSubmitting.value"
          @click="profileForm.submitForm"
        >
          {{ profileForm.isSubmitting.value ? 'Proszę czekać…' : 'Zapisz dane' }}
        </BaseButton>

        <div class="mt-6 border-t pt-4">
          <p class="text-sm text-gray-600 mb-2">
            Chcesz usunąć konto? Otrzymasz maila z linkiem do potwierdzenia.
          </p>
          <BaseButton
            class="w-full bg-red-600 text-white"
            :disabled="deleteForm.isSubmitting.value"
            @click="deleteForm.submitForm"
          >
            {{ deleteForm.isSubmitting.value ? 'Proszę czekać…' : 'Poproś o usunięcie konta' }}
          </BaseButton>
          <p v-if="deleteForm.isSuccess" class="text-green-600 mt-2">
            {{ deleteForm.globalMessage }}
          </p>
          <p v-else-if="deleteForm.globalMessage" class="text-red-600 mt-2">
            {{ deleteForm.globalMessage }}
          </p>
        </div>
      </div>

      <div v-else class="space-y-4">
        <PasswordInput
          id="current_password" v-model="passwordForm.formData.current_password"
          name="current_password"
          label="Aktualne hasło"
          :error="passwordForm.fieldErrors.current_password"
        />
        <PasswordInput
          id="new_password" v-model="passwordForm.formData.new_password"
          name="new_password"
          label="Nowe hasło"
          :error="passwordForm.fieldErrors.new_password"
        />
        <PasswordInput
          id="confirm_password" v-model="passwordForm.formData.confirm_password"
          name="confirm_password"
          label="Potwierdź nowe hasło"
          :error="passwordForm.fieldErrors.confirm_password"
        />

        <p v-if="passwordForm.isSuccess" class="text-green-600">{{ passwordForm.globalMessage }}</p>
        <p v-else-if="passwordForm.globalMessage" class="text-red-600">{{ passwordForm.globalMessage }}</p>

        <BaseButton
          class="mt-4 w-full bg-blue-600 text-white"
          :disabled="passwordForm.isSubmitting.value"
          @click="passwordForm.submitForm"
        >
          {{ passwordForm.isSubmitting.value ? 'Proszę czekać…' : 'Zapisz nowe hasło' }}
        </BaseButton>
      </div>
    </div>
  </div>
</template>
