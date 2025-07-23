import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

interface User {
  id: number
  name: string
  email: string
}

interface PageProps {
  auth: {
    user: User | null
  }
  [key: string]: any
}

export function useUser() {
  const page = usePage<PageProps>()
  const user = computed(() => page.props.auth.user)
  const isLoggedIn = computed(() => !!user.value)

  return {
    user,
    isLoggedIn,
  }
}
