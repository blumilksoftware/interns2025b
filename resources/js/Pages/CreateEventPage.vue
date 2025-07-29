<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseSelect from '@/Components/BaseSelect.vue'
import BaseButton from '@/Components/BaseButton.vue'
import Socials from '@/Components/Socials.vue'
import { useApiForm } from '@/composables/useApiForm'
import type { EventForm, SelectOption } from '@/types/types'
import { computed } from 'vue'

const page = usePage()
const statusOptions = computed<SelectOption[]>(() => {
  const options = page.props.statusOptions as SelectOption[] | undefined
  if (options && Array.isArray(options) && options.length > 0) {
    return options
  }
  return [
    { label: 'Draft', value: 'draft' },
    { label: 'Published', value: 'published' },
    { label: 'Ongoing', value: 'ongoing' },
  ]
})

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
    age_category: '',
  },
  {
    endpoint: '/api/events',
    method: 'post',
    onSuccess: () => {
      router.visit('/event')
    },
  },
)

const latitudeString = computed({
  get: () => (form.latitude !== null ? String(form.latitude) : ''),
  set: (value: string) => { form.latitude = value === '' ? null : Number(value) },
})

const longitudeString = computed({
  get: () => (form.longitude !== null ? String(form.longitude) : ''),
  set: (value: string) => { form.longitude = value === '' ? null : Number(value) },
})

const priceString = computed({
  get: () => (form.price !== null ? String(form.price) : ''),
  set: (value: string) => { form.price = value === '' ? null : Number(value) },
})
</script>

<template>
  <AppHead title="Utwórz wydarzenie" />
  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full mb-12">
      <Navbar>
        <h1 class="text-4xl font-bold">Dodaj wydarzenie</h1>
      </Navbar>
    </div>

    <form class="w-full md:w-3/4 space-y-6 p-6 bg-white rounded-xl shadow-md" @submit.prevent="submitForm">
      <BaseInput id="title" v-model="form.title" name="title" label="Tytuł wydarzenia" :error="errors.title" />
      <BaseInput id="description" v-model="form.description" name="description" label="Opis" :error="errors.description" textarea />
      <BaseInput id="start" v-model="form.start" name="start" label="Data rozpoczęcia" type="datetime-local" :error="errors.start" />
      <BaseInput id="end" v-model="form.end" name="end" label="Data zakończenia" type="datetime-local" :error="errors.end" />
      <BaseInput id="location" v-model="form.location" name="location" label="Lokalizacja" :error="errors.location" />
      <BaseInput id="address" v-model="form.address" name="address" label="Adres (opcjonalnie)" :error="errors.address" />
      <BaseInput id="latitude" v-model="latitudeString" name="latitude" label="Szerokość geograficzna" type="number" :error="errors.latitude" />
      <BaseInput id="longitude" v-model="longitudeString" name="longitude" label="Długość geograficzna" type="number" :error="errors.longitude" />
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
        Utwórz wydarzenie
      </BaseButton>
    </form>

    <div class="w-full bg-gradient-to-tr from-brand to-brand-light py-12 mt-16">
      <div class="text-center text-white space-y-6">
        <h2 class="text-3xl font-bold">Bądź na bieżąco z wydarzeniami</h2>
        <p>Dołącz do społeczności LetsEvent</p>
        <InertiaLink href="/register" class="bg-white text-black px-6 py-2 rounded-full font-semibold shadow-sm hover:scale-105 transition">
          Zarejestruj się
        </InertiaLink>
        <div class="flex flex-col items-center text-gray-200 mt-8 space-y-2">
          <div class="flex space-x-4">
            <InertiaLink href="#" class="hover:underline">Regulamin</InertiaLink>
            <span>•</span>
            <InertiaLink href="#" class="hover:underline">Polityka prywatności</InertiaLink>
          </div>
          <Socials />
          <p class="text-sm">&copy; 2025 Interns2025b</p>
        </div>
      </div>
    </div>
  </div>
</template>
