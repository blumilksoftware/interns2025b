<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import BaseInput from '@/Components/BaseInput.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { ref } from 'vue'


const email = ref('')
const password = ref('')
defineOptions({
  layout: AuthLayout,
})
</script>

<template>
  <form action="#" method="post"
        class="flex flex-col items-center justify-center w-full mt-6 space-y-6 text-xl"
  >
    <div class="w-5/6">
      <BaseInput
        id="email"
        v-model="email"
        name="email"
        label="Email"
        type="email"
      />
      <BaseInput
        id="password"
        v-model="password"
        name="password"
        label="Hasło"
        type="password"
      />
      <BaseInput
        id="password"
        v-model="password"
        name="password"
        label="Powtórz Hasło"
        type="password"
      />
    </div>
    <div class="flex items-center justify-between w-5/6">
      <label class="flex items-center">
        <input id="remember_password" name="remember_password" type="checkbox" class="mr-2">
        <span class="text-base text-gray-700">Zapamiętaj mnie</span>
      </label>
      <a href="/rememberpassword" class="font-bold text-base text-[#025F60]">Zapomniałeś hasła?</a>
    </div>
    <BaseButton class="w-5/6" type="submit" @click="Submit">
      Zarejestruj się
    </BaseButton>
  </form>
</template>
