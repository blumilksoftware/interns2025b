import { router } from '@inertiajs/vue3'
import api from '@/services/api'

export function useLogout() {
  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch (e) {
      console.error('Błąd przy wylogowaniu:', e)
    } finally {
      sessionStorage.removeItem('token')
      router.visit('/login', { method: 'get' })
    }
  }

  return { logout }
}
