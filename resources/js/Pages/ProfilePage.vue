<script setup lang="ts">
import { onMounted } from 'vue'
import { useSessionAuth } from '@/composables/useSessionAuth'

const { user, isAuthenticated, fetchUser, logout } = useSessionAuth()

onMounted(() => {
  fetchUser()
})
</script>

<template>
  <div v-if="isAuthenticated">
    <h1>Witaj, {{ user?.name }}</h1>
    <button @click="logout">Wyloguj się</button>
  </div>
  <div v-else>
    <p>Ładowanie lub brak dostępu, zaloguj się.</p>
  </div>
</template>
