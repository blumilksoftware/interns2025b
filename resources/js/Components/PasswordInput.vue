<script setup lang="ts">
import { computed } from 'vue'
import BaseInput from '@/Components/BaseInput.vue'
import { EyeIcon as EyeIconOutline } from '@heroicons/vue/24/outline'
import { EyeIcon as EyeIconSolid } from '@heroicons/vue/24/solid'

import {
  showPasswords,
  togglePasswords,
} from '@/composables/usePasswordVisibility'

type InputType = 'password';

const props = defineProps<{
  modelValue: string
  label?: string
  name: string
  id: string
  placeholder?: string
  type?: InputType
}>()

const emit = defineEmits<(e: 'update:modelValue', v: string) => void>()

const modelValue = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})
</script>

<template>
  <BaseInput
    :id="id"
    v-model="modelValue"
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
