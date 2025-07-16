<script setup lang="ts">
import navbar from '@/Components/navbar.vue'
</script>

<template>
  <app-head title="Event" />
  <div class="w-full flex flex-col sm:items-center sm:justify-center">
    <div class="flex w-full pb-32">
      <div class="">
        <navbar />
        <div class="w-full h-[200px] bg-red-200">
          <img src="/images/#" alt="EventBanner">
        </div>
        <div class="flex flex-col justify-center items-center">
          <div class="flex w-full py-16 bg-white shadow-lg border border-gray-300 justify-center">
            <div class="w-11/12 space-y-2">
              <div class="flex justify-between text-center w-full">
                <h2 class="text-2xl text-brand-light font-semibold">26 Maja 2025 - 12:00</h2>
                <p class="bg-[#0A84FF33] px-8 py-2 rounded-2xl font-semibold text-blue-600">Płatny</p>
              </div>
              <h1 class="text-5xl font-bold">Taylor Swift - City of Lover</h1>
              <div class="flex justify-between">
                <div>
                  <h3 class="text-2xl font-medium">Stadion narodowy</h3>
                  <h4 class="text-lg text-gray-400 font-normal">Brak ograniczenia wiekowego</h4>
                </div>
                <div>
                  <div class="flex justify-end font-semibold text-xl px-5 py-2 space-x-3">
                    <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Zainteresowany(a)</button>
                    <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Wezmę udział</button>
                    <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Zaproś</button>
                    <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">-></button>
                    <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">...</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="flex justify-center w-11/12">
            <div class="flex w-full space-x-6 mt-6">
              <div class="bg-white py-16 px-32 rounded-lg shadow-lg w-4/6 flex justify-center">
                <h1>1</h1>
              </div>
              <div class="flex flex-col space-y-6 w-2/6 justify-center">
                <div class="w-full rounded-lg py-11 px-16 space-y-8 shadow-lg bg-white">
                  <p class="text-2xl font-medium">Bilety</p>
                  <div class="text-center">
                    <p class="font-medium text-base p-[15px] bg-brand-dark text-white rounded-2xl">KUP BILET</p>
                  </div>
                </div>
                <div class="w-full space-y-6 rounded-lg py-11 px-16 shadow-lg overflow-visible bg-white">
                  <div class="-mx-16 -mt-11 bg-red-200 h-96 rounded-2xl">
                    mapa
                  </div>
                  <div class="w-10/12 content-center">
                    <p class="font-medium text-3xl">Stadion narodowy</p>
                    <p class="font-normal text-gray-400 text-xl">al. Księcia Józefa Poniatowskiego 1, 03-901 Warszawa</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
