<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { ForgotPasswordForm } from '@/types/types'
import AppHead from '@/Components/AppHead.vue'

const { t, locale } = useI18n()

function switchLanguage(lang: string) {
  locale.value = lang
}

const { formData: form, fieldErrors: errors, isSubmitting, submitForm, globalMessage } = useApiForm<ForgotPasswordForm>(
  {
    email: '',
  },
  {
    endpoint: '/api/auth/forgot-password',
    onSuccess: () => {
      globalMessage.value = t('auth.resetLinkSent')
    },
    onError: () => {
      globalMessage.value = t('auth.resetLinkError')
    },
  },
)
</script>

<template>
  <app-head :title="t('auth.forgotPassword')" />
  <div class="fixed top-0 right-0 z-50 flex gap-3 p-4">
    <button class="underline" @click="switchLanguage('pl')">PL</button>
    <button class="underline" @click="switchLanguage('en')">EN</button>
  </div>
  <AuthLayout>
    <template #header>
      <h2 class="font-bold text-3xl">{{ t('auth.forgotPassword') }}</h2>
      <p class="font-medium mt-3">
        {{ t('auth.rememberedPassword') }}
        <InertiaLink href="/login" class="underline font-semibold hover:text-gray-200">
          {{ t('auth.login') }}
        </InertiaLink><br>
        {{ t('auth.wantAccount') }}
        <InertiaLink href="/register" class="underline font-semibold hover:text-gray-200">
          {{ t('auth.register') }}
        </InertiaLink>
      </p>
    </template>

    <template #form>
      <div class="space-y-6 mb-6">
        <form class="flex flex-col items-center justify-center w-full space-y-4 text-xl" @submit.prevent="submitForm">
          <div class="w-5/6 space-y-2 mt-6">
            <div class="flex-col items-center space-y-4 text-xl">
              <h2 class="text-2xl text-gray-800 font-bold">{{ t('auth.resetPassword') }}</h2>
              <p class="text-gray-600">{{ t('auth.resetPasswordInstructions') }}</p>
              <div
                v-if="globalMessage"
                class="text-center rounded-md p-4 mx-auto"
                :class="{
                  'text-red-600': globalMessage.includes('błąd') || globalMessage.includes('error'),
                  'bg-green-100 text-green-600': globalMessage.includes('został wysłany') || globalMessage.includes('sent')
                }"
              >
                {{ globalMessage }}
              </div>
            </div>
            <BaseInput
              id="email"
              v-model="form.email"
              name="email"
              :label="t('auth.email')"
              type="email"
              :error="errors.email"
            />
          </div>

          <BaseButton
            class="w-5/6 h-12 bg-black shadow-shadow-blue text-white font-bold"
            :disabled="isSubmitting"
            type="submit"
          >
            {{ t('auth.send') }}
          </BaseButton>
        </form>
      </div>
    </template>
  </AuthLayout>
</template>
