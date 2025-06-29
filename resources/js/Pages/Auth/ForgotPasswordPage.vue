<script setup lang="ts">
import { ref } from 'vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

defineOptions({
  layout: AuthLayout,
})

interface FormErrors {
  email: string
  general: string
}

const errors = ref<FormErrors>({
  email: '',
  general: '',
})

const email = ref('')
const isProcessing = ref(false)

async function handleSubmit() {
  errors.value = { email: '', general: '' }
  isProcessing.value = true

  try {
    await axios.post('/api/auth/forgot-password', {
      email: email.value,
    })

    errors.value.general = 'Link do resetowania hasła został wysłany na podany adres email'
  } catch (error: any) {
    if (error.response?.status === 422) {
      const validationErrors = error.response.data.errors
      if (validationErrors) {
        errors.value.email = validationErrors.email?.[0] || ''
      }
    } else {
      errors.value.general = 'Wystąpił błąd podczas wysyłania linku resetującego'
    }
  } finally {
    isProcessing.value = false
  }
}

function goToLogin() {
  router.visit('/login')
}
</script>

<template>
  <div class="w-full max-w-md mx-auto">
    <div v-if="errors.general" class="mb-4 text-center" :class="{
      'text-red-600': errors.general.includes('błąd'),
      'text-green-600': errors.general.includes('został wysłany')
    }"
    >
      {{ errors.general }}
    </div>

    <form
      class="flex flex-col items-center justify-center w-full mt-6 space-y-6"
      @submit.prevent="handleSubmit"
    >
      <div class="w-full">
        <h2 class="text-2xl font-bold mb-4">Reset hasła</h2>
        <p class="text-gray-600 mb-4">
          Wprowadź swój adres email, a wyślemy Ci link do zresetowania hasła.
        </p>

        <BaseInput
          id="forgot-password-email"
          v-model="email"
          name="email"
          label="Email"
          type="email"
        />
        <small v-if="errors.email" class="text-red-600">
          {{ errors.email }}
        </small>
      </div>

      <div class="w-full flex gap-4">
        <BaseButton
          type="button"
          class="w-1/2"
          @click="goToLogin"
        >
          Powrót do logowania
        </BaseButton>

        <BaseButton
          type="submit"
          class="w-1/2"
          :disabled="isProcessing"
        >
          Wyślij link
        </BaseButton>
      </div>
    </form>
  </div>
</template>
