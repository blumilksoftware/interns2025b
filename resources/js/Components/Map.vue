<script setup lang="ts">
import { ref, onMounted, defineProps } from 'vue'
import L from 'leaflet'
import api from '@/services/api'

interface EventMarker {
  id: number
  title: string
  latitude: number
  longitude: number
  image_url: string | null
  location: string | null
  is_paid: boolean
  age_category: string | null
}


const INITIAL_ZOOM = 14
const MIN_ZOOM     = 1
const MAX_ZOOM     = 17
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
          const m = L.marker([evt.latitude, evt.longitude]).addTo(map)
          m.bindPopup(`
            <div style="width:200px">
              <img src="${evt.image_url ?? '/images/placeholder.png'}"
                   alt="${evt.title}" style="width:100%; height: auto; border-radius:4px; margin-bottom:8px"/>
              <h3 style="margin:0; font-size:1rem;">${evt.title}</h3>
              <p style="margin:4px 0; font-size:0.875rem; color:#555;">
                ${evt.location ?? 'Brak lokalizacji'}
              </p>
              <p style="margin:4px 0; font-size:0.75rem;">
                ${evt.is_paid ? '<span style="color:#007bff">Płatny</span>' : '<span style="color:#28a745">Darmowy</span>'}
              </p>
              <p style="margin:4px 0; font-size:0.75rem;">
                Kategoria: ${evt.age_category ?? 'Brak'}
              </p>
              <a href="/event/${evt.id}"
                 style="display:inline-block; margin-top:8px; font-size:0.875rem; color:#fff;
                        background:#007bff; padding:4px 8px; border-radius:4px; text-decoration:none;">
                Szczegóły
              </a>
            </div>
          `)
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
