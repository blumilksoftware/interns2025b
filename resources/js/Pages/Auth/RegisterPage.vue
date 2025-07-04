<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { RegisterForm } from '@/types/types'
import { Link as InertiaLink, router } from '@inertiajs/vue3'
import LoginFacebook from '@/Components/LoginFacebook.vue'
import PasswordInput from '@/Components/PasswordInput.vue'

const {
  formData: form,
  fieldErrors: errors,
  isSubmitting,
  submitForm,
  reset,
} = useApiForm<RegisterForm>(
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
          notification:
            'Registration successful! Please check your email for verification.',
        },
      })
    },
  },
)
</script>

<template>
  <AuthLayout>
    <template #header>
      <h2 class="font-bold text-3xl">Zarejestruj się</h2>
      <p class="font-medium">
        Posiadasz konto?
        <InertiaLink
          href="/login"
          class="underline font-semibold hover:text-gray-200"
        >
          Zaloguj się
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
            label="E-mail"
            type="email"
          />
          <small v-if="errors.email" class="text-red-600">
            {{ errors.email }}
          </small>
          <div class="flex flex-col sm:grid sm:grid-cols-2 sm:gap-5">
            <div>
              <BaseInput
                id="first_name"
                v-model="form.first_name"
                name="first_name"
                label="Imię"
                type="text"
              />
              <small v-if="errors.first_name" class="text-red-600">
                {{ errors.first_name }}
              </small>
            </div>

            <div>
              <BaseInput
                id="last_name"
                v-model="form.last_name"
                name="last_name"
                label="Nazwisko (opcjonalnie)"
                type="text"
              />
              <small v-if="errors.last_name" class="text-red-600">
                {{ errors.last_name }}
              </small>
            </div>

            <div>
              <PasswordInput
                id="password"
                v-model="form.password"
                name="password"
                label="Hasło"
              />
              <small v-if="errors.password" class="text-red-600">
                {{ errors.password }}
              </small>
            </div>

            <div>
              <PasswordInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                name="password_confirmation"
                label="Powtórz Hasło"
              />
              <small v-if="errors.password_confirmation" class="text-red-600">
                {{ errors.password_confirmation }}
              </small>
            </div>
          </div>
        </div>

        <BaseButton
          class="w-5/6 h-12 bg-black shadow-[#375DFB] text-white font-bold"
          :disabled="isSubmitting"
          type="submit"
        >
          Zarejestruj się
        </BaseButton>
      </form>
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
              class="text-[#025F60] hover:text-[#024c4d] font-semibold"
            >
              Warunki świadczenia usług
            </InertiaLink>
            oraz
            <InertiaLink
              href="#"
              class="text-[#025F60] hover:text-[#024c4d] font-semibold"
            >
              Umowę o przetwarzaniu danych
            </InertiaLink>
          </p>
        </div>
      </div>
    </template>
  </AuthLayout>
</template>
