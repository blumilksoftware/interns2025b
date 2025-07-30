<script setup lang="ts">
import { Link as InertiaLink, usePage } from '@inertiajs/vue3'
import { useAuth } from '@/composables/useAuth'
import { computed } from 'vue'

const page = usePage()

const { isLoggedIn, logout } = useAuth()

const isHomePage = computed(() => page.url === '/')
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

    <div
      class="flex flex-col space-y-6 items-center justify-center text-center mb-6"
    >
      <template v-if="isHomePage">
        <img src="/images/LogoBrand.png" alt="LetsEvent">
      </template>
      <template v-else>
        <InertiaLink href="/">
          <img src="/images/LogoBrand.png" alt="LetsEvent" class="hover:opacity-80 transition-opacity">
        </InertiaLink>
      </template>

      <slot />
    </div>
  </div>
</template>
