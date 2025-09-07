<script setup lang="ts">
import { computed } from 'vue'
import { usePage, Link as InertiaLink } from '@inertiajs/vue3'
import Socials from '@/Components/Socials.vue'
import type { AuthProps } from '@/types/types'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const page = usePage()
const authProps = computed(() => (page.props as unknown) as AuthProps)
const isLoggedIn = computed(() => !!authProps.value.auth.user)
const isHome = computed(() => page.url === '/')
const roles = computed<string[]>(() => (authProps.value.auth.user as any)?.roles ?? [])
const isAdmin = computed(() => roles.value.includes('administrator') || roles.value.includes('superAdministrator'))
</script>

<template>
  <div class="w-full bg-gradient-to-tr from-brand font-normal to-brand-light py-16 max-sm:pt-8 max-sm:pb-3">
    <div class="flex flex-col items-center justify-center text-center text-white space-y-16 max-sm:space-y-8">
      <template v-if="isHome">
        <h1 class="text-6xl font-bold pt-6">
          {{ t('home.heroLine1') }}<br>
          <span class="text-gradient-teal-light">{{ t('home.heroLine2') }}</span>
        </h1>

        <div class="flex max-[475px]:flex-col font-normal justify-center gap-x-8 max-sm:gap-4">
          <InertiaLink href="/event/create" class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]">
            {{ t('footer.createEvent') }}
          </InertiaLink>
          <InertiaLink href="/event" class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]">
            {{ t('footer.joinEvents') }}
          </InertiaLink>
          <InertiaLink
            v-if="isAdmin"
            href="/organizations/create"
            class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]"
          >
            {{ t('footer.createOrganization') }}
          </InertiaLink>
          <InertiaLink
            v-if="isAdmin"
            href="/users/create"
            class="border border-[#FFFFFF1A] rounded-full px-[13px] py-[5px]"
          >
            {{ t('footer.createUser') }}
          </InertiaLink>
        </div>

        <InertiaLink
          v-if="!isLoggedIn"
          href="/register"
          class="bg-white font-normal rounded-full text-black shadow-shadow-blue hover:scale-105 shadow-sm py-3 px-8"
        >
          {{ t('footer.register') }}
        </InertiaLink>
      </template>

      <div class="lg:w-5/6 lg:flex lg:justify-between text-gray-500">
        <div class="order-1 lg:order-2 max-lg:mb-1">
          <div class="flex max-sm:flex-col order-2 gap-x-4">
            <ul class="flex justify-center order-1 max-sm:order-2 gap-x-4">
              <li>
                <InertiaLink href="#" class="hover:underline hover:text-gray-400">
                  {{ t('footer.terms') }}
                </InertiaLink>
              </li>
              <li aria-hidden="true">•</li>
              <li>
                <InertiaLink href="#" class="hover:underline hover:text-gray-400">
                  {{ t('footer.privacy') }}
                </InertiaLink>
              </li>
            </ul>
            <Socials />
          </div>
        </div>
        <div class="order-2 lg:order-1 max-sm:mt-5">
          <p>{{ t('footer.rights', { year: new Date().getFullYear(), company: 'Interns2025b' }) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

