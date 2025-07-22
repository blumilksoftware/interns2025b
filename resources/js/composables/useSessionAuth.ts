import { ref } from 'vue'
import api from '@/services/api'
import { router } from '@inertiajs/vue3'
import type { User } from '@/types/types'

const user = ref<User | null>(null)
const isAuthenticated = ref(false)

export function useSessionAuth(redirectIfNotAuth = true) {
  async function fetchUser() {
    try {
      const response = await api.get('/user')
      user.value = response.data
      isAuthenticated.value = true
    } catch {
      user.value = null
      isAuthenticated.value = false
      if (redirectIfNotAuth) router.visit('/login')
    }
  }

  async function login(email: string, password: string) {
    try {
      const response = await api.post('/auth/login', { email, password })

      const token = response.data.token
      localStorage.setItem('token', token)

      isAuthenticated.value = true
      await fetchUser()
      router.visit('/profile')
    } catch (error) {
      console.error('Login failed', error)
      throw error
    }
  }

  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch {}

    localStorage.removeItem('token')
    user.value = null
    isAuthenticated.value = false

    router.visit('/login')
  }

  return {
    user,
    isAuthenticated,
    fetchUser,
    login,
    logout,
  }
}
