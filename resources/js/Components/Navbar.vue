<template>
  <div class="w-full">
    <div class="flex font-medium items-center content-center justify-end gap-x-10 p-10 ">
      <InertiaLink href="/login">zaloguj się</InertiaLink><br>
      <InertiaLink href="/register" class="bg-black text-white rounded-full shadow-shadow-blue hover:scale-105 shadow-sm p-3">zarejestruj się</InertiaLink>
    </div>
    <div
      class="flex flex-col space-y-6 items-center justify-center text-center mb-6"
    >
      <img src="/images/LogoBrand.png" alt="LetsEvent" class="">
      <slot />
    </div>
  </div>
</template>
