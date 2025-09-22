<script setup lang="ts">
import { Link as InertiaLink, usePage } from '@inertiajs/vue3'
import { useAuth } from '@/composables/useAuth'
import { computed } from 'vue'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
import { useI18n } from 'vue-i18n'

const page = usePage()

const { isLoggedIn, logout } = useAuth()

const isHomePage = computed(() => page.url === '/')

const { t } = useI18n()

</script>

<template>
  <div class="w-full">
    <div class="flex items-center justify-between pt-10 px-6 mb-10">
      <div />

      <div class="hidden md:flex items-center gap-4">
        <template v-if="!isLoggedIn">
          <InertiaLink href="/login" class="hover:underline text-sm">
            {{ t('auth.login') }}
          </InertiaLink>
          <InertiaLink href="/register" class="bg-black text-white rounded-full shadow-shadow-blue hover:scale-105 shadow-sm py-2 px-6 text-sm">
            {{ t('auth.register') }}
          </InertiaLink>
        </template>
        <template v-else>
          <InertiaLink href="/event/create" class="bg-brand-light text-white rounded-full py-2 px-6 shadow-md hover:scale-105 transition text-sm">
            {{ t('event.addEvent') }}
          </InertiaLink>
          <InertiaLink href="/profile" class="bg-brand-light text-white rounded-full py-2 px-6 shadow-md hover:scale-105 transition text-sm">
            {{ t('auth.profile') }}
          </InertiaLink>
          <button class="text-sm text-gray-600 hover:underline" @click.prevent="logout">
            {{ t('auth.logout') }}
          </button>
        </template>
        <LanguageSwitcher />
      </div>

      <div class="flex md:hidden items-center gap-3">
        <template v-if="!isLoggedIn">
          <InertiaLink href="/login" class="hover:underline text-sm">{{ t('auth.login') }}</InertiaLink>
          <InertiaLink href="/register" class="bg-black text-white rounded-full py-2 px-4 text-sm">{{ t('auth.register') }}</InertiaLink>
        </template>
        <template v-else>
          <InertiaLink href="/profile" class="bg-brand-light text-white rounded-full py-2 px-4 text-sm">{{ t('auth.profile') }}</InertiaLink>
          <InertiaLink href="/event/create" class="bg-black text-white rounded-full py-2 px-4 text-sm">{{ t('event.addEvent') }}</InertiaLink>
          <button class="bg-black text-white rounded-full py-2 px-4 text-sm" @click.prevent="logout">{{ t('auth.logout') }}</button>
        </template>
        <LanguageSwitcher />
      </div>
    </div>

    <div class="flex flex-col space-y-6 items-center justify-center text-center mb-6">
      <template v-if="isHomePage">
        <img src="/images/LogoBrand.png" alt="LetsEvent">
      </template>
      <template v-else>
        <InertiaLink href="/">
          <img src="/images/LogoBrand.png" alt="LetsEvent" class="hover:opacity-80 transition-opacity">
        </InertiaLink>
      </template>

      <slot />
    </div>
  </div>
</template>
