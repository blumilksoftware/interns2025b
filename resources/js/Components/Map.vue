<script setup lang="ts">
import api from '@/services/api'
import { ref, onMounted, defineProps } from 'vue'
import L from 'leaflet'
import { eventMapPopup, type EventMarker } from '@/utilities/eventMapPopup'

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
      res.data.data.forEach(evt => {
        if (evt.latitude != null && evt.longitude != null) {
          const marker = L.marker([evt.latitude, evt.longitude]).addTo(map)
          marker.bindPopup(eventMapPopup(evt), {
            maxWidth: 300,
            className: 'leaflet-popup--custom',
          })
        }
      })
    }
  } catch (err) {
    console.error('Nie udało się pobrać wydarzeń:', err)
  }
})
</script>

<template>
  <div ref="mapElement" class="size-full rounded-[inherit]" />
</template>
