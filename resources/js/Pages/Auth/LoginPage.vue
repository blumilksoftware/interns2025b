<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'

</script>

<template>
  <AuthLayout>
    <template #auth>
      <div class=" flex-auto bg-gray-50 min-w-[300px] max-w-screen-md rounded-lg">
        <div class="[&>*]:ml-9 pb-20 text-xl text-white bg-gradient-to-tr from-[#025F60] to-[#093637] rounded-lg">
          <h2 class=" font-bold">Zaloguj się</h2>
          <p class=" font-semibold">Nie posidasz konta? <a href="" class="text-white font-bold"><span class="underline font-bold align">Zarejestruj się</span></a></p>
        </div>
        <form class="[&>*]:ml-9 text-xl mt-4" action="#" method="post">
          <p class="text-base text-gray-500">Email</p>
          <input class=" font-extrabold bg-gradient-to-tr from-[#025F60] to-[#025F60] bg-clip-text text-transparent border rounded-md">
          <p class="text-base text-gray-500">Hasło</p>
          <input class=" font-extrabold bg-gradient-to-tr from-[#025F60] to-[#025F60] bg-clip-text text-transparent border rounded-md"><br>
          <input id="remember_password" type="checkbox" name="remember_password">
          <label for="remember_password">Zapamiętaj mnie</label>
          <p>zapomniałeś hasła?</p>
          <br>
          <button class="bg-black rounded-lg w-1/2 h-8 text-white" type="submit">Zaloguj się</button>
        </form>
        <div>
          <p>Zaloguj się za pomocą Facebook</p>
        </div>
      </div>
    </template>
  </AuthLayout>
</template>
