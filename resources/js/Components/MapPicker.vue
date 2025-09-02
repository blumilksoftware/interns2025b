<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch, defineEmits, defineProps } from 'vue'
import L, { type LatLngExpression, type Map as LeafletMap, type Marker } from 'leaflet'
import 'leaflet/dist/leaflet.css'
import iconUrl from 'leaflet/dist/images/marker-icon.png'
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png'
import shadowUrl from 'leaflet/dist/images/marker-shadow.png'

L.Marker.prototype.options.icon = L.icon({
  iconUrl,
  iconRetinaUrl,
  shadowUrl,
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
})

const props = defineProps<{
  modelValue?: { lat: number | null, lng: number | null } | null
  center?: [number, number]
  zoom?: number
  reverseGeocode?: boolean
  createOnClick?: boolean
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', val: { lat: number | null, lng: number | null } | null): void
  (e: 'update:address', val: string | null): void
}>()

const mapEl = ref<HTMLElement | null>(null)
let map: LeafletMap | null = null
let marker: Marker | null = null

const defaultCenter: LatLngExpression = props.center ?? [51.21, 16.16]
const defaultZoom = props.zoom ?? 14
const createOnClick = props.createOnClick ?? true

onMounted(() => {
  if (!mapEl.value) return

  map = L.map(mapEl.value, { center: defaultCenter, zoom: defaultZoom })
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map)

  if (props.modelValue?.lat != null && props.modelValue?.lng != null) {
    createMarker([props.modelValue.lat, props.modelValue.lng])
    map.setView([props.modelValue.lat, props.modelValue.lng], defaultZoom)
  }

  map.on('click', (e: L.LeafletMouseEvent) => {
    const { lat, lng } = e.latlng
    if (!marker && createOnClick) {
      createMarker([lat, lng])
    } else if (marker) {
      marker.setLatLng([lat, lng])
    }
    if (map) map.panTo([lat, lng])
    emits('update:modelValue', { lat, lng })
    if (props.reverseGeocode) doReverseGeocode(lat, lng)
  })
})

onBeforeUnmount(() => {
  if (marker) {
    try { marker.remove() } catch (e) {
      console.warn('Marker remove error', e)
    }
    marker = null
  }
  if (map) {
    try { map.remove() } catch (e) {
      console.warn('Map remove error', e)
    }
    map = null
  }
})

watch(() => props.modelValue, (nv) => {
  if (!map) return
  if (nv?.lat == null || nv.lng == null) {
    if (marker) {
      try { marker.remove() } catch (e) {
        console.warn('Marker remove error', e)
      }
      marker = null
    }
    return
  }
  if (!marker) {
    createMarker([nv.lat, nv.lng])
    map.setView([nv.lat, nv.lng], map.getZoom())
  } else {
    const cur = marker.getLatLng()
    if (cur.lat !== nv.lat || cur.lng !== nv.lng) {
      marker.setLatLng([nv.lat, nv.lng])
      map.setView([nv.lat, nv.lng], map.getZoom())
    }
  }
}, { deep: true })

function createMarker(latlng: LatLngExpression) {
  if (!map) return
  if (marker) {
    try { marker.remove() } catch (e) {
      console.warn('Marker remove error', e)
    }
    marker = null
  }
  marker = L.marker(latlng, { draggable: true }).addTo(map)
  marker.on('dragend', () => {
    const ll = marker?.getLatLng()
    if (ll) {
      emits('update:modelValue', { lat: ll.lat, lng: ll.lng })
      if (props.reverseGeocode) doReverseGeocode(ll.lat, ll.lng)
    }
  })
}

function locateMe() {
  if (!map) return
  if (!navigator.geolocation) {
    alert('Twoja przeglądarka nie wspiera Geolocation API')
    return
  }
  navigator.geolocation.getCurrentPosition((pos) => {
    const lat = pos.coords.latitude
    const lng = pos.coords.longitude
    if (!marker) createMarker([lat, lng])
    else marker.setLatLng([lat, lng])
    if (map) map.setView([lat, lng], 14)
    emits('update:modelValue', { lat, lng })
    if (props.reverseGeocode) doReverseGeocode(lat, lng)
  }, (err) => {
    console.error(err)
    alert('Nie udało się pobrać lokalizacji')
  }, { enableHighAccuracy: true, timeout: 10000 })
}

async function doReverseGeocode(lat: number, lng: number) {
  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } })
    if (!res.ok) {
      emits('update:address', null)
      return
    }
    const data = await res.json()
    const display = data?.display_name ?? null
    emits('update:address', display)
  } catch (e) {
    console.error('reverseGeocode error', e)
    emits('update:address', null)
  }
}
</script>

<template>
  <div class="relative w-full">
    <div ref="mapEl" class="w-full h-80 rounded-md overflow-hidden" />
    <div class="absolute top-3 right-3 flex gap-2">
      <button
        type="button"
        class="bg-white/90 px-3 py-2 rounded shadow text-sm"
        title="Ustaw moją lokalizację"
        @click="locateMe"
      >
        Moja lokalizacja
      </button>
    </div>
  </div>
</template>
