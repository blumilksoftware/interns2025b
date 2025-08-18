<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseSelect from '@/Components/BaseSelect.vue'
import BaseButton from '@/Components/BaseButton.vue'
import Footer from '@/Components/Footer.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { SelectOption, UserForm } from '@/types/types'

const roleOptions: SelectOption[] = [
  { label: 'User', value: 'user' },
  { label: 'Moderator', value: 'moderator' },
  { label: 'Administrator', value: 'administrator' },
  { label: 'Super Administrator', value: 'superAdministrator' },
]

const { formData: form, fieldErrors: errors, isSubmitting, submitForm } = useApiForm<UserForm>(
  {
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
  },
  {
    endpoint: '/api/admin/users',
    method: 'post',
    onSuccess: (response) => {
      router.visit(`/users/${response.data.id}/edit`)
    },
  },
)
</script>


<template>
  <AppHead title="Utwórz użytkownika" />
  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full mb-12">
      <Navbar>
        <h1 class="text-4xl font-bold">Dodaj użytkownika</h1>
      </Navbar>
    </div>

    <form
      class="w-full md:w-3/4 space-y-6 p-6 bg-white rounded-xl shadow-md"
      @submit.prevent="submitForm"
    >
      <BaseInput
        id="first_name"
        v-model="form.first_name"
        name="first_name"
        label="Imię"
        :error="errors.first_name"
      />
      <BaseInput
        id="last_name"
        v-model="form.last_name"
        name="last_name"
        label="Nazwisko"
        :error="errors.last_name"
      />
      <BaseInput
        id="email"
        v-model="form.email"
        name="email"
        label="Email"
        type="email"
        :error="errors.email"
      />
      <BaseInput
        id="password"
        v-model="form.password"
        name="password"
        label="Hasło"
        type="password"
        :error="errors.password"
      />
      <BaseInput
        id="password_confirmation"
        v-model="form.password_confirmation"
        name="password_confirmation"
        label="Potwierdź hasło"
        type="password"
        :error="errors.password_confirmation"
      />

      <BaseSelect
        id="role"
        v-model="form.role"
        name="role"
        label="Rola"
        :options="roleOptions"
        :error="errors.role"
      />

      <BaseButton
        class="bg-brand-light text-white px-6 py-3 rounded-md"
        :disabled="isSubmitting"
      >
        Utwórz użytkownika
      </BaseButton>
    </form>

    <Footer class="mt-16" />
  </div>
</template>
