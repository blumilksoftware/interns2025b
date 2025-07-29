<script setup lang="ts">
import api from '@/services/api'
import { ref, onMounted, createApp, h } from 'vue'
import L from 'leaflet'
import EventPopUp from '@/Components/EventPopUp.vue'
import type { EventMarker } from '@/types/events'

const INITIAL_ZOOM = 14
const MIN_ZOOM = 1
const MAX_ZOOM = 17
const MAX_BOUNDS_VISCOSITY = 1

const props = defineProps<{
  center?: [number, number]
  zoom?: number
  disableFetch?: boolean
}>()

const DEFAULT_CENTER: [number, number] = [51.21006, 16.1619]
const mapElement = ref<HTMLDivElement | null>(null)

onMounted(async () => {
  if (!mapElement.value) return

  const mapCenter = props.center ?? DEFAULT_CENTER
  const mapZoom   = props.zoom   ?? INITIAL_ZOOM

  const map = L.map(mapElement.value, {
    center: mapCenter,
    zoom: mapZoom,
    minZoom: MIN_ZOOM,
    maxZoom: MAX_ZOOM,
    maxBoundsViscosity: MAX_BOUNDS_VISCOSITY,
  })

  L.tileLayer(
    'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
    {
      attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/">CARTO</a>',
      subdomains: 'abcd',
      maxZoom: 19,
    },
  ).addTo(map)

  L.marker(mapCenter).addTo(map).bindPopup('You are here.')

  try {
    if (!props.disableFetch) {
      const res = await api.get<{ data: EventMarker[] }>('/events')
      res.data.data.forEach(event => {
        if (event.latitude != null && event.longitude != null) {
          const marker = L.marker([event.latitude, event.longitude]).addTo(map)
          const container = document.createElement('div')
          createApp({ render: () => h(EventPopUp, { event }) }).mount(container)
          marker.bindPopup(container, {
            minWidth: 250,
            maxWidth: 250,
            autoPanPadding: [0, 0],
            className: 'leaflet-popup--custom',
          })
        }
      })
    }
  } catch (error) {
    alert('Nie udało się pobrać wydarzeń:')
  }
})
</script>

<template>
  <div ref="mapElement" class="size-full rounded-[inherit]" />
</template>
