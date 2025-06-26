<script lang="ts" setup>
import AppHead from '@/Components/AppHead.vue'
import LoginFacebook from '@/Components/LoginFacebook.vue'

</script>

<template>
  <AppHead title="Dashboard" description="This is the dashboard page" />
  <div class="flex flex-col items-center justify-center bg-gray-50 [min-width:448px]:h-screen ">
    <div class="w-full max-w-md">
      <div class="flex justify-center items-center bg-gray-100">
        <div class="flex-auto min-w-[300px] pb-9 max-w-screen-md bg-gray-50 shadow-xl rounded-lg [max-width:448px]:rounded-b-lg">
          <div class="px-9 py-20 bg-gradient-to-tr from-[#025F60] to-[#093637] rounded-lg text-white text-xm [&>*]:mb-4">
            <img class="mb-10" src="public/images/LogoWhite2.png" alt="LetsEvent">
            <h2 class="font-bold text-3xl">Zaloguj się</h2>
            <p class="font-semibold">
              Nie posiadasz konta?
              <a href="/register" class="underline font-bold">Zarejestruj się</a>
            </p>
          </div>

          <slot />
          <div class="flex flex-col items-center justify-center w-full mt-6 space-y-6 text-xl">
            <div class="flex items-center w-5/6 mt-8">
              <div class="grow h-px bg-gray-200" />
              <span class="px-4 text-gray-500 text-sm">lub</span>
              <div class="grow h-px bg-gray-200" />
            </div>
            <login-facebook />
            <div class="w-5/6">
              <div class="text-center">
                <p class="text-base text-gray-500">
                  Rejestrując się wyrażasz zgodę na
                  <span class="text-[#025F60] font-bold ">Warunki świadczenia usług</span> oraz
                  <span class="text-[#025F60] font-bold ">Umowę o przetwarzaniu danych</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

