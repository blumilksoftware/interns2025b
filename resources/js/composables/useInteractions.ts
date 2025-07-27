import { ref } from 'vue'
import api from '@/services/api'

export function useInteractions() {
  const isFollowing = ref(false)

  async function followUser(userId: number) {
    try {
      await api.post(`/follow/user/${userId}`)
      isFollowing.value = true
    } catch (error) {
      console.error('Błąd podczas obserwowania użytkownika:', error)
    }
  }

  return { isFollowing, followUser }
}
