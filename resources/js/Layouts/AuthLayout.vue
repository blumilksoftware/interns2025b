<script lang="ts" setup>
defineSlots<{
  header: () => any
  form: () => any
  footer: () => any
}>()
</script>

<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white pb-8 rounded-lg shadow-lg">
      <div class="px-9 py-20 bg-gradient-to-tr from-[#025F60] to-[#093637] rounded-lg text-white text-xm [&>*]:mb-4">
        <img class="mb-10" src="/images/LogoWhite2.png" alt="LetsEvent">
        <slot name="header" />
      </div>

      <div>
        <slot name="form" />
      </div>
      <div class="flex flex-col items-center justify-center w-full mt-6  text-xl">
        <slot name="footer" />
        <div class="w-5/6">
          <div class="text-center">
            <p class="text-base text-gray-500 mt-6">
              Rejestrując się wyrażasz zgodę na
              <span class="text-[#025F60] font-bold ">Warunki świadczenia usług</span> oraz
              <span class="text-[#025F60] font-bold ">Umowę o przetwarzaniu danych</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
