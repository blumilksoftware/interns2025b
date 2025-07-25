import { router } from '@inertiajs/vue3'

export interface UseLogout {
  logout: () => void
}

export function useLogout(): UseLogout {
  function logout(): void {
    router.post('/logout')
  }

  return { logout }
}
