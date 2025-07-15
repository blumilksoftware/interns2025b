import { ref } from 'vue'

export const useTogglePassword = () => {
  const showPasswords = ref(false)
  const togglePasswords = () => {
    showPasswords.value = !showPasswords.value
  }

  return {
    showPasswords,
    togglePasswords,
  }
}
