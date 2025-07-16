<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3'
import navbar from '@/Components/navbar.vue'
</script>

<template>
  <app-head title="Home Page" />
  <div class="w-full flex flex-col sm:items-center sm:justify-center">
    <div class="flex w-full">
      <navbar>
        <h1 class="font-bold text-6xl">
          Znajdź
          <InertiaLink href="#" class="text-gradient-teal">
            wydarzenie
          </InertiaLink><br>
          w Twojej okolicy
        </h1>
      </navbar>
    </div>
    <div class="sm:w-5/6 flex flex-col">
      <div class="mb-36">
        <div class="w-full h-[777px] bg-[#E5E7EB] rounded-3xl">
          <h1>mapa</h1>
        </div>
      </div>
    </div>
    <div
      class="w-full bg-gradient-to-tr from-brand font-normal to-brand-light pb-8"
    >
      <div
        class="flex flex-col items-center justify-center text-center text-white space-y-16"
      >
        <h1 class="text-6xl font-bold pt-6">
          Bądz na bieżąnco<br>
          <span class="text-gradient-teal-light">dołącz do LetsEvent</span>
        </h1>
        <div class="flex font-normal justify-center gap-8">
          <p>Dodaj własne wydarzenie</p>
          <p>Bierz udział w wydarzeniach</p>
        </div>
        <InertiaLink href="/register" class="text-black">
          zarejestruj się
        </InertiaLink>
        <div class="w-4/6 flex justify-between text-gray-500">
          <p>Wszystkie prawa zastrzeżone © 2025 Interns2025b</p>
          <div>
            <ul class="flex items-center gap-4">
              <li>
                <a href="/regulamin" class="hover:underline">Regulamin</a>
              </li>
              <li aria-hidden="true">•</li>
              <li>
                <a href="/polityka-prywatnosci" class="hover:underline">Polityka prywatności</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
