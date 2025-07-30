<script setup lang="ts">

withDefaults(
  defineProps<{
    id: number | string
    imageUrl?: string | undefined
    start: string
    isPaid: boolean
    title: string
    location?: string | null
    ageCategory?: string | null
  }>(),
  {
    imageUrl: 'https://picsum.photos/200/300',
    location: 'Brak lokalizacji',
    ageCategory: 'Brak',
  },
)
</script>

<template>
  <InertiaLink
    :href="`/events/${id}`"
    class="block max-w-sm bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition delay-100 duration-300 hover:scale-105"
  >
    <img
      :src="imageUrl"
      alt="Event Banner"
      class="w-full h-48 object-cover"
    >

    <div class="p-4 space-y-2">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">{{ start }}</div>
        <span
          class="text-xs font-semibold px-2 py-1 rounded-full"
          :class="[isPaid ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600']"
        >
          {{ isPaid ? 'Płatny' : 'Darmowy' }}
        </span>
      </div>

      <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>

      <p class="text-sm text-gray-600 flex items-center">
        {{ location }}
      </p>

      <p class="text-sm text-gray-600">
        Ograniczenie wiekowe:
        <span class="font-medium">{{ ageCategory }}</span>
      </p>
    </div>
  </InertiaLink>
</template>
