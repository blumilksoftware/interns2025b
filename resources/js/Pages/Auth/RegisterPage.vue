<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { RegisterForm } from '@/types/types'
import { Link as InertiaLink, router } from '@inertiajs/vue3'
import LoginFacebook from '@/Components/LoginFacebook.vue'
import PasswordInput from '@/Components/PasswordInput.vue'
import AppHead from '@/Components/AppHead.vue'

const { t, locale } = useI18n()

function switchLanguage(lang: string) {
  locale.value = lang
}

const { formData: form, fieldErrors: errors, isSubmitting, submitForm, reset } = useApiForm<RegisterForm>(
  {
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
  },
  {
    endpoint: '/api/auth/register',
    onSuccess: () => {
      reset()
      router.visit('/login', {
        method: 'get',
        preserveState: false,
        preserveScroll: false,
        data: {
          notification: t('auth.registerSuccess'),
        },
      })
    },
  },
)
</script>

<template>
  <app-head :title="t('auth.register')" />
  <div class="fixed top-0 right-0 z-50 flex gap-3 p-4">
    <button @click="switchLanguage('pl')" class="underline">PL</button>
    <button @click="switchLanguage('en')" class="underline">EN</button>
  </div>
  <AuthLayout>
    <template #header>
      <h2 class="font-bold text-3xl">{{ t('auth.register') }}</h2>
      <p class="font-medium mt-3">
        {{ t('auth.haveAccount') }}
        <InertiaLink href="/login" class="underline font-semibold hover:text-gray-200">
          {{ t('auth.login') }}
        </InertiaLink>
      </p>
    </template>

    <template #form>
      <form
        class="flex flex-col items-center justify-center w-full mt-6 space-y-6 text-xl"
        @submit.prevent="submitForm"
      >
        <div class="w-5/6 space-y-2">
          <BaseInput
            id="email"
            v-model="form.email"
            name="email"
            :label="t('auth.email')"
            type="email"
            focus-placeholder="example@example.com"
            :error="errors.email"
          />
          <div class="flex flex-col sm:grid sm:grid-cols-2 sm:gap-5">
            <div>
              <BaseInput
                id="first_name"
                v-model="form.first_name"
                name="first_name"
                :label="t('auth.firstName')"
                type="text"
                :error="errors.first_name"
              />
            </div>

            <div>
              <BaseInput
                id="last_name"
                v-model="form.last_name"
                name="last_name"
                :label="t('auth.lastNameOptional')"
                type="text"
                :error="errors.last_name"
              />
            </div>

            <div>
              <PasswordInput
                id="password"
                v-model="form.password"
                name="password"
                :label="t('auth.password')"
                :error="errors.password"
              />
            </div>

            <div>
              <PasswordInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                name="password_confirmation"
                :label="t('auth.passwordConfirmation')"
                :error="errors.password_confirmation"
              />
            </div>
          </div>
        </div>

        <BaseButton
          class="w-5/6 h-12 bg-black shadow-shadow-blue text-white font-bold"
          :disabled="isSubmitting"
          type="submit"
        >
          {{ t('auth.register') }}
        </BaseButton>
      </form>
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
