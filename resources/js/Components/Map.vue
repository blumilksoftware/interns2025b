<script setup lang="ts">
import { ref, onMounted } from 'vue'
import L from 'leaflet'

const CENTER: [number, number] = [51.21006, 16.1619]
const INITIAL_ZOOM = 14
const MIN_ZOOM = 13
const MAX_ZOOM = 17
const BOUNDS: [[number, number], [number, number]] = [
  [51.2597694104174, 16.03829270690674],
  [51.153081195098444, 16.30093461210093],
]
const MAX_BOUNDS_VISCOSITY = 1

const mapElement = ref<HTMLDivElement | null>(null)

onMounted(() => {
  if (!mapElement.value) return

  const map = L.map(mapElement.value, {
    center: CENTER,
    zoom: INITIAL_ZOOM,
    minZoom: MIN_ZOOM,
    maxZoom: MAX_ZOOM,
    maxBounds: BOUNDS,
    maxBoundsViscosity: MAX_BOUNDS_VISCOSITY,
  })

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map)

  L.marker(CENTER)
    .addTo(map)
    .bindPopup('You are here.')
})
</script>


<template>
  <div ref="mapElement" class="size-full rounded-[inherit]" />
</template>
