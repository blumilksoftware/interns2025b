<script setup lang="ts">
defineSlots<{
  header: () => any
  form: () => any
  footer: () => any
}>()
</script>

<template>
  <div class="place-items-center flex justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white pb-8 rounded-lg shadow-lg">
      <div
        class="px-9 py-20 bg-gradient-to-tr from-[#025F60] to-[#093637] rounded-lg text-white [&>*]:mb-4"
      >
        <img class="mb-10" src="/images/LogoWhite2.png" alt="LetsEvent">
        <slot name="header" />
      </div>

      <div>
        <slot name="form" />
      </div>
      <div
        class="flex flex-col items-center justify-center w-full mt-6 text-xl"
      >
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>
