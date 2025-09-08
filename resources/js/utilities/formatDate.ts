function getCurrentLocale(): string {
  const saved = sessionStorage.getItem('locale')
  if (saved) return saved === 'en' ? 'en-US' : 'pl-PL'

  const browserLang = navigator.language
  if (browserLang.startsWith('en')) return 'en-US'
  if (browserLang.startsWith('pl')) return 'pl-PL'

  return 'en-US'
}

function formatFullDateTime(
  dateString: string | null | undefined,
  locale?: string,
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

  const lang = locale ?? getCurrentLocale()
  return new Intl.DateTimeFormat(lang, options).format(date)
}

export function formatDay(dateString: string | null | undefined, locale?: string): string {
  return formatFullDateTime(dateString, locale, { weekday: 'long' })
}

export function formatDate(dateString: string | null | undefined, locale?: string): string {
  return formatFullDateTime(dateString, locale, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

export function formatTime(dateString: string | null | undefined, locale?: string): string {
  return formatFullDateTime(dateString, locale, {
    hour: '2-digit',
    minute: '2-digit',
  })
}
