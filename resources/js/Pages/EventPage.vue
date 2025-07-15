<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3'
</script>

<template>
  <div class="w-full flex flex-col sm:items-center sm:justify-center">
    <div class="flex w-full pb-32">
      <div class="">
        <div class="w-screen flex font-medium justify-end gap-10 p-10">
          <InertiaLink href="/login">zaloguj się</InertiaLink><br>
          <InertiaLink href="/register">zarejestruj się</InertiaLink>
        </div>
        <div
          class="flex flex-col items-center justify-center text-center space-y-16"
        >
          <img src="/images/LogoBrand.png" alt="LetsEvent" class="">
        </div>
        <div class="w-full h-[536px] bg-red-200">
          <img src="/images/#" alt="EventBanner">
        </div>
        <div class="flex flex-col justify-center">
          <div class="w-5/6 h-[200px] bg-white">
            <div class="space-y-2">
              <h2 class="text-2xl font-bold">data</h2>
              <h1 class="text-2xl font-bold">title</h1>
              <h3 class="text-2xl font-bold">place</h3>
              <h4 class="text-2xl font-bold">restrictons</h4>
            </div>
          </div>
          <div class="flex w-5/6 h-[200px] bg-green-300 justify-center">
            <div class="flex space-x-6">
              <div class="bg-emerald-400">
                <h1>1</h1>
              </div>
              <div class="bg-fuchsia-400">
                <h1>2</h1>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div />
    </div>
  </div>
</template>
