export interface EventMarker {
  id: number
  title: string
  latitude: number
  longitude: number
  image_url: string | null
  location: string | null
  is_paid: boolean
  age_category: string | null
}

export function eventMapPopup(evt: EventMarker): string {
  return `
    <div class="bg-white rounded-lg shadow-md overflow-hidden w-64">
      <img
        src="${evt.image_url ?? '/images/placeholder.png'}"
        alt="${evt.title}"
        class="w-full h-32 object-cover"
      />
      <div class="p-4 space-y-2">
        <h3 class="text-lg font-semibold">${evt.title}</h3>
        <p class="text-sm text-gray-500">${evt.location ?? 'Brak lokalizacji'}</p>
        <p class="text-sm">
          ${evt.is_paid
    ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Płatny</span>'
    : '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">Darmowy</span>'}
        </p>
        <p class="text-xs text-gray-400">Kategoria: ${evt.age_category ?? 'Brak'}</p>
        <a
          href="/event/${evt.id}"
          class="block text-center mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded"
        >
          Zobacz szczegóły
        </a>
      </div>
    </div>
  `
}
