<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/Footer.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseSelect from '@/Components/BaseSelect.vue'
import BaseButton from '@/Components/BaseButton.vue'
import MapPicker from '@/Components/MapPicker.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { EventForm, SelectOption } from '@/types/types'

const page = usePage()
const event = page.props.event as EventForm

const statusOptions = computed<SelectOption[]>(() => {
  const options = page.props.statusOptions as SelectOption[] | undefined
  return options?.length ? options : [
    { label: 'Draft', value: 'draft' },
    { label: 'Published', value: 'published' },
    { label: 'Cancelled', value: 'cancelled' },
  ]
})

const { formData: form, fieldErrors: errors, isSubmitting, submitForm } = useApiForm<EventForm>(
  { ...event },
  {
    endpoint: `/api/events/${event.id}`,
    method: 'put',
    onSuccess: () => router.visit('/event'),
  },
)

const priceString = computed({
  get: () => (form.price !== null ? String(form.price) : ''),
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
  <AppHead title="Edytuj wydarzenie" />
  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full mb-12">
      <Navbar>
        <h1 class="text-4xl font-bold">Edytuj wydarzenie</h1>
      </Navbar>
    </div>

    <form class="w-full md:w-3/4 space-y-6 p-6 bg-white rounded-xl shadow-md" @submit.prevent="submitForm">
      <BaseInput id="title" v-model="form.title" name="title" label="Tytuł wydarzenia" :error="errors.title" />
      <BaseInput id="description" v-model="form.description" name="description" label="Opis" type="textarea" :error="errors.description" />
      <BaseInput id="start" v-model="form.start" name="start" label="Data rozpoczęcia" type="datetime-local" :error="errors.start" />
      <BaseInput id="end" v-model="form.end" name="end" label="Data zakończenia" type="datetime-local" :error="errors.end" />
      <BaseInput id="location" v-model="form.location" name="location" label="Lokalizacja" :error="errors.location" />
      <BaseInput id="address" v-model="form.address" name="address" label="Adres" :error="errors.address" />

      <div>
        <label class="block text-sm font-medium mb-2">Wybierz lokalizację na mapie</label>
        <MapPicker
          v-model="coords"
          :center="[51.21,16.16]"
          :zoom="14"
          :reverse-geocode="true"
          :create-on-click="true"
          @update:address="onMapAddressUpdate"
        />
        <div v-if="form.address" class="mt-3 text-sm text-gray-700">
          <strong>Wybrany adres:</strong>
          <div class="mt-1 break-words">{{ form.address }}</div>
        </div>
        <div v-else class="mt-3 text-sm text-gray-500">Kliknij na mapie, aby ustawić lokalizację.</div>
      </div>

      <BaseInput id="image_url" v-model="form.image_url" name="image_url" label="URL zdjęcia" :error="errors.image_url" />
      <BaseInput id="age_category" v-model="form.age_category" name="age_category" label="Kategoria wiekowa" :error="errors.age_category" />

      <div class="flex space-x-4 items-center">
        <label class="flex items-center space-x-2">
          <input v-model="form.is_paid" type="checkbox">
          <span>Wydarzenie płatne?</span>
        </label>
        <BaseInput
          v-if="form.is_paid"
          id="price"
          v-model="priceString"
          name="price"
          label="Cena"
          type="number"
          min="0"
          :error="errors.price"
        />
      </div>

      <BaseSelect
        id="status"
        v-model="form.status"
        name="status"
        label="Status wydarzenia"
        :options="statusOptions"
        :error="errors.status"
      />

      <BaseButton class="bg-brand-light text-white px-6 py-3 rounded-md" :disabled="isSubmitting">
        <span v-if="isSubmitting">Zapisuję…</span>
        <span v-else>Zapisz zmiany</span>
      </BaseButton>
    </form>

    <Footer class="mt-16" />
  </div>
</template>
