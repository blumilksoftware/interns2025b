import { ref, computed } from 'vue'
import api from '@/services/api'

export type FollowType = 'user' | 'organization' | 'event'

export interface Followings {
  users: any[]
  organizations: any[]
  events: any[]
}

export function useInteractions() {
  const followings = ref<Followings>({
    users: [],
    organizations: [],
    events: [],
  })

  const followKeyMap: Record<FollowType, keyof Followings> = {
    user: 'users',
    organization: 'organizations',
    event: 'events',
  }

  async function fetchFollowings() {
    try {
      const { data } = await api.get<Followings>('/followings')
      followings.value = data
    } catch (e: any) {
      console.error('Fetch followings error:', e)
    }
  }

  async function toggleFollow(type: FollowType, id: number) {
    try {
      const { data } = await api.post<{ message: string }>(`/follow/${type}/${id}`)
      const key = followKeyMap[type]
      const list = followings.value[key]
      const idx  = list.findIndex((i: any) => i.id === id)

      if (idx >= 0) list.splice(idx, 1)
      else          list.push({ id })

      return data.message
    } catch (e: any) {
      alert('Toggle follow error')
      throw e
    }
  }

  function useIsFollowing(type: FollowType, idRef: number | { value: number }) {
    return computed(() => {
      const id = typeof idRef === 'number' ? idRef : idRef.value
      const key = followKeyMap[type]
      return followings.value[key].some((i: any) => i.id === id)
    })
  }

  const isParticipating = ref<boolean>(false)

  async function participateEvent(eventId: number) {
    try {
      const { data } = await api.post<{ message: string }>(`/events/${eventId}/participate`)
      isParticipating.value = !isParticipating.value
      return data.message
    } catch (e: any) {
      alert('Participation error:')
      throw e
    }
  }

  function useIsParticipating() {
    return computed(() => isParticipating.value)
  }

  return {
    followings,
    fetchFollowings,
    toggleFollow,
    useIsFollowing,
    isParticipating,
    participateEvent,
    useIsParticipating,
  }
}
