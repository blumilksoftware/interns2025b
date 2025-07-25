export function formatFullDateTime(
  dateString: string | null | undefined,
  locale = 'pl-PL',
  options: Intl.DateTimeFormatOptions = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  },
): string {
  if (!dateString) return 'Brak daty'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'Brak daty'
  return new Intl.DateTimeFormat(locale, options).format(date)
}

export function formatDay(dateString: string | null | undefined): string {
  return formatFullDateTime(dateString, 'pl-PL', { weekday: 'long' })
}

export function formatDate(dateString: string | null | undefined): string {
  return formatFullDateTime(dateString, 'pl-PL', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

export function formatTime(dateString: string | null | undefined): string {
  return formatFullDateTime(dateString, 'pl-PL', {
    hour: '2-digit',
    minute: '2-digit',
  })
}
