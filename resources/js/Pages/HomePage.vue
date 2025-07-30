<script setup lang="ts">
import Navbar from '@/Components/Navbar.vue'
import Map from '@/Components/Map.vue'
import BaseInput from '@/Components/BaseInput.vue'
import { CalendarIcon } from '@heroicons/vue/24/outline'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import Footer from '@/Components/Footer.vue'
import AppHead from '@/Components/AppHead.vue'
import { useEvents } from '@/composables/useEvents'
import { useSearch } from '@/composables/useSearch'
import ActiveFilters from '@/Components/ActiveFilters.vue'
import { onMounted } from 'vue'
import DropdownFilters from '@/Components/DropdownFilters.vue'

const { activeEvents, fetchAll } = useEvents({ all: true, activeOnly: true })

const {
  query,
  dateFilter,
  activeFields,
  availableFields,
  filtered,
} = useSearch(activeEvents, ['title','location','age_category','id'])


onMounted(() => {
  fetchAll().catch(err => {
    alert('Nie udało się pobrać eventów')
  })
})
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
          <img
            src="/images/Fade.svg"
            alt=""
            class="flex-1 absolute w-full h-[1000px] inset-0 top-[-430px] pointer-events-none"
          >
        </div>
        <div
          class="w-full relative flex flex-col items-center lg:pt-6 bg-[#F2F2F2] overflow-visible md:rounded-xl"
        >
          <div
            class="flex items-center border-none justify-center mb-8 max-lg:mt-6 max-lg:mx-2 text-sm gap-x-2 gap-y-8 h-1/6 [&>*]:mb-1 [&>*]:flex-col max-lg:grid max-lg:grid-cols-2"
          >
            <div class="lg:w-6/12 col-span-2">
              <BaseInput
                id="city"
                v-model="query"
                name="city"
                label="Wyszukaj wydarzenia"
                type="text"
                append-position="left"
                variant="event"
              >
                <template #append>
                  <MagnifyingGlassIcon
                    class="size-5 text-brand-light absolute left-3 top-3.5 pointer-events-none"
                  />
                </template>
              </BaseInput>
              <ActiveFilters v-model="activeFields" :options="availableFields" />
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
                    class="absolute inset-0 -z-10 w-full pointer-events-none object-cover"
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
          <div class="w-full h-5/6 bg-white rounded-b-xl">
            <Map
              :events="filtered"
              :center="[51.21,16.16]"
              class="min-h-96 aspect-[2/1] max-md:aspect-square"
            />
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>
