<script setup lang="ts">
import {router, useForm} from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import axios from 'axios'

defineOptions({
  layout: AuthLayout,
})

interface Props {
  notification?: string
}

const props = defineProps<Props>()

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

async function submit() {
  try {
    const response = await axios.post('/api/auth/login', form.data())
    if (response.data.token) {
      localStorage.setItem('token', response.data.token)
      router.visit('/')
    }
  } catch (error: any) {
    if (error.response?.status === 403) {
      form.setError('email', 'Invalid credentials')
    } else if (error.response?.data?.errors) {
      form.setError(error.response.data.errors)
    }
  }
}
</script>

<template>
  <form class="flex flex-col items-center justify-center w-full mt-6 space-y-6 text-xl"
        @submit.prevent="submit"
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
      class="w-5/6"
      :disabled="form.processing"
      type="submit"
    >
      Zaloguj się
    </BaseButton>
  </form>
</template>
