<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import Map from '@/Components/Map.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import Socials from '@/Components/Socials.vue'
import { MapPinIcon, CalendarIcon } from '@heroicons/vue/24/outline'
</script>

<template>
  <app-head title="Home Page" />
  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full md:mb-32 mb-8">
      <navbar>
        <h1 class="justify-center font-bold text-6xl">
          Znajdź
          <span class="text-gradient-teal"> wydarzenie </span><br>
          w Twojej okolicy
        </h1>
      </navbar>
    </div>
    <div class="md:w-5/6 flex flex-col">
      <div class="md:mb-36 mb-16">
        <div class="w-full relative">
          <img src="/images/Fade.svg" alt="" class="flex-1 absolute w-full h-[1000px] inset-0 top-[-430px] pointer-events-none">
        </div>
        <div
          class="w-full relative flex flex-col items-center lg:pt-6 bg-[#F2F2F2] overflow-visible md:rounded-xl"
        >
          <div
            class="flex items-center border-none justify-center mb-6 max-lg:mt-6 max-lg:mx-2 text-sm gap-x-2 h-1/6 [&>*]:mb-1 [&>*]:flex-col max-lg:grid max-lg:grid-cols-2"
          >
            <div class="lg:w-6/12 col-span-2">
              <BaseInput
                id="city"
                name="city"
                label="Miasto"
                type="text"
                placeholder="Legnica"
                append-position="left"
                variant="event"
              >
                <template #append>
                  <MapPinIcon class="size-5 text-brand-light absolute left-3 top-3.5 pointer-events-none" />
                </template>
              </BaseInput>
            </div>
            <div class="lg:w-4/12">
              <BaseInput
                id="date"
                name="date"
                label="Data"
                type="date"
                append-position="left"
                variant="event"
              >
                <template #append>
                  <CalendarIcon class="absolute inset-0 -z-10 w-full pointer-events-none object-cover" />
                </template>
              </BaseInput>
            </div>
            <div class="lg:w-3/12">
              <BaseInput
                id="xDD"
                name="email"
                label="Rodzaj wydarzenia"
                type="text"
                placeholder="koncert"
                variant="event"
              />
            </div>
            <BaseButton class="!bg-zinc-800 !text-white justify-center font-bold px-10 mt-[24px] col-span-2">
              Szukaj
            </BaseButton>
          </div>
          <div class="w-full h-5/6 bg-white rounded-b-xl">
            <Map class="min-h-96 aspect-[2/1] max-md:aspect-square" />
          </div>
        </div>
      </div>
    </div>
    <div
      class="w-full bg-gradient-to-tr from-brand font-normal to-brand-light py-16 max-sm:pt-8 max-sm:pb-3"
    >
      <div
        class="flex flex-col items-center justify-center text-center text-white space-y-16 max-sm:space-y-8"
      >
        <h1 class="text-6xl font-bold pt-6">
          Bądź na bieżaco<br>
          <span class="text-gradient-teal-light">dołącz do LetsEvent</span>
        </h1>
        <div
          class="flex max-[475px]:flex-col font-normal justify-center gap-x-8 max-sm:gap-4"
        >
          <inertia-link
            href="/event"
            class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]"
          >
            Dodawaj własne wydarzenia
          </inertia-link>
          <inertia-link
            href="/event"
            class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]"
          >
            Bierz udział w wydarzeniach
          </inertia-link>
        </div>
        <InertiaLink
          href="/register"
          class="bg-white font-normal rounded-full text-black shadow-shadow-blue hover:scale-105 shadow-sm py-3 px-8"
        >
          Zarejestruj się
        </InertiaLink>
        <div class="lg:w-5/6 lg:flex lg:justify-between text-gray-500">
          <div class="order-1 lg:order-2 max-lg:mb-1">
            <div class="flex max-sm:flex-col order-2 gap-x-4">
              <ul class="flex justify-center order-1 max-sm:order-2 gap-x-4">
                <li>
                  <InertiaLink
                    href="#"
                    class="hover:underline hover:text-gray-400"
                  >
                    Regulamin
                  </InertiaLink>
                </li>
                <li aria-hidden="true">•</li>
                <li>
                  <InertiaLink
                    href="#"
                    class="hover:underline hover:text-gray-400"
                  >
                    Polityka prywatności
                  </InertiaLink>
                </li>
              </ul>
              <socials />
            </div>
          </div>
          <div class="order-2 lg:order-1 max-sm:mt-5">
            <p>Wszystkie prawa zastrzeżone &copy; 2025 Interns2025b</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
