import { ref } from 'vue'

export const showPasswords = ref(false)
export function togglePasswords() {
  showPasswords.value = !showPasswords.value
}
