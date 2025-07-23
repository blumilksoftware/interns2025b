<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useUser } from '@/composables/useSessionAuth'
const { logout } = useUser()

const page = usePage<{ auth: { user: { name: string, email: string } | null } }>()
const user = computed(() => page.props.auth.user)
</script>

<template>
  <div v-if="user">
    <h1>Witaj, {{ user.name }}</h1>
    <p>Email: {{ user.email }}</p>
    <button class="btn-logout" @click="logout">
      Wyloguj się
    </button>
  </div>
</template>
