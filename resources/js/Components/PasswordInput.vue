<script setup lang="ts">
import BaseInput from '@/Components/BaseInput.vue'
import { EyeIcon as EyeIconOutline } from '@heroicons/vue/24/outline'
import { EyeIcon as EyeIconSolid } from '@heroicons/vue/24/solid'

import { useTogglePassword } from '@/composables/useTogglePassword'
const { showPasswords, togglePasswords } = useTogglePassword()

const model = defineModel<string>()

defineProps<{
  label?: string
  name: string
  id: string
  placeholder?: string
  type?: 'password'
}>()
</script>

<template>
  <BaseInput
    :id="id"
    v-model="model"
    :name="name"
    :label="label"
    :placeholder="placeholder"
    :type="showPasswords ? 'text' : 'password'"
  >
    <template #append>
      <button
        type="button"
        class="absolute right-2 top-1/2 -translate-y-1/2"
        :aria-label="showPasswords ? 'Hide password' : 'Show password'"
        @click="togglePasswords"
      >
        <component
          :is="showPasswords ? EyeIconSolid : EyeIconOutline"
          class="size-5"
        />
      </button>
    </template>
  </BaseInput>
</template>
