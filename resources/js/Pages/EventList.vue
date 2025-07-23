<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import EventCard from '@/Components/EventCard.vue'
import { type RawEvent } from '@/types/events'
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import BaseInput from '@/Components/BaseInput.vue'

const events = ref<RawEvent[]>([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get<{ data: RawEvent[] }>('/events')
    events.value = res.data.data
  } catch (error) {
    console.error('Błąd pobierania wydarzeń:', error)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <AppHead title="Event" />
  <div class="flex flex-col">
    <navbar />
    <BaseInput id="search" type="search" placeholder="Szukaj wydarzenia" />
    <div class="p-6">
      <p v-if="loading" class="text-center">Ładowanie wydarzeń...</p>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <EventCard
          v-for="ev in events"
          :key="ev.id"
          :event="ev"
        />
        <p v-if="events.length === 0" class="col-span-full text-center text-gray-500">
          Brak dostępnych wydarzeń.
        </p>
      </div>
    </div>
  </div>
</template>

