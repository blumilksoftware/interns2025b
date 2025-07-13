<script setup lang="ts">
import { Link as InertiaLink, router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { LoginForm, LoginResponse } from '@/types/types'
import LoginFacebook from '@/Components/LoginFacebook.vue'
import PasswordInput from '@/Components/PasswordInput.vue'
import AppHead from '@/Components/AppHead.vue'

const { notification } = defineProps<{
  notification?: string
}>()

const {
  formData: form,
  fieldErrors: errors,
  isSubmitting,
  submitForm,
} = useApiForm<LoginForm, LoginResponse>(
  {
    email: '',
    password: '',
    remember: false,
  },
  {
    endpoint: '/api/auth/login',
    onSuccess: (response) => {
      localStorage.setItem('token', response.data.token)
      router.visit('/', {
        method: 'get',
        preserveState: false,
        preserveScroll: false,
      })
    },
    onError: (error) => {
      if (error.response?.status === 403) {
        errors.email = 'Invalid credentials'
      }
    },
  },
)
</script>

<template>
  <app-head title="Logowanie" />
  <auth-layout>
    <template #header>
      <h2 class="font-bold text-3xl">Zaloguj się</h2>
      <p class="font-medium mt-3">
        Nie posiadasz konta?
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
          <div class="w-full">
            <div
              v-if="notification"
              class="w-5/6 mx-auto p-4 mt-6 text-center text-green-700 bg-green-100 rounded-lg"
            >
              {{ notification }}
            </div>
          </div>

          <div class="w-5/6 space-y-2">
            <BaseInput
              id="email"
              v-model="form.email"
              name="email"
              label="E-mail"
              type="email"
              :error="errors.email"
            />
            <PasswordInput
              id="password"
              v-model="form.password"
              name="password"
              label="Hasło"
              :error="errors.password"
            />
          </div>

          <div class="flex items-center justify-between w-5/6">
            <label class="flex items-center">
              <input
                id="remember_password"
                v-model="form.remember"
                name="remember_password"
                type="checkbox"
                class="mr-2 size-4 accent-lightBrand bg-gray-100 rounded-sm border-gray-300"
              >
              <span class="text-base text-gray-700">Zapamiętaj mnie</span>
            </label>
            <inertia-link
              href="/forgot-password"
              class="font-bold text-base text-lightBrand hover:text-darkBrand"
            >
              Nie pamiętasz hasła?
            </inertia-link>
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
    </template>
    <template #footer>
      <div class="flex items-center w-5/6 mt-8 mb-4">
        <div class="grow h-px bg-gray-200" />
        <span class="px-4 text-gray-500 text-sm">lub</span>
        <div class="grow h-px bg-gray-200" />
      </div>
      <login-facebook />
      <div class="w-5/6">
        <div class="text-center">
          <p class="text-base text-gray-500 mt-6">
            Rejestrując się wyrażasz zgodę na
            <InertiaLink
              href="#"
              class="text-lightBrand hover:text-darkBrand font-semibold"
            >
              Warunki świadczenia usług
            </InertiaLink>
            oraz
            <InertiaLink
              href="#"
              class="text-lightBrand hover:text-darkBrand font-semibold"
            >
              Umowę o przetwarzaniu danych
            </InertiaLink>
          </p>
        </div>
      </div>
    </template>
  </auth-layout>
</template>
