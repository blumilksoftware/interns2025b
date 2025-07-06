import api from '@/services/api'
import { router } from '@inertiajs/vue3'

export function useSessionAuth() {
  async function logout() {
    await api.post('/auth/logout').catch(() => {})
    sessionStorage.removeItem('token')
    router.visit('/login')
  }

  return { logout }
}
