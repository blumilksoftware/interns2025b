<script setup lang="ts">
import { computed } from 'vue'
import { usePage, Link as InertiaLink } from '@inertiajs/vue3'
import Socials from '@/Components/Socials.vue'
import type { AuthProps } from '@/types/types'

const page = usePage()
const authProps = computed(() => (page.props as unknown) as AuthProps)
const isLoggedIn = computed(() => !!authProps.value.auth.user)
const isHome = computed(() => page.url === '/')
</script>

<template>
  <div class="w-full bg-gradient-to-tr from-brand font-normal to-brand-light py-16 max-sm:pt-8 max-sm:pb-3">
    <div class="flex flex-col items-center justify-center text-center text-white space-y-16 max-sm:space-y-8">
      <template v-if="isHome">
        <h1 class="text-6xl font-bold pt-6">
          Bądź na biconcave<br>
          <span class="text-gradient-teal-light">dołącz do LetsEvent</span>
        </h1>

        <div class="flex max-[475px]:flex-col font-normal justify-center gap-x-8 max-sm:gap-4">
          <InertiaLink href="/event/create" class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]">
            Dodawaj własne wydarzenia
          </InertiaLink>
          <InertiaLink href="/event" class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]">
            Bierz udział w wydarzeniach
          </InertiaLink>
          <InertiaLink href="/organizations/create" class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]">
            Twórz organizacje
          </InertiaLink>
        </div>

        <InertiaLink
          v-if="!isLoggedIn"
          href="/register"
          class="bg-white font-normal rounded-full text-black shadow-shadow-blue hover:scale-105 shadow-sm py-3 px-8"
        >
          Zarejestruj się
        </InertiaLink>
      </template>

      <div class="lg:w-5/6 lg:flex lg:justify-between text-gray-500">
        <div class="order-1 lg:order-2 max-lg:mb-1">
          <div class="flex max-sm:flex-col order-2 gap-x-4">
            <ul class="flex justify-center order-1 max-sm:order-2 gap-x-4">
              <li>
                <InertiaLink href="#" class="hover:underline hover:text-gray-400">Regulamin</InertiaLink>
              </li>
              <li aria-hidden="true">•</li>
              <li>
                <InertiaLink href="#" class="hover:underline hover:text-gray-400">Polityka prywatności</InertiaLink>
              </li>
            </ul>
            <Socials />
          </div>
        </div>
        <div class="order-2 lg:order-1 max-sm:mt-5">
          <p>Wszystkie prawa zastrzeżone &copy; 2025 Interns2025b</p>
        </div>
      </div>
    </div>
  </div>
</template>
