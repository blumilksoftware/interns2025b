<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/Footer.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { OrganizationForm } from '@/types/types'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

axios.defaults.withCredentials = true

const { t } = useI18n()

const page = usePage()
const organization = page.props.organization as OrganizationForm & { id: number }

const endpoint = `/api/admin/organizations/${organization.id}`

const { formData: form, fieldErrors: errors, isSubmitting, submitForm } = useApiForm<OrganizationForm>(
  { ...organization },
  {
    endpoint,
    method: 'put',
    onSuccess: () => {},
    onError: (err: unknown) => {
      console.error('Organization update error:', err)
    },
  },
)
</script>

<template>
  <AppHead :title="t('organization.editTitle')" />
  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full mb-12">
      <Navbar>
        <h1 class="text-4xl font-bold">{{ t('organization.editHeader') }}</h1>
      </Navbar>
    </div>

    <form class="w-full md:w-3/4 space-y-6 p-6 bg-white rounded-xl shadow-md" @submit.prevent="submitForm">
      <BaseInput id="name" v-model="form.name" name="name" :label="t('organization.fields.name')" :error="errors.name" />
      <BaseInput
        id="owner_id"
        v-model="(form.owner_id as any)"
        name="owner_id"
        :label="t('organization.fields.ownerId')"
        type="number"
        :error="errors.owner_id"
      />
      <BaseInput id="group_url" v-model="form.group_url" name="group_url" :label="t('organization.fields.groupUrl')" :error="errors.group_url" />
      <BaseInput id="avatar_url" v-model="form.avatar_url" name="avatar_url" :label="t('organization.fields.avatarUrl')" :error="errors.avatar_url" />

      <BaseButton class="bg-brand-light text-white px-6 py-3 rounded-md" :disabled="isSubmitting">
        {{ t('organization.saveChanges') }}
      </BaseButton>
    </form>

    <Footer class="mt-16" />
  </div>
</template>
