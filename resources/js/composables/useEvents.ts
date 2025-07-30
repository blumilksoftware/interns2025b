import { ref, watch, computed } from 'vue'
import api from '@/services/api'
import type { RawEvent } from '@/types/events'

interface Meta {
  current_page: number
  last_page:    number
}

export interface UseEventsOptions {
  all?: boolean
  activeOnly?: boolean
}

export function useEvents(options?: UseEventsOptions) {
  const events = ref<RawEvent[]>([])
  const search = ref('')
  const page  = ref(1)
  const meta  = ref<Meta>({ current_page: 1, last_page: 1 })

  async function fetchPage() {
    const res = await api.get<{
      data: RawEvent[]
      meta: Meta
    }>('/events', {
      params: { search: search.value, page: page.value },
    })
    return res.data
  }

  async function fetchAll() {
    let allData: RawEvent[] = []
    let next = 1
    let last = 1

    do {
      const res = await api.get<{
        data: RawEvent[]
        meta: Meta
      }>('/events', {
        params: { search: search.value, page: next },
      })
      allData = allData.concat(res.data.data)
      next = res.data.meta.current_page + 1
      last = res.data.meta.last_page
    } while (next <= last)

    events.value = allData
    meta.value   = { current_page: 1, last_page: 1 }
  }

  if (options?.all) {
    fetchAll().catch(console.error)
  } else {
    watch(
      [search, page],
      async () => {
        try {
          const { data, meta: m } = await fetchPage()
          events.value = data
          meta.value   = m
        } catch (err) {
          console.error(err)
        }
      },
      { immediate: true },
    )
  }

  const activeEvents = computed(() => {
    if (!options?.activeOnly) {
      return events.value
    }
    return events.value.filter(e =>
      e.status === 'published' || e.status === 'ongoing',
    )
  })

  function prevPage() { if (page.value > 1) page.value-- }
  function nextPage() { if (page.value < meta.value.last_page) page.value++ }

  return {
    events,
    activeEvents,
    search,
    page,
    meta,
    prevPage,
    nextPage,
    fetchAll,
  }
}
