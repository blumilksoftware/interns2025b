<template>
  <div class="w-full">
    <div
      class="flex font-medium  items-center content-center md:justify-end justify-between gap-y-5 pt-10 sm:gap-x-10 mx-5 mb-10"
    >
      <InertiaLink href="/login">zaloguj się</InertiaLink><br>
      <InertiaLink
        href="/register"
        class="bg-black text-white rounded-full shadow-shadow-blue hover:scale-105 shadow-sm py-2 px-8"
      >
        zarejestruj się
      </InertiaLink>
    </div>
    <div
      class="flex flex-col space-y-6 items-center justify-center text-center mb-6"
    >
      <img src="/images/LogoBrand.png" alt="LetsEvent" class="">
      <slot />
    </div>
  </div>
</template>
