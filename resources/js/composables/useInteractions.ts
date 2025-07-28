import { ref, computed, type Ref } from 'vue'
import api from '@/services/api'

export type FollowType = 'user' | 'organization'

export interface Followings {
  users: any[]
  organizations: any[]
}

export function useInteractions() {
  const followings = ref<Followings>({ users: [], organizations: [] })
  const followers  = ref<any[]>([])

  async function fetchFollowings(userId?: number) {
    const url = userId ? `/followings?user=${userId}` : '/followings'
    const { data } = await api.get<Followings>(url)
    followings.value = data
    return data
  }

  async function fetchFollowers(userId?: number) {
    const url = userId ? `/followers?user=${userId}` : '/followers'
    const { data } = await api.get<{ followers: any[] }>(url)
    followers.value = data.followers
    return data.followers
  }

  async function toggleFollow(type: FollowType, id: number) {
    const { data } = await api.post<{ message: string }>(`/follow/${type}/${id}`)
    await fetchFollowings()
    return data.message
  }

  function useIsFollowing(type: FollowType, idRef: Ref<number|undefined>) {
    return computed(() => {
      const key = `${type}s` as keyof Followings
      return !!(idRef.value != null &&
        followings.value[key]?.some(item => item.id === idRef.value))
    })
  }

  return {
    followings,
    followers,
    fetchFollowings,
    fetchFollowers,
    toggleFollow,
    useIsFollowing,
  }
}
