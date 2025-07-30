<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import EventCard from '@/Components/EventCard.vue'
import PaginationComponent from '@/Components/PaginationComponent.vue'
import BaseInput from '@/Components/BaseInput.vue'
import DropdownFilters from '@/Components/DropdownFilters.vue'
import { formatDate } from '@/utilities/formatDate'
import { useEvents } from '@/composables/useEvents'
import { useSearch } from '@/composables/useSearch'
import { MapPinIcon, CalendarIcon } from '@heroicons/vue/24/outline'
import Socials from '@/Components/Socials.vue'
import ActiveFilters from '@/Components/ActiveFilters.vue'

const { events, page, meta, prevPage, nextPage } = useEvents({ all: true })

const {
  query,
  filtered,
  activeFields,
  availableFields,
  dateFilter,
} = useSearch(events, ['id', 'title', 'location'])
</script>

<template>
  <AppHead title="Home Page" />

  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full md:mb-32 mb-8">
      <Navbar />
    </div>
    <div class="md:w-5/6 flex flex-col">
      <div class="md:mb-36 mb-16">
        <div class="w-full relative">
          <img
            src="/images/Fade.svg"
            alt=""
            class="absolute w-full h-[1000px] top-[-430px] pointer-events-none"
          />
        </div>
        <div class="w-full relative flex flex-col items-center lg:pt-6 bg-[#F2F2F2] overflow-visible md:rounded-xl">
          <div
            class="flex items-center justify-center mb-6 text-sm gap-x-2 [&>*]:flex-col max-lg:grid max-lg:grid-cols-2"
          >
            <div class="lg:w-6/12 col-span-2">
              <BaseInput
                id="search"
                v-model="query"
                label="Szukaj"
                type="text"
                append-position="left"
                variant="event"
              >
                <template #append>
                  <MapPinIcon
                    class="size-5 text-brand-light absolute left-3 top-3.5 pointer-events-none"
                  />
                </template>
              </BaseInput>
              <ActiveFilters v-model="activeFields" />
            </div>

            <div class="lg:w-4/12">
              <BaseInput
                id="date"
                v-model="dateFilter"
                name="date"
                label="Data"
                type="date"
                append-position="left"
                variant="event"
              >
                <template #append>
                  <CalendarIcon
                    class="absolute inset-0 -z-10 w-full pointer-events-none"
                  />
                </template>
              </BaseInput>
            </div>

            <div class="lg:w-3/12">
              <DropdownFilters
                v-model="activeFields"
                :fields="availableFields"
              />
            </div>
          </div>

          <h3 class="w-5/6 text-left font-medium size-4 text-gray-800 my-3">Twoje Wydarzenia</h3>
          <div class="flex w-full h-5/6 items-center justify-center ">
            <div class="flex gap-8">
              <EventCard
                v-for="e in filtered"
                :id="e.id"
                :key="e.id"
                :image-url="e.image_url"
                :start="formatDate(e.start)"
                :is-paid="e.is_paid"
                :title="e.title"
                :location="e.location"
                :age-category="e.age_category"
              />
            </div>
            <p
              v-if="filtered.length === 0"
              class="mt-8 text-center text-gray-500"
            >
              Brak wyników.
            </p>
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
          Nic Cię nie interesuje?<br>
          <span class="text-gradient-teal-light">
            Stwórz własne wydarzenie w LetsEvent
          </span>
        </h1>
        <div class="flex gap-x-8">
          <inertia-link
            href="/event"
            class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]"
          >
            Dodaj własne wydarzenie
          </inertia-link>
        </div>
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
