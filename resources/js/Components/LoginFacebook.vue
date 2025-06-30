<script setup lang="ts">
import axios from 'axios'
import BaseButton from './BaseButton.vue'

async function handleFacebookLogin() {
  try {
    const response = await axios.get('/api/auth/facebook/redirect')
    if (response.data.url) {
      localStorage.setItem('loginRedirectUrl', window.location.href)
      window.location.href = response.data.url
    }
  } catch (error: any) {
    const errorMessage = error.response?.data?.message || 'An error occurred during Facebook login'
    console.error('Facebook login error:', error.response?.data || error)
    alert(errorMessage)
  }
}
</script>

<template>
  <BaseButton
    type="button"
    class="w-5/6 border border-black font-bold flex items-center justify-center gap-2"
    @click="handleFacebookLogin"
  >
    <span>Zaloguj za pomocą Facebook</span>
  </BaseButton>
</template>
