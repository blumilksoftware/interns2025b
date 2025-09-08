<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const dropdownOpen = ref(false)
type LanguageCode = 'en' | 'pl'

const { locale } = useI18n()
const selected = ref<LanguageCode>(locale.value as LanguageCode)

const languageMap: Record<LanguageCode, { name: string, flag: string }> = {
  en: { name: 'English', flag: 'https://flagcdn.com/w40/us.png' },
  pl: { name: 'Polski',  flag: 'https://flagcdn.com/w40/pl.png' },
}

const availableLocales: LanguageCode[] = ['en', 'pl']

const languageOptions = computed(() =>
  availableLocales.map(l => ({ code: l, name: languageMap[l].name, flag: languageMap[l].flag })),
)

const currentLanguage = computed(() => languageMap[selected.value])

watch(selected, (val) => {
  locale.value = val
  sessionStorage.setItem('locale', val)
})

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value
}

function selectLocale(l: LanguageCode) {
  selected.value = l
  dropdownOpen.value = false
}

const root = ref<HTMLElement | null>(null)

function onDocumentClick(e: MouseEvent) {
  if (!root.value) return
  const target = e.target as Node
  if (!root.value.contains(target)) dropdownOpen.value = false
}

function onKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape') dropdownOpen.value = false
}

onMounted(() => {
  document.addEventListener('click', onDocumentClick)
  document.addEventListener('keydown', onKeydown)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', onDocumentClick)
  document.removeEventListener('keydown', onKeydown)
})
</script>

<template>
  <div ref="root" class="relative inline-block text-left">
    <button
      class="flex items-center border rounded px-3 py-2 text-sm min-w-28 bg-white shadow"
      :aria-expanded="dropdownOpen"
      aria-haspopup="listbox"
      type="button"
      @click="toggleDropdown"
    >
      <img :src="currentLanguage.flag" alt="" class="w-5 h-4 mr-2 rounded-sm">
      {{ currentLanguage.name }}
    </button>

    <div v-if="dropdownOpen" class="absolute z-50 mt-2 w-40 text-sm bg-white border rounded shadow" role="listbox">
      <ul>
        <li
          v-for="opt in languageOptions"
          :key="opt.code"
          class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer"
          role="option"
          :aria-selected="selected === opt.code"
          @click="selectLocale(opt.code)"
        >
          <img :src="opt.flag" alt="" class="w-5 h-4 mr-2 rounded-sm">
          {{ opt.name }}
        </li>
      </ul>
    </div>
  </div>
</template>
