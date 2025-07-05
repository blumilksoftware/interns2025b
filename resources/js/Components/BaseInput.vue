<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

type InputType = 'text' | 'email' | 'password' | 'number' | 'search';

defineProps<{
  modelValue: string
  label?: string
  type?: InputType
  name: string
  id: string
  placeholder?: string
}>()

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<template>
  <label class="block text-base text-gray-500 mb-1" :for="id">
    {{ label }}
  </label>
  <div class="relative">
    <input
      :id="id"
      :name="name"
      :type="type"
      :placeholder="placeholder"
      :value="modelValue"
      class="w-full h-12 px-4 pr-10 font-medium text-[#025F60] border-2 rounded-lg focus:outline-none"
      @input="
        $emit('update:modelValue', ($event.target as HTMLInputElement).value)
      "
    >
    <slot name="append" />
  </div>
</template>
