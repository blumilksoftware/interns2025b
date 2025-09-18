<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseSelect from '@/Components/BaseSelect.vue'
import BaseButton from '@/Components/BaseButton.vue'
import Footer from '@/Components/Footer.vue'
import MapPicker from '@/Components/MapPicker.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { EventForm, SelectOption } from '@/types/types'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const page = usePage()

const allowedStatuses = ['draft', 'published'] as const
const statusOptions = computed<SelectOption[]>(() => {
  const options = page.props.statusOptions as SelectOption[] | undefined
  if (options && Array.isArray(options) && options.length > 0) {
    const filtered = options
      .filter(o => allowedStatuses.includes(o.value as typeof allowedStatuses[number]))
      .map(o => ({ label: t(`status.${o.value}`), value: o.value }))
    if (filtered.length > 0) return filtered
  }
  return [
    { label: t('status.draft'), value: 'draft' },
    { label: t('status.published'), value: 'published' },
  ]
})

const ageCategoryOptions = computed<SelectOption[]>(() => [
  { label: t('event.ageKids'), value: 'kids' },
  { label: t('event.ageTeens'), value: 'teens' },
  { label: t('event.ageAdults'), value: 'adults' },
  { label: t('event.ageEveryone'), value: 'everyone' },
])

const { formData: form, fieldErrors: errors, isSubmitting, submitForm } = useApiForm<EventForm>(
  {
    title: '',
    description: '',
    start: '',
    end: '',
    location: '',
    address: '',
    latitude: null,
    longitude: null,
    is_paid: false,
    price: null,
    status: 'draft',
    image_url: '',
    age_category: 'everyone',
  },
  {
    endpoint: '/api/events',
    method: 'post',
    onSuccess: () => {
      router.visit('/event')
    },
  },
)

const priceString = computed({
  get: () => (form.price !== null && form.price !== undefined ? String(form.price) : ''),
  set: (value: string) => { form.price = value === '' ? null : Number(value) },
})

const coords = computed({
  get: () => ({ lat: form.latitude ?? null, lng: form.longitude ?? null }),
  set: (val: { lat: number | null, lng: number | null }) => {
    form.latitude = val.lat ?? null
    form.longitude = val.lng ?? null
  },
})

function onMapAddressUpdate(address: string | null) {
  if (address !== null && address !== undefined) {
    form.address = address
  }
}
</script>

<template>
  <AppHead :title="t('event.createTitle')" />
  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full mb-12">
      <Navbar>
        <h1 class="text-4xl font-bold">{{ t('event.addEvent') }}</h1>
      </Navbar>
    </div>

    <form class="w-full md:w-3/4 space-y-6 p-6 bg-white rounded-xl shadow-md" @submit.prevent="submitForm">
      <BaseInput id="title" v-model="form.title" name="title" :label="t('event.title')" :error="errors.title" />
      <BaseInput id="description" v-model="form.description" name="description" :label="t('event.description')" type="textarea" :error="errors.description" />
      <BaseInput id="start" v-model="form.start" name="start" :label="t('event.startDate')" type="datetime-local" :error="errors.start" />
      <BaseInput id="end" v-model="form.end" name="end" :label="t('event.endDate')" type="datetime-local" :error="errors.end" />
      <BaseInput id="location" v-model="form.location" name="location" :label="t('event.location')" :error="errors.location" />
      <BaseInput id="address" v-model="form.address" name="address" :label="t('event.address')" :error="errors.address" />

      <div>
        <label class="block text-sm font-medium mb-2">{{ t('event.clickToSet') }}</label>
        <MapPicker
          v-model="coords"
          :center="[51.21,16.16]"
          :zoom="14"
          :reverse-geocode="true"
          :create-on-click="true"
          @update:address="onMapAddressUpdate"
        />
        <div v-if="form.address" class="mt-3 text-sm text-gray-700">
          <strong>{{ t('event.address') }}:</strong>
          <div class="mt-1 break-words">{{ form.address }}</div>
        </div>
        <div v-else class="mt-3 text-sm text-gray-500">{{ t('event.clickToSet') }}</div>
      </div>
      <BaseInput id="image_url" v-model="form.image_url" name="image_url" :label="t('event.imageUrl')" :error="errors.image_url" />

      <BaseSelect
        id="age_category"
        v-model="form.age_category"
        name="age_category"
        :label="t('event.ageCategory')"
        :options="ageCategoryOptions"
        :error="errors.age_category"
      />

      <div class="flex space-x-4 items-center">
        <label class="flex items-center space-x-2">
          <input v-model="form.is_paid" type="checkbox">
          <span>{{ t('event.isPaid') }}</span>
        </label>
        <BaseInput
          v-if="form.is_paid"
          id="price"
          v-model="priceString"
          name="price"
          :label="t('event.price')"
          type="number"
          step="0.01"
          min="0"
          :error="errors.price"
        />
      </div>

      <BaseSelect
        id="status"
        v-model="form.status"
        name="status"
        :label="t('event.status')"
        :options="statusOptions"
        :error="errors.status"
      />

      <BaseButton class="bg-brand-light text-white px-6 py-3 rounded-md" :disabled="isSubmitting">
        {{ t('event.createButton') }}
      </BaseButton>
    </form>
    <Footer class="mt-16" />
  </div>
</template>
