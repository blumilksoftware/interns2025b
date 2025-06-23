<script setup lang="ts">
import { ref } from 'vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'


const email = ref('')
const password = ref('')
</script>

<template>
  <AuthLayout>
    <template #left>
      <div class="flex-auto bg-gray-200 min-w-[300px] max-w-[70%]">
        <h2>Welcome back</h2>
        <form @submit.prevent="submit">
          <BaseInput
            v-model="email"
            placeholder="Email"
            error="Niepoprawny format"
          />
          <BaseInput
            v-model="password"
            type="password"
            placeholder="Hasło"
          />
          <button class="" type="submit">Zaloguj się</button>
        </form>
      </div>
    </template>
    <template #right>
      <h2>Welcome Back!</h2>
      <p>Please login to access your dashboard.</p>
    </template>
  </AuthLayout>
</template>

<style scoped>

</style>
