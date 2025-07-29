import { ref, watch } from 'vue'
import api from '@/services/api'
import type { RawEvent } from '@/types/events'

export function useEvents() {
  const events = ref<RawEvent[]>([])
  const search = ref<string>('')
  const page = ref<number>(1)
  const meta = ref<{ current_page: number, last_page: number }>({ current_page: 1, last_page: 1 })

  async function fetchEvents() {
    try {
      const res = await api.get<{
        data: RawEvent[]
        meta: { current_page: number, last_page: number }
      }>('/events', {
        params: {
          search: search.value,
          page: page.value,
        },
      })
      events.value = res.data.data
      meta.value.current_page = res.data.meta.current_page
      meta.value.last_page = res.data.meta.last_page
    } catch (error) {
      console.error('useEvents fetchEvents error:', error)
    }
  }

  watch([search, page], fetchEvents, { immediate: true })

  function prevPage() {
    if (page.value > 1) page.value--
  }
  function nextPage() {
    if (page.value < meta.value.last_page) page.value++
  }

  return {
    events,
    search,
    page,
    meta,
    prevPage,
    nextPage,
  }
}
