<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { ForgotPasswordForm } from '@/types/types'


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
  <auth-layout>
    <template #header>
      <h2 class="font-bold text-3xl">Przypomnij Hasło</h2>
      <p class="font-semibold">
        Pamiętasz hasło?
        <InertiaLink href="/login" class="underline font-bold">Zaloguj się</InertiaLink><br>
        Chcesz założyć konto?
        <InertiaLink href="/register" class="underline font-bold">Zarejestruj się</InertiaLink>
      </p>
    </template>
    <template #form>
      <div class="flex flex-col items-center justify-center mt-6 space-y-6 text-xl">
        <div
          v-if="globalMessage"
          class="mb-4 text-center max-w-md mx-auto"
          :class="{
            'text-red-600': globalMessage.includes('błąd'),
            'text-green-600': globalMessage.includes('został wysłany')
          }"
        >
          {{ globalMessage }}
        </div>

        <form
          class="flex w-5/6 flex-col items-center justify-center max-w-md mx-auto mt-6 space-y-6"
          @submit.prevent="submitForm"
        >
          <div class="w-full text-center">
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

          <div class="w-full flex justify-center">
            <BaseButton
              type="submit"
              class="w-full h-12 bg-black shadow-[#375DFB] text-white font-bold"
              :disabled="isSubmitting"
            >
              Wyślij link
            </BaseButton>
          </div>
        </form>
      </div>
    </template>
  </auth-layout>
</template>
