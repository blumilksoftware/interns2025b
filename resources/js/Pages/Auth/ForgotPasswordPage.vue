<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { ForgotPasswordForm } from '@/types/types'

defineOptions({
  layout: AuthLayout,
})

const {
  formData: form,
  fieldErrors: errors,
  isSubmitting,
  submitForm,
  globalMessage,
} = useApiForm<ForgotPasswordForm>(
  {
    email: '',
  },
  {
    endpoint: '/api/auth/forgot-password',
    onSuccess: () => {
      globalMessage.value = 'Link do resetowania hasła został wysłany na podany adres email'
    },
    onError: () => {
      globalMessage.value = 'Wystąpił błąd podczas wysyłania linku resetującego'
    },
  },
)
</script>

<template>
  <div class="w-full max-w-md mx-auto">
    <div
      v-if="globalMessage"
      class="mb-4 text-center"
      :class="{
        'text-red-600': globalMessage.includes('błąd'),
        'text-green-600': globalMessage.includes('został wysłany')
      }"
    >
      {{ globalMessage }}
    </div>

    <form
      class="flex flex-col items-center justify-center w-full mt-6 space-y-6"
      @submit.prevent="submitForm"
    >
      <div class="w-full">
        <h2 class="text-2xl font-bold mb-4">Reset hasła</h2>
        <p class="text-gray-600 mb-4">
          Wprowadź swój adres email, a wyślemy Ci link do zresetowania hasła.
        </p>

        <BaseInput
          id="forgot-password-email"
          v-model="form.email"
          name="email"
          label="Email"
          type="email"
        />
        <small v-if="errors.email" class="text-red-600">
          {{ errors.email }}
        </small>
      </div>

      <div class="w-full flex gap-4">
        <InertiaLink
          href="/login"
          class="w-1/2"
        >
          <BaseButton
            type="button"
            class="w-full"
          >
            Powrót do logowania
          </BaseButton>
        </InertiaLink>

        <BaseButton
          type="submit"
          class="w-1/2"
          :disabled="isSubmitting"
        >
          Wyślij link
        </BaseButton>
      </div>
    </form>
  </div>
</template>
