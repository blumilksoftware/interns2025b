<script setup lang="ts">

type InputType = 'text' | 'email' | 'password' | 'number' | 'search';

const model = defineModel<string>()

defineProps<{
  label?: string
  type?: InputType
  name: string
  id: string
  placeholder?: string
  error?: string | null
}>()
</script>

<template>
  <label class="block text-base text-gray-500 mb-1" :for="id">
    {{ label }}
  </label>
  <div class="relative">
    <input
      :id="id"
      v-model="model"
      :name="name"
      :type="type"
      :placeholder="placeholder"
      :value="modelValue"
      :class="[
        'w-full h-12 px-4 pr-10 font-medium rounded-lg focus:outline-none text-lightBrand hover:bg-gray-100 focus:bg-gray-100 transition duration-100 ease-in-out',
        error ? 'border border-red-500' : 'border border-lightBrand',
      ]"
    >
    <slot name="append" />
  </div>
  <small v-if="error" class="text-red-600 text-sm">
    {{ error }}
  </small>
</template>
