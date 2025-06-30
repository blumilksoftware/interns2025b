<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { LoginForm, LoginResponse } from '@/types/types'

defineOptions({
  layout: AuthLayout,
})

interface Props {
  notification?: string
}

const props = defineProps<Props>()

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
  <AppHead title="Logowanie" description="Strona Logowania" />
  <form class="flex flex-col items-center justify-center w-full mt-6 space-y-6 text-xl"
        @submit.prevent="submitForm"
  >
    <div class="w-full">
      <div
        v-if="notification"
        class="w-5/6 mx-auto p-4 mb-4 text-center text-green-700 bg-green-100 rounded-lg"
      >
        {{ notification }}
      </div>
    </div>

    <div class="w-5/6">
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
    </div>

    <div class="flex items-center justify-between w-5/6">
      <label class="flex items-center">
        <input
          id="remember_password"
          v-model="form.remember"
          name="remember_password"
          type="checkbox"
          class="mr-2"
        >
        <span class="text-base text-gray-700">Zapamiętaj mnie</span>
      </label>
      <a href="/forgotpassword" class="font-bold text-base text-[#025F60]">Zapomniałeś hasła?</a>
    </div>

    <BaseButton
      class="w-5/6 h-12 bg-black shadow-[#375DFB] text-white font-bold"
      :disabled="isSubmitting"
      type="submit"
    >
      Zaloguj się
    </BaseButton>
  </form>
</template>
