import { ref, watch } from 'vue'
import api from '@/services/api'
import type { RawEvent } from '@/types/events'

interface Meta {
  current_page: number
  last_page:    number
}

interface UseEventsOptions {
  all?: boolean
}

export function useEvents(options?: UseEventsOptions) {
  const events = ref<RawEvent[]>([])
  const search = ref<string>('')
  const page   = ref<number>(1)
  const meta   = ref<Meta>({ current_page: 1, last_page: 1 })

  async function fetchEventsPage() {
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
    let nextPage = 1
    let lastPage = 1

    do {
      const res = await api.get<{
        data: RawEvent[]
        meta: Meta
      }>('/events', {
        params: { search: search.value, page: nextPage },
      })
      allData = allData.concat(res.data.data)
      nextPage = res.data.meta.current_page + 1
      lastPage = res.data.meta.last_page
    } while (nextPage <= lastPage)

    events.value = allData
    meta.value = { current_page: 1, last_page: 1 }
  }

  if (options?.all) {
    fetchAll().catch(err => {
      console.error('useEvents fetchAll error:', err)
    })
  } else {
    watch(
      [search, page],
      async () => {
        try {
          const { data, meta: m } = await fetchEventsPage()
          events.value = data
          meta.value   = m
        } catch (err) {
          console.error('useEvents fetchEventsPage error:', err)
        }
      },
      { immediate: true },
    )
  }

  function prevPage() {
    if (page.value > 1) page.value--
  }
  function nextPage() {
    if (page.value < meta.value.last_page) page.value++
  }

  return { events, search, page, meta, prevPage, nextPage, fetchAll }
}
