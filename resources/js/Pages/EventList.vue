<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import BaseInput from '@/Components/BaseInput.vue'
import InfoBlock from '@/Components/InfoBlock.vue'
import EventCard from '@/Components/EventCard.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { MapPinIcon, CalendarIcon } from '@heroicons/vue/24/outline'
import { formatDate } from '@/utilities/formatDate'
import { useEvents } from '@/composables/useEvents'

const { events, search, page, meta, prevPage, nextPage } = useEvents()
</script>

<template>
  <AppHead title="Wydarzenia" />
  <div class="flex items-center flex-col w-full">
    <Navbar />
    <div class="md:w-5/6 flex flex-col">
      <div class="md:mb-36 mb-16">
        <div class="w-full relative">
          <img src="/images/Fade.svg" alt="" class="flex-1 absolute w-full h-[1000px] inset-0 top-[-430px] pointer-events-none">
        </div>
        <div class="w-full relative flex flex-col items-center lg:pt-6 bg-[#F2F2F2] overflow-visible md:rounded-xl">
          <div class="flex items-center border-none justify-center mb-6 max-lg:mt-6 max-lg:mx-2 text-sm gap-x-2 h-1/6 [&>*]:mb-1 [&>*]:flex-col max-lg:grid max-lg:grid-cols-2">
            <div class="lg:w-6/12 col-span-2">
              <BaseInput id="city" v-model="search" name="city" label="Miasto" type="text" placeholder="Legnica" append-position="left" variant="event">
                <template #append>
                  <MapPinIcon class="size-5 text-brand-light absolute left-3 top-3.5 pointer-events-none" />
                </template>
              </BaseInput>
            </div>
            <div class="lg:w-4/12">
              <BaseInput id="date" name="date" label="Data" type="date" append-position="left" variant="event">
                <template #append>
                  <CalendarIcon class="absolute inset-0 -z-10 w-full pointer-events-none object-cover" />
                </template>
              </BaseInput>
            </div>
            <div class="lg:w-3/12">
              <BaseInput id="type" name="type" label="Rodzaj wydarzenia" type="text" placeholder="koncert" variant="event" />
            </div>
            <BaseButton class="!bg-zinc-800 !text-white justify-center font-bold px-10 mt-[24px] col-span-2" @click="page = 1">
              Szukaj
            </BaseButton>
          </div>

          <section class="w-full max-w-5xl mx-auto mt-12">
            <h2 class="text-2xl font-semibold mb-4">Przeglądaj wydarzenia</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
              <EventCard
                v-for="event in events"
                :id="event.id"
                :key="event.id"
                :image-url="event.image_url"
                :start="formatDate(event.start)"
                :is-paid="event.is_paid"
                :title="event.title"
                :location="event.location"
                :age-category="event.age_category"
              />
            </div>
          </section>

          <section class="w-full max-w-4xl mx-auto mt-12 flex flex-col gap-6">
            <InfoBlock
              v-for="event in events"
              :key="event.id"
              :icon="MapPinIcon"
              :title="event.title"
              :line1="event.location || 'Brak lokalizacji'"
              :line2="formatDate(event.start)"
              :line3="event.age_category || 'Brak'"
            />
            <p v-if="events.length === 0" class="col-span-full text-center text-gray-500">
              Brak wydarzeń do wyświetlenia.
            </p>
          </section>

          <div v-if="meta.last_page > 1" class="flex justify-center items-center gap-4 mt-8 mb-12">
            <button class="px-4 py-2 bg-gray-200 rounded" :disabled="page === 1" @click="prevPage">Poprzednia</button>
            <span>Strona {{ page }} z {{ meta.last_page }}</span>
            <button class="px-4 py-2 bg-gray-200 rounded" :disabled="page === meta.last_page" @click="nextPage">Następna</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
