<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import axios from 'axios'

defineOptions({
  layout: AuthLayout,
})

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

async function submit() {
  try {
    await axios.post('/api/auth/register', form.data())
    form.reset()
    window.location.href = '/login?notification=Registration successful! Please check your email for verification.'
  } catch (error: any) {
    if (error.response?.data?.errors) {
      form.setError(error.response.data.errors)
    }
  }
}
</script>


<template>
  <AppHead title="Rejestracja" description="Strona rejestracji" />
  <form class="flex flex-col items-center justify-center w-full mt-6 space-y-6 text-xl"
        @submit.prevent="submit"
  >
    <div class="w-5/6">
      <BaseInput
        id="first_name"
        v-model="form.first_name"
        name="first_name"
        label="First Name"
        type="text"
      />
      <small v-if="form.errors.first_name" class="text-red-600">
        {{ form.errors.first_name }}
      </small>

      <BaseInput
        id="last_name"
        v-model="form.last_name"
        name="last_name"
        label="Last Name"
        type="text"
      />
      <small v-if="form.errors.last_name" class="text-red-600">
        {{ form.errors.last_name }}
      </small>

      <BaseInput
        id="email"
        v-model="form.email"
        name="email"
        label="Email"
        type="email"
      />
      <small v-if="form.errors.email" class="text-red-600">
        {{ form.errors.email }}
      </small>

      <BaseInput
        id="password"
        v-model="form.password"
        name="password"
        label="Hasło"
        type="password"
      />
      <small v-if="form.errors.password" class="text-red-600">
        {{ form.errors.password }}
      </small>

      <BaseInput
        id="password_confirmation"
        v-model="form.password_confirmation"
        name="password_confirmation"
        label="Powtórz Hasło"
        type="password"
      />
      <small v-if="form.errors.password_confirmation" class="text-red-600">
        {{ form.errors.password_confirmation }}
      </small>
    </div>

    <BaseButton
      class="w-5/6 h-12 bg-black shadow-[#375DFB] text-white font-bold"
      :disabled="form.processing"
      type="submit"
    >
      Zarejestruj się
    </BaseButton>
  </form>
</template>
