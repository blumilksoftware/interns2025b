<script setup lang="ts">
import { ref, onMounted } from 'vue'
import L from 'leaflet'
import api from '@/services/api'

interface EventMarker {
  id: number
  title: string
  latitude: number
  longitude: number
}

const CENTER: [number, number] = [51.21006, 16.1619]
const INITIAL_ZOOM = 14
const MIN_ZOOM = 1//13
const MAX_ZOOM = 17
//const BOUNDS: [[number, number], [number, number]] = [
//  [51.2597694104174, 16.03829270690674],
//  [51.153081195098444, 16.30093461210093],
//]
const MAX_BOUNDS_VISCOSITY = 1

const mapElement = ref<HTMLDivElement | null>(null)

onMounted(async () => {
  if (!mapElement.value) return

  const map = L.map(mapElement.value, {
    center: CENTER,
    zoom: INITIAL_ZOOM,
    minZoom: MIN_ZOOM,
    maxZoom: MAX_ZOOM,
    //  maxBounds: BOUNDS,
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

  L.marker(CENTER).addTo(map).bindPopup('You are here.')

  try {
    const res = await api.get<{ data: EventMarker[] }>('/events')
    const events = res.data.data

    events.forEach(evt => {
      if (typeof evt.latitude === 'number' && typeof evt.longitude === 'number') {
        const marker = L.marker([evt.latitude, evt.longitude]).addTo(map)
        marker.bindPopup(`
          <strong>${evt.title}</strong><br>
          <a href="/event/${evt.id}">Zobacz szczegóły</a>
        `)
      }
    })
  } catch (err) {
    console.error('Nie udało się pobrać wydarzeń:', err)
  }
})
</script>

<template>
  <div ref="mapElement" class="size-full rounded-[inherit]" />
</template>

<style>

div[ref="mapElement"] {
  min-height: 300px;
}
</style>
