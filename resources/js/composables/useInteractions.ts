import { ref } from 'vue'
import api from '@/services/api'

export function useInteractions() {
  const isFollowing = ref(false)

  async function followUser(userId: number) {
    try {
      await api.post(`/follow/user/${userId}`)
      isFollowing.value = true
    } catch (error) {
      alert('Błąd podczas obserwowania użytkownika')
    }
  }

  return { isFollowing, followUser }
}
