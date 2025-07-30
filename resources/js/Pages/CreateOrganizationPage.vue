<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import Footer from '@/Components/Footer.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { OrganizationForm } from '@/types/types'

const { formData: form, fieldErrors: errors, isSubmitting, submitForm } = useApiForm<OrganizationForm>(
  {
    name: '',
    owner_id: null,
    group_url: '',
    avatar_url: '',
  },
  {
    endpoint: '/api/admin/organizations',
    method: 'post',
    onSuccess: () => {
      router.visit('/')
    },
  },
)
</script>

<template>
  <AppHead title="Utwórz organizację" />
  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full mb-12">
      <Navbar>
        <h1 class="text-4xl font-bold">Dodaj organizację</h1>
      </Navbar>
    </div>

    <form class="w-full md:w-3/4 space-y-6 p-6 bg-white rounded-xl shadow-md" @submit.prevent="submitForm">
      <BaseInput id="name" v-model="form.name" name="name" label="Nazwa organizacji" :error="errors.name" />
      <BaseInput
        id="owner_id"
        v-model="(form.owner_id as any)"
        name="owner_id"
        label="ID właściciela (Opcjonalne)"
        type="number"
        :error="errors.owner_id"
      />
      <BaseInput id="group_url" v-model="form.group_url" name="group_url" label="Grupa URL (Opcjonalne)" :error="errors.group_url" />
      <BaseInput id="avatar_url" v-model="form.avatar_url" name="avatar_url" label="Avatar URL (Opcjonalne)" :error="errors.avatar_url" />

      <BaseButton class="bg-brand-light text-white px-6 py-3 rounded-md" :disabled="isSubmitting">
        Utwórz organizację
      </BaseButton>
    </form>
    <Footer class="mt-16" />
  </div>
</template>
