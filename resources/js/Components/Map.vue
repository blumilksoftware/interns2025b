<script setup lang="ts">
import { onMounted, watch, ref, toRef } from 'vue'
import L, { type LatLngExpression, type Map as LeafletMap } from 'leaflet'
import { createApp, h } from 'vue'
import EventPopUp from '@/Components/EventPopUp.vue'
import type { RawEvent } from '@/types/events'

const props = defineProps<{
  events: RawEvent[]
  center?: [number, number]
  zoom?: number
  minZoom?: number
  maxZoom?: number
  maxBounds?: [[number, number], [number, number]]
  maxBoundsViscosity?: number
}>()


const mapEl = ref<HTMLElement|null>(null)
let map: LeafletMap

onMounted(() => {
  if (!mapEl.value) return

  map = L.map(mapEl.value, {
    center: (props.center as LatLngExpression) ?? [51.21,16.16],
    zoom:   props.zoom   ?? 14,
    minZoom: props.minZoom ?? 3,
    maxZoom: props.maxZoom ?? 18,
    maxBounds: props.maxBounds,
    maxBoundsViscosity: props.maxBoundsViscosity ?? 0.5,
  })

  L.tileLayer(
    'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
    { attribution: '&copy; OpenStreetMap contributors' },
  ).addTo(map)

  props.events.forEach(addMarker)

  watch(() => props.events, (newList) => {
    map.eachLayer(layer => {
      if ((layer as any).getLatLng) map.removeLayer(layer)
    })
    newList.forEach(addMarker)
  })

  const centerRef = toRef(props, 'center')
  watch(centerRef, (newCenter) => {
    if (newCenter && map) {
      map.setView(newCenter as LatLngExpression, props.zoom)
    }
  })
})

function addMarker(evt: RawEvent) {
  if (evt.latitude != null && evt.longitude != null) {
    const m = L.marker([evt.latitude, evt.longitude]).addTo(map)
    const c = document.createElement('div')
    createApp({ render: () => h(EventPopUp, { event: evt }) }).mount(c)
    m.bindPopup(c, {
      minWidth:  250,
      maxWidth:  250,
      className: 'leaflet-popup--custom',
    })
  }
}
</script>

<template>
  <div ref="mapEl" class="size-full rounded-[inherit]" />
</template>
