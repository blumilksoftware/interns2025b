
<template>
  <div>
    <div class="w-full bg-white rounded-lg shadow-lg pb-5">
      <div
        class="w-full place-items-center flex justify-center py-12 bg-gradient-to-tr from-brand to-brand-light rounded-lg text-white"
      >
        <div class="w-5/6">
          <img class="mb-10" src="/images/LogoWhite2.png" alt="LetsEvent">
          <slot name="header" />
        </div>
      </div>

      <slot name="form" />

      <div class="flex flex-col items-center justify-center w-full text-xl">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>
