import { createI18n } from 'vue-i18n'
import pl from '@/Locales/pl.json'
import en from '@/Locales/en.json'

function getStartingLocale() {
  const saved = sessionStorage.getItem('locale')
  if (saved) return saved

  const browserLang = navigator.language.split('-')[0]
  if (['pl', 'en'].includes(browserLang)) return browserLang

  return 'en'
}

export const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: getStartingLocale(),
  fallbackLocale: 'en',
  messages: {
    pl,
    en,
  },
})
