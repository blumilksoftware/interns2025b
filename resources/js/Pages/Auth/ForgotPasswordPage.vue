<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { ForgotPasswordForm } from '@/types/types'
import AppHead from '@/Components/AppHead.vue'

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
      globalMessage.value =
        'Link do resetowania hasła został wysłany na podany adres e-mail'
    },
    onError: () => {
      globalMessage.value =
        'Wystąpił błąd podczas wysyłania linku resetującego'
    },
  },
)
</script>

<template>
  <app-head title="Przypominj hasło" />
  <auth-layout>
    <template #header>
      <h2 class="font-bold text-3xl">Przypomnij hasło</h2>
      <p class="font-medium mt-3">
        Pamiętasz hasło?
        <InertiaLink
          href="/login"
          class="underline font-semibold hover:text-gray-200"
        >
          Zaloguj się
        </InertiaLink><br>
        Chcesz założyć konto?
        <InertiaLink
          href="/register"
          class="underline font-semibold hover:text-gray-200"
        >
          Zarejestruj się
        </InertiaLink>
      </p>
    </template>

    <template #form>
      <div class="space-y-6">
        <form
          class="flex flex-col items-center justify-center w-full space-y-4 text-xl"
          @submit.prevent="submitForm"
        >
          <div class="w-5/6 space-y-2 mt-6">
            <div class="flex-col items-center space-y-4 text-xl">
              <h2 class="text-2xl text-gray-800 font-bold">Reset hasła</h2>
              <p class="text-gray-600">
                Wprowadź swój adres e-mail, a wyślemy Ci link do zresetowania hasła.
              </p>
              <div
                v-if="globalMessage"
                class="text-center rounded-md p-4 mx-auto"
                :class="{
                  'text-red-600': globalMessage.includes('błąd'),
                  'bg-green-100 text-green-600':
                    globalMessage.includes('został wysłany'),
                }"
              >
                {{ globalMessage }}
              </div>
            </div>
            <BaseInput
              id="forgot-password-email"
              v-model="form.email"
              name="email"
              label="E-mail"
              type="email"
              :error="errors.email"
            />
          </div>


          <BaseButton
            class="w-5/6 h-12 bg-black shadow-BtnShadowBlue text-white font-bold"
            :disabled="isSubmitting"
            type="submit"
          >
            Zaloguj się
          </BaseButton>
        </form>
      </div>
      <div class=" mb-6" />
    </template>
  </auth-layout>
</template>
