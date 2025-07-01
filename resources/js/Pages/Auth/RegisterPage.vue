<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { RegisterForm } from '@/types/types'
import {Link as InertiaLink, router} from '@inertiajs/vue3'
import LoginFacebook from '@/Components/LoginFacebook.vue'


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
          notification: 'Registration successful! Please check your email for verification.',
        },
      })
    },
  },
)
</script>

<template>
  <AuthLayout>
    <template #header>
      <h2 class="font-bold text-3xl">Rejestracja</h2>
      <p class="font-semibold">
        Posiadasz konto?
        <InertiaLink href="/login" class="underline font-bold">Zaloguj się</InertiaLink>
      </p>
    </template>

    <template #form>
      <form
        class="flex flex-col items-center justify-center w-full mt-6 space-y-6 text-xl"
        @submit.prevent="submitForm"
      >
        <div class="w-5/6">
          <BaseInput
            id="first_name"
            v-model="form.first_name"
            name="first_name"
            label="First Name"
            type="text"
          />
          <small v-if="errors.first_name" class="text-red-600">
            {{ errors.first_name }}
          </small>

          <BaseInput
            id="last_name"
            v-model="form.last_name"
            name="last_name"
            label="Last Name"
            type="text"
          />
          <small v-if="errors.last_name" class="text-red-600">
            {{ errors.last_name }}
          </small>

          <BaseInput
            id="email"
            v-model="form.email"
            name="email"
            label="Email"
            type="email"
          />
          <small v-if="errors.email" class="text-red-600">
            {{ errors.email }}
          </small>

          <BaseInput
            id="password"
            v-model="form.password"
            name="password"
            label="Hasło"
            type="password"
          />
          <small v-if="errors.password" class="text-red-600">
            {{ errors.password }}
          </small>

          <BaseInput
            id="password_confirmation"
            v-model="form.password_confirmation"
            name="password_confirmation"
            label="Powtórz Hasło"
            type="password"
          />
          <small v-if="errors.password_confirmation" class="text-red-600">
            {{ errors.password_confirmation }}
          </small>
          <div class="flex items-center justify-end">
            <InertiaLink href="/forgotpassword" class="font-bold text-base text-[#025F60]">Zapomniałeś hasła?</InertiaLink>
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
    </template>
  </AuthLayout>
</template>

