<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue'
import BaseButton from '@/Components/BaseButton.vue'
import InfoBlock from '@/Components/InfoBlock.vue'
import Map from '@/Components/Map.vue'
import Navbar from '@/Components/Navbar.vue'

import {
  ArrowRightCircleIcon,
  CheckCircleIcon,
  EnvelopeIcon,
  ShareIcon,
  StarIcon,
  CalendarIcon,
  UsersIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'

const bannerSrc = 'https://picsum.photos/100/100'
const eventDate = '26 maja 2025 – 12:00'
const eventTitle = 'Taylor Swift – City of Lover'
const venueName = 'Stadion Narodowy'
const venueAddress = 'al. Księcia Józefa Poniatowskiego 1, 03-901 Warszawa'
const isPaid = true
</script>

<template>
  <AppHead title="Event" />
  <div class="w-full mb-16 sm:mb-12 flex-col">
    <Navbar class="mb-[72px]" />

    <div class="w-full h-[400px] bg-red-200 relative">
      <img :src="bannerSrc" alt="Event Banner" class="size-full object-cover">

      <div
        class="sm:hidden absolute left-1/2 -translate-x-1/2 bottom-[-32px] flex justify-between items-center bg-white rounded-full shadow px-6 py-3 w-fit max-w-full"
      >
        <p class="text-brand-dark font-medium whitespace-nowrap">
          12k osób weźmie udział
        </p>
        <base-button
          class="ml-4 h-[28px] bg-brand-dark text-white px-4 py-1 rounded-lg text-sm whitespace-nowrap"
        >
          Invite
        </base-button>
      </div>
    </div>

    <div class="flex flex-col items-center justify-center">
      <div
        class="w-full sm:py-16 pt-16 p-8 bg-white sm:shadow-lg sm:border sm:border-gray-300 flex justify-center items-center"
      >
        <div class="sm:w-11/12 w-full space-y-2">
          <div
            class="flex max-sm:hidden justify-between items-center w-full text-center"
          >
            <h2 class="text-2xl text-brand-light font-semibold">
              {{ eventDate }}
            </h2>
            <p
              v-if="isPaid"
              class="bg-[#0A84FF33] px-8 py-2 rounded-2xl font-semibold text-blue-600"
            >
              Płatny
            </p>
          </div>

          <h1 class="text-5xl font-bold">{{ eventTitle }}</h1>

          <div
            class="flex max-sm:hidden max-xl:flex-col sm:justify-between items-start"
          >
            <div>
              <h3 class="text-2xl font-medium">{{ venueName }}</h3>
              <h4 class="text-lg text-gray-400 font-normal">
                Brak ograniczenia wiekowego
              </h4>
            </div>

            <div>
              <div
                class="hidden max-sm:flex-wrap max-sm:flex max-sm:gap-2 sm:flex lg:justify-end font-semibold text-xl flex-wrap gap-3"
              >
                <base-button
                  class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded"
                >
                  <span
                    class="inline-flex font-semibold items-center space-x-2"
                  >
                    <StarIcon class="size-6" />
                    <span>Zainteresowany(a)</span>
                  </span>
                </base-button>

                <base-button
                  class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded"
                >
                  <span
                    class="inline-flex font-semibold items-center space-x-2"
                  >
                    <CheckCircleIcon class="size-6" />
                    <span>Wezmę udział</span>
                  </span>
                </base-button>

                <base-button
                  class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded"
                >
                  <span
                    class="inline-flex font-semibold items-center space-x-2"
                  >
                    <EnvelopeIcon class="size-6" />
                    <span>Zaproś</span>
                  </span>
                </base-button>

                <base-button
                  class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded"
                >
                  <span
                    class="inline-flex font-semibold items-center space-x-2"
                  >
                    <ShareIcon class="size-6" />
                  </span>
                </base-button>

                <base-button
                  class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded"
                >
                  <span
                    class="inline-flex font-semibold items-center space-x-2"
                  >
                    <span>...</span>
                  </span>
                </base-button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex sm:w-11/12 sm:mt-6">
        <div class="flex w-full max-md:flex-col md:space-x-6 max-md:space-y-6">
          <div
            class="flex w-full flex-col rounded-lg shadow-lg bg-white lg:py-16 p-8 lg:px-32 md:w-4/6 max-w-full"
          >
            <div class="flex flex-col gap-y-8 w-full">
              <InfoBlock
                :icon="UsersIcon"
                title="12056 osób weźmie udział"
                class="max-sm:hidden"
              />
              <InfoBlock
                :icon="CalendarIcon"
                title="26 maja 2025"
                line2="Godzina 12:00"
              />
              <InfoBlock
                title="Stadion Narodowy"
                line1="al. Księcia Józefa Poniatowskiego 1"
                line2="03‑901 Warszawa"
              />

              <div
                class="w-full flex flex-col min-[467px]:flex-row justify-between gap-4"
              >
                <InfoBlock
                  :icon="UserIcon"
                  title="Piotr Nowak"
                  line1="Organiztor"
                />
                <div class="flex items-center justify-start sm:justify-end">
                  <button class="bg-brand/10 text-brand px-3 py-1 rounded-xl">
                    Obserwuj
                  </button>
                </div>
              </div>

              <h1 class="font-medium text-3xl text-[#120D26]">Informacje</h1>
              <p class="font-normal text-xl text-[#120D26]">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit Lorem
                ipsum dolor sit amet, consectetur adipiscing elit Lorem ipsum
                dolor sit amet, consectetur adipiscing elit Lorem ipsum dolor
                sit amet, consectetur adipiscing elit Lorem ipsum dolor sit
                amet, consectetur adipiscing elit Lorem ipsum dolor sit amet,
                consectetur adipiscing elit Lorem ipsum dolor sit amet,
                consectetur adipiscing elit Lorem ipsum dolor sit amet,
                consectetur adipiscing elit Lorem ipsum dolor sit amet,
                consectetur adipiscing elit Lorem ipsum dolor sit amet,
                consectetur adipiscing elit Lorem ipsum dolor sit amet,
                consectetur adipiscing elit
              </p>
            </div>
          </div>

          <div class="flex flex-col space-y-6 md:w-2/6 justify-start">
            <div
              class="sm:hidden fixed bottom-0 left-0 w-full z-[1001] bg-white p-4 shadow-t"
            >
              <base-button
                class="w-full text-base h-16 p-[15px] bg-brand-dark text-white rounded-2xl"
              >
                <span
                  class="inline-flex font-semibold items-center justify-center space-x-2"
                >
                  <span>KUP BILET</span>
                  <ArrowRightCircleIcon class="size-6" />
                </span>
              </base-button>
            </div>

            <div
              class="max-sm:hidden w-full rounded-lg shadow-lg !mt-0 bg-white lg:py-11 lg:px-16 p-8 lg:space-y-8 space-y-4"
            >
              <p class="text-2xl font-medium">Bilety</p>
              <div class="text-center">
                <base-button
                  class="w-full mb-1 text-base p-[15px] bg-brand-dark text-white rounded-2xl"
                >
                  <span
                    class="inline-flex font-semibold items-center justify-center space-x-2"
                  >
                    <span>KUP BILET</span>
                    <ArrowRightCircleIcon class="size-6" />
                  </span>
                </base-button>
              </div>
            </div>

            <div
              class="rounded-lg shadow-lg bg-white space-y-6 lg:py-11 lg:px-16 p-8"
            >
              <div
                class="lg:-mx-16 lg:-mt-11 -mt-8 -mx-8 bg-red-200 h-96 rounded-2xl"
              >
                <Map />
              </div>
              <div class="content-center">
                <p class="font-medium text-3xl">{{ venueName }}</p>
                <p class="font-normal text-gray-400 text-xl">
                  {{ venueAddress }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
