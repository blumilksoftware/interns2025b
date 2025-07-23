export function formatDate(
  dateString: string | null | undefined,
  locale = 'pl-PL',
  options: Intl.DateTimeFormatOptions = {
    year:  'numeric',
    month: 'short',
    day:   'numeric',
  },
): string {
  if (!dateString) return 'Brak daty'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'Brak daty'
  return new Intl.DateTimeFormat(locale, options).format(date)
}

export function formatTime(
  dateString: string | null | undefined,
  locale = 'pl-PL',
  options: Intl.DateTimeFormatOptions = {
    hour:   '2-digit',
    minute: '2-digit',
  },
): string {
  if (!dateString) return 'Brak godziny'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'Brak godziny'
  return new Intl.DateTimeFormat(locale, options).format(date)
}

