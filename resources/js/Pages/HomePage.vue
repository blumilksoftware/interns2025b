<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Map from '@/Components/Map.vue'
import BaseInput from '@/Components/BaseInput.vue'
import { CalendarIcon, MagnifyingGlassIcon, MapPinIcon, ListBulletIcon } from '@heroicons/vue/24/outline'
import Footer from '@/Components/Footer.vue'
import AppHead from '@/Components/AppHead.vue'
import { useEvents } from '@/composables/useEvents'
import { useSearch } from '@/composables/useSearch'
import ActiveFilters from '@/Components/ActiveFilters.vue'
import DropdownFilters from '@/Components/DropdownFilters.vue'
import { useI18n } from 'vue-i18n'
import InfoBlock from '@/Components/InfoBlock.vue'
import { formatDate, formatTime } from '@/utilities/formatDate'
import { Link as InertiaLink } from '@inertiajs/vue3'

const { t, locale } = useI18n()



const { activeEvents, fetchAll } = useEvents({ all: true, activeOnly: true })

const {
  query,
  dateFilter,
  activeFields,
  availableFields,
  filtered,
} = useSearch(activeEvents, ['title','location','age_category','id'])

const activeView = ref<'map' | 'list'>('map')

onMounted(() => {
  fetchAll().catch(() => {
    alert(t('home.fetch_error'))
  })
})
</script>

<template>
  <app-head :title="t('home.title')" />
  <div class="w-full flex flex-col md:items-center justify-center">
    <div class="flex w-full md:mb-32 mb-8">
      <navbar>
        <h1 class="justify-center font-bold text-6xl">
          {{ t('home.find') }}
          <span class="text-gradient-teal">{{ t('home.event') }}</span><br>
          {{ t('home.near_you') }}
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
        <div class="w-full relative flex flex-col items-center lg:pt-6 bg-[#F2F2F2] overflow-visible md:rounded-xl">
          <div
            class="flex items-center border-none justify-center mb-8 max-lg:mt-6 max-lg:mx-2 text-sm gap-x-2 gap-y-8 h-1/6 [&>*]:mb-1 [&>*]:flex-col max-lg:grid max-lg:grid-cols-2"
          >
            <div class="lg:w-6/12 col-span-2">
              <BaseInput
                id="city"
                v-model="query"
                name="city"
                :label="t('home.search_events')"
                type="text"
                append-position="left"
                variant="event"
              >
                <template #append>
                  <MagnifyingGlassIcon class="size-5 text-brand-light absolute left-3 top-3.5 pointer-events-none" />
                </template>
              </BaseInput>
              <ActiveFilters v-model="activeFields" :options="availableFields" />
            </div>
            <div class="lg:w-4/12">
              <BaseInput
                id="date"
                v-model="dateFilter"
                name="date"
                :label="t('home.date')"
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
              <DropdownFilters v-model="activeFields" :fields="availableFields" />
            </div>
          </div>

          <div class="w-full relative bg-white rounded-b-xl">
            <div class="absolute top-2 left-1/2 -translate-x-1/2 z-30 flex items-center gap-2 pointer-events-auto">
              <button
                type="button"
                :class="[
                  'px-3 py-1 rounded-md text-sm border flex items-center',
                  activeView === 'map' ? 'bg-brand-light text-white border-brand-light' : 'bg-white text-gray-700'
                ]"
                :aria-pressed="activeView === 'map'"
                title="Mapa"
                @click="activeView = 'map'"
              >
                <MapPinIcon class="inline size-4 mr-2" />
                {{ t('home.show_map') }}
              </button>

              <button
                type="button"
                :class="[
                  'px-3 py-1 rounded-md text-sm border flex items-center',
                  activeView === 'list' ? 'bg-brand-light text-white border-brand-light' : 'bg-white text-gray-700'
                ]"
                :aria-pressed="activeView === 'list'"
                title="Lista"
                @click="activeView = 'list'"
              >
                <ListBulletIcon class="inline size-4 mr-2" />
                {{ t('home.show_list') }}
              </button>
            </div>

            <div v-if="activeView === 'map'" class="size-full">
              <Map
                :events="filtered"
                :center="[51.21,16.16]"
                class="size-full min-h-96 aspect-[2/1] max-md:aspect-square relative z-0"
              />
            </div>

            <div v-else class="size-full overflow-auto pt-12">
              <div class="max-w-5xl mx-auto">
                <div v-if="filtered.length > 0" class="mb-6">
                  <h3 class="text-left font-medium text-gray-800 mb-4">{{ t('event.browseEvents') }}</h3>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <InertiaLink
                      v-for="e in filtered"
                      :key="e.id"
                      :href="`/events/${e.id}`"
                      class="block hover:shadow-lg transition-shadow rounded-lg overflow-hidden"
                    >
                      <InfoBlock
                        :header="`${formatDate(e.start, locale)} - ${formatTime(e.start, locale)}`"
                        :title="e.title"
                        :image-url="e.image_url"
                        :info-items="[ e.location ?? t('event.no_location'), e.age_category ? t(`home.age_category.${e.age_category}`) : t('home.no_age_restriction')]"
                        class="bg-white p-4"
                      />
                    </InertiaLink>
                  </div>
                </div>

                <p v-if="filtered.length === 0" class="mt-8 text-center text-gray-500">
                  {{ t('home.no_results') }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>
