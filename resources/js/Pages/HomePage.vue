<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import Map from '@/Components/Map.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
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
        <div
          class="w-full flex flex-col items-center lg:pt-6 bg-[#F2F2F2] md:rounded-xl"
        >
          <div
            class="flex items-center justify-center mb-6 max-lg:mt-6 max-lg:mx-2 text-sm gap-x-2 h-1/6 [&>*]:flex [&>*]:mb-1 [&>*]:flex-col [&_*]:text-black max-lg:grid max-lg:grid-cols-2"
          >
            <div class="lg:w-6/12 col-span-2">
              <BaseInput id="city" name="email" label="Miasto" type="text" />
            </div>
            <div class="lg:w-3/12">
              <BaseInput id="date" name="email" label="Data" type="date" />
            </div>
            <div class="lg:w-2/12">
              <BaseInput
                id="city"
                name="email"
                label="Rodzaj wydarzenia"
                type="text"
              />
            </div>
            <base-button
              class="bg-black !text-white justify-center font-bold px-10 mt-[24px] col-span-2"
            >
              Szukaj
            </base-button>
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
            Dodaj własne wydarzenie
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
          zarejestruj się
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
              <div
                class="flex justify-center items-center text-gray-500 order-2 max-sm:order-1 gap-x-4 max-sm:mb-6 max-sm:gap-x-6 max-sm:[&>*]:size-10 [&>*]:size-[14px] gray-400"
              >
                <a href="#"><img src="/images/x.svg" alt="X"></a>
                <a href="#"><img src="/images/linkedin.svg" alt="LinkedIn"></a>
                <a href="#"><img src="/images/youtube.svg" alt="Youtube"></a>
                <a href="#"><img src="/images/slack.svg" alt="Slack"></a>
              </div>
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
