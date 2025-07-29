import { ref, computed, type Ref } from 'vue'

export function useSearch<T extends Record<string, any>>(
  items: Ref<T[]>,
  availableFields: Array<keyof T>,
) {

  const query = ref('')

  const activeFields = ref<Array<keyof T>>([])

  const dateFilter = ref('')

  const filtered = computed(() => {
    const term = query.value.trim().toLowerCase()

    return items.value.filter(item => {
      const fieldsToCheck = activeFields.value.length > 0
        ? activeFields.value
        : availableFields

      const matchesText = term
        ? fieldsToCheck.some(field => {
          const val = (item as any)[field]
          return String(val ?? '')
            .toLowerCase()
            .includes(term)
        })
        : true

      const matchesDate = dateFilter.value
        ? String((item as any).start ?? '').startsWith(dateFilter.value)
        : true

      return matchesText && matchesDate
    })
  })

  return {
    query,
    activeFields,
    availableFields,
    dateFilter,
    filtered,
  }
}
