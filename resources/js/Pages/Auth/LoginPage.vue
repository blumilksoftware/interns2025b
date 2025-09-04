<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { Link as InertiaLink, router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { LoginForm, LoginResponse } from '@/types/types'
import LoginFacebook from '@/Components/LoginFacebook.vue'
import PasswordInput from '@/Components/PasswordInput.vue'
import AppHead from '@/Components/AppHead.vue'

const { t, locale } = useI18n()

function switchLanguage(lang: string) {
  locale.value = lang
}

const { notification } = defineProps<{ notification?: string }>()

const { formData: form, fieldErrors: errors, isSubmitting, submitForm } = useApiForm<LoginForm, LoginResponse>(
  {
    email: '',
    password: '',
    remember: false,
  },
  {
    endpoint: '/api/auth/login',
    onSuccess: (response) => {
      sessionStorage.setItem('token', response.data.token)
      router.visit('/', { method: 'get', preserveState: false, preserveScroll: false })
    },
    onError: (error) => {
      if (error.response?.status === 403) {
        errors.email = t('auth.invalidCredentials')
      }
    },
  },
)
</script>

<template>
  <app-head :title="t('auth.login')" />
  <div class="fixed top-0 right-0 z-50 flex gap-3 p-4">
    <button @click="switchLanguage('pl')" class="underline">PL</button>
    <button @click="switchLanguage('en')" class="underline">EN</button>
  </div>
  <AuthLayout>
    <template #header>
      <h2 class="font-bold text-3xl">{{ t('auth.login') }}</h2>
      <p class="font-medium mt-3">
        {{ t('auth.noAccount') }}
        <InertiaLink href="/register" class="underline font-semibold hover:text-gray-200">
          {{ t('auth.register') }}
        </InertiaLink>
      </p>
    </template>

    <template #form>
      <div class="space-y-6">
        <form class="flex flex-col items-center justify-center w-full space-y-4 text-xl" @submit.prevent="submitForm">
          <div class="w-full">
            <div v-if="notification" class="w-5/6 mx-auto p-4 mt-6 text-center text-green-700 bg-green-100 rounded-lg">
              {{ notification }}
            </div>
          </div>

          <div class="w-5/6 space-y-2">
            <BaseInput id="email" v-model="form.email" name="email" :label="t('auth.email')" type="email" :error="errors.email" />
            <PasswordInput id="password" v-model="form.password" name="password" :label="t('auth.password')" :error="errors.password" />
          </div>

          <div class="flex items-center justify-between w-5/6">
            <label class="flex items-center">
              <input id="remember_password" v-model="form.remember" name="remember_password" type="checkbox"
                     class="mr-2 size-4 accent-brand-light bg-gray-100 rounded-sm border-gray-300" />
              <span class="text-base text-gray-700">{{ t('auth.rememberMe') }}</span>
            </label>
            <inertia-link href="/forgot-password" class="font-bold text-base text-brand-light hover:text-brand-dark">
              {{ t('auth.forgotPassword') }}
            </inertia-link>
          </div>

          <BaseButton class="w-5/6 h-12 bg-black shadow-shadow-blue text-white font-bold" :disabled="isSubmitting" type="submit">
            {{ t('auth.login') }}
          </BaseButton>
        </form>
      </div>
    </template>

    <template #footer>
      <div class="flex items-center w-5/6 mt-8 mb-4">
        <div class="grow h-px bg-gray-200" />
        <span class="px-4 text-gray-500 text-sm">{{ t('auth.or') }}</span>
        <div class="grow h-px bg-gray-200" />
      </div>
      <login-facebook />
      <div class="w-5/6">
        <div class="text-center mt-6">
          <p class="text-base text-gray-500">
            {{ t('auth.registerTerms') }}
          </p>
        </div>
      </div>
    </template>
  </AuthLayout>
</template>
