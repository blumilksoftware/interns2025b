<template>
  <label
    class="block text-base text-gray-500 mb-2"
    :for="id"
  >
    {{ label }}
  </label>
  <input
    :id="id"
    :name="name"
    :type="type"
    :placeholder="placeholder"
    :value="modelValue"
    class="w-full h-12 px-4 font-semibold text-[#025F60] border-2 rounded-lg focus:outline-none"
    @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
  >
</template>

<script lang="ts" setup>
import { defineProps, defineEmits } from 'vue'

type InputType = 'text' | 'email' | 'password' | 'number' | 'search'

const props = defineProps<{
  modelValue: string
  id: string
  name: string
  label: string
  type?: InputType
  placeholder?: string
}>()

const emit = defineEmits<(e: 'update:modelValue', value: string) => void>()

</script>
