import axios from 'axios'
import { createI18n } from 'vue-i18n'
import en from '@/Locales/en.json'
import pl from '@/Locales/pl.json'

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = sessionStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api

export const i18n = createI18n({
  globalInjection: true,
  locale: 'pl',
  fallbackLocale: 'en',
  messages: { en, pl },
})
