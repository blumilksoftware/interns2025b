<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import Map from '@/Components/Map.vue'
</script>

<template>
  <app-head title="Home Page" />
  <div class="w-full flex flex-col sm:items-center sm:justify-center">
    <div class="flex w-full mb-32">
      <navbar>
        <h1 class="font-bold text-6xl">
          Znajdź
          <span class="text-gradient-teal">
            wydarzenie
          </span><br>
          w Twojej okolicy
        </h1>
      </navbar>
    </div>
    <div class="sm:w-5/6 flex flex-col">
      <div class="mb-36">
        <div class="w-full h-[777px] bg-[#E5E7EB] rounded-3xl">
          <Map />
        </div>
      </div>
    </div>
    <div
      class="w-full bg-gradient-to-tr from-brand font-normal to-brand-light py-16"
    >
      <div
        class="flex flex-col items-center justify-center text-center text-white space-y-16"
      >
        <h1 class="text-6xl font-bold pt-6">
          Bądź na bieżaco<br>
          <span class="text-gradient-teal-light">dołącz do LetsEvent</span>
        </h1>
        <div class="flex font-normal justify-center gap-8">
          <inertia-link href="/event" class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px] ">Dodaj własne wydarzenie</inertia-link>
          <inertia-link href="/event" class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px] ">Bierz udział w wydarzeniach</inertia-link>
        </div>
        <InertiaLink href="/register" class="bg-white font-normal rounded-full text-black shadow-shadow-blue hover:scale-105 shadow-sm p-3">
          zarejestruj się
        </InertiaLink>
        <div class="w-4/6 flex justify-between text-gray-500">
          <p>Wszystkie prawa zastrzeżone © 2025 Interns2025b</p>
          <div class="flex gap-8 items-center">
            <ul class="flex items-center gap-4">
              <li>
                <InertiaLink href="/regulamin" class="hover:underline hover:text-gray-400">Regulamin</InertiaLink>
              </li>
              <li aria-hidden="true">•</li>
              <li>
                <InertiaLink href="/polityka-prywatnosci" class="hover:underline hover:text-gray-400">Polityka prywatności</InertiaLink>
              </li>
            </ul>
            <div class="flex items-center gap-4">
              <a href="#">X</a>
              <a href="#">Ln</a>
              <a href="#">YT</a>
              <a href="#">Slack</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
