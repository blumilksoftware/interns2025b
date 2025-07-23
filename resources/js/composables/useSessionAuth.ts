import { computed, ref } from 'vue'
import api from '@/services/api'
import { router } from '@inertiajs/vue3'
import type { User } from '@/types/types'

const user = ref<User | null>(null)

export function useUser() {
  const isAuthenticated = computed(() => user.value !== null)

  async function fetchUser(redirect = true) {
    try {
      const { data } = await api.get<User>('/user')
      user.value = data
    } catch {
      sessionStorage.removeItem('token')
      user.value = null
      if (redirect) router.visit('/login')
    }
  }

  async function login(email: string, password: string) {
    const { data } = await api.post<{ user: User, token: string }>('/auth/login', { email, password })
    sessionStorage.setItem('token', data.token)
    return await fetchUser(false)
  }

  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch (e) {//a
    }
    sessionStorage.removeItem('token')
    user.value = null
    router.visit('/login')
  }

  return { user, isAuthenticated, fetchUser, login, logout }
}
