<script setup lang="ts">
import { useAuth } from '@/composables/useAuth'

const { isLoggedIn, logout } = useAuth()
</script>

<template>
  <div class="w-full">
    <div class="flex font-medium items-center md:justify-end justify-between gap-y-5 pt-10 sm:gap-x-10 mx-5 mb-10">
      <template v-if="!isLoggedIn">
        <InertiaLink href="/login" class="hover:underline">Zaloguj się</InertiaLink>
        <InertiaLink href="/register"
                     class="bg-black text-white rounded-full shadow-shadow-blue hover:scale-105 shadow-sm py-2 px-8"
        >
          Zarejestruj się
        </InertiaLink>
      </template>

      <template v-else>
        <InertiaLink href="/profile" class="hover:underline">Profil</InertiaLink>
        <button class="bg-black text-white rounded-full shadow-shadow-blue hover:scale-105 shadow-sm py-2 px-8"
                @click.prevent="logout"
        >
          Wyloguj się
        </button>
      </template>
    </div>

    <div class="flex flex-col space-y-6 items-center justify-center text-center mb-6">
      <img src="/images/LogoBrand.png" alt="LetsEvent">
      <slot />
    </div>
  </div>
</template>
