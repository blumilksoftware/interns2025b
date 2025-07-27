import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import api from '@/services/api'


export function useAuth() {
  const page = usePage()
  const props = page.props as unknown as AuthProps

  const authUser   = computed(() => props.auth.user)
  const authUserId = computed(() => props.auth.user?.id)

  const isLoggedIn = computed(() => !!authUser.value)

  async function logout() {
    try {
      await api.post('/auth/logout')
      sessionStorage.removeItem('token')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      router.visit('/login', { method: 'get' })
    }
  }

  return { authUser, authUserId, isLoggedIn, logout }
}
