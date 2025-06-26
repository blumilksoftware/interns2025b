<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import {reactive} from 'vue'

defineOptions({
  layout: AuthLayout,
})

//const form = reactive({
//  email:    '',
//  password: '',
//})

const form = useForm({
  email:    '',
  password: '',
})

function Submit() {
  console.log(form)
}
</script>

<template>
  <form class="flex flex-col items-center justify-center w-full mt-6 space-y-6 text-xl"
        @submit.prevent="Submit"
  >
    <div class="w-5/6">
      <BaseInput
        id="email"
        v-model="form.email"
        name="email"
        label="Email"
        type="email"
      />
      <small v-if="form.errors.email" class="text-red-600">
        {{ form.errors.password }}
      </small>
      <BaseInput
        id="password"
        v-model="form.password"
        name="password"
        label="Hasło"
        type="password"
      />
      <small v-if="form.errors.password" class="text-red-600">
        {{ form.errors.password }}
      </small>
    </div>
    <div class="flex items-center justify-between w-5/6">
      <label class="flex items-center">
        <input id="remember_password" name="remember_password" type="checkbox" class="mr-2">
        <span class="text-base text-gray-700">Zapamiętaj mnie</span>
      </label>
      <a href="#" class="font-bold text-base text-[#025F60]">Zapomniałeś hasła?</a>
    </div>
    <BaseButton class="w-5/6" :disabled="form.processing" type="submit">
      Zaloguj się
    </BaseButton>
  </form>
</template>
