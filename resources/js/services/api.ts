import axios from 'axios'

export function mapToAcceptLanguage(code?: string | null) {
  if (!code) return navigator.language || 'en-US'
  if (code.startsWith('pl')) return 'pl-PL'
  if (code.startsWith('en')) return 'en-US'
  return code
}

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Accept-Language': mapToAcceptLanguage(sessionStorage.getItem('locale')),
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true,
})

export function setAcceptLanguageHeader(code?: string | null) {
  const lang = mapToAcceptLanguage(code)
  api.defaults.headers.common['Accept-Language'] = lang
  if (typeof window !== 'undefined' && (window as any).axios) {
    (window as any).axios.defaults.headers.common['Accept-Language'] = lang
  }
  axios.defaults.headers.common['Accept-Language'] = lang
}

api.interceptors.request.use((config) => {
  const token = sessionStorage.getItem('token')
  if (token) {
    config.headers = config.headers || {}
    config.headers.Authorization = `Bearer ${token}`
  }
  const stored = sessionStorage.getItem('locale')
  const langHeader = mapToAcceptLanguage(stored)
  config.headers = config.headers || {}
  config.headers['Accept-Language'] = langHeader

  console.debug('[api] sending Accept-Language:', config.headers['Accept-Language'])

  config.withCredentials = true
  return config
}, async (error) => await Promise.reject(error))

export default api
