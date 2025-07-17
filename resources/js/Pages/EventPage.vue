<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue'
import Navbar from '@/Components/Navbar.vue'
import Map from '@/Components/Map.vue'
import InfoBlock from '@/Components/InfoBlock.vue'

const bannerSrc = 'https://picsum.photos/100/100'
const eventDate = '26 maja 2025 – 12:00'
const eventTitle = 'Taylor Swift – City of Lover'
const venueName = 'Stadion Narodowy'
const venueAddress = 'al. Księcia Józefa Poniatowskiego 1, 03-901 Warszawa'
const isPaid = true
</script>

<template>
  <AppHead title="Event" />
  <div class="w-full mb-12">
    <Navbar class="mb-[72px]" />

    <div class="w-full h-[400px]  bg-red-200">
      <img :src="bannerSrc" alt="Event Banner" class="size-full object-cover">
    </div>

    <div class="flex flex-col items-center">
      <div class="w-full py-16 bg-white shadow-lg border border-gray-300 flex justify-center items-center">
        <div class="w-11/12 space-y-2">
          <div class="flex justify-between items-center w-full text-center">
            <h2 class="text-2xl text-brand-light font-semibold">{{ eventDate }}</h2>
            <p v-if="isPaid" class="bg-[#0A84FF33] px-8 py-2 rounded-2xl font-semibold text-blue-600">Płatny</p>
          </div>
          <h1 class="text-5xl font-bold">{{ eventTitle }}</h1>
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-2xl font-medium">{{ venueName }}</h3>
              <h4 class="text-lg text-gray-400 font-normal">Brak ograniczenia wiekowego</h4>
            </div>
            <div>
              <div class="flex justify-end font-semibold text-xl px-5 py-2 space-x-3">
                <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Zainteresowany(a)</button>
                <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Wezmę udział</button>
                <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Zaproś</button>
                <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">-&gt;</button>
                <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">...</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex w-11/12 mt-6">
        <div class="flex w-full space-x-6">
          <div class="flex flex-col rounded-lg shadow-lg bg-white py-16 px-32 w-4/6">
            <div class="flex flex-col gap-y-8 w-full">
              <InfoBlock
                title="12056 osób weźmie udział"
              />
              <InfoBlock
                title="26 maja 2025"
                line2="Godzina 12:00"
              />
              <InfoBlock
                title="Stadion Narodowy"
                line1="al. Księcia Józefa Poniatowskiego 1"
                line2="03‑901 Warszawa"
              />
              <div class="w-full flex justify-between">
                <InfoBlock
                  title="Piotr Nowak"
                  line1="Organiztor"
                />
                <div class="flex items-center">
                  <button class="bg-brand/10 text-brand px-3 py-1 rounded-xl">Obserwuj</button>
                </div>
              </div>
              <h1 class="font-medium text-3xl text-#120D26">Informacje</h1>
              <p class="font-normal text-xl text-[#120D26]">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur facilisis accumsan nisi non semper. Vivamus ut egestas nibh, non vehicula felis. Aenean mollis porta ultricies. Duis fermentum pulvinar diam ac rutrum. Fusce sit amet feugiat purus, non imperdiet nibh. Maecenas lacinia ipsum at leo ultricies maximus. Cras sit amet bibendum mauris, ac dictum libero.

                Vivamus convallis lectus in nulla mollis aliquam eu eu ligula. Nam vehicula orci id egestas sagittis. Pellentesque quis varius mi. Nullam euismod congue tortor congue tempus. Sed id tortor quis ante placerat suscipit ut sit amet orci. Proin porta odio et nisi fringilla mollis. Quisque id enim eu libero dignissim bibendum. Suspendisse mi eros, varius in malesuada sed, ultrices ac nulla. Vivamus urna neque, semper sit amet elit vitae, ultricies volutpat nunc. Phasellus lacinia rutrum ipsum, nec tempus orci porta eget. Maecenas sollicitudin vel sapien quis ornare. Fusce pulvinar ultrices interdum. Aenean porttitor congue fringilla. Cras sed fringilla orci, sit amet facilisis lectus. Nullam at nisl sit amet orci rutrum tincidunt sit amet ac libero.

                Vivamus eu ex id nunc faucibus blandit quis sed nisi. Ut nec elit mauris. Aliquam id dapibus quam. Fusce vehicula efficitur sapien, id convallis lorem convallis non. Etiam tincidunt pellentesque leo vitae ullamcorper. Fusce varius quam sed aliquet faucibus. Quisque ac ullamcorper sem, sed varius augue. Morbi dictum tortor nec turpis dignissim, a finibus nibh facilisis. Nulla vel varius neque. Nullam arcu velit, pharetra in mattis vitae, fermentum condimentum tortor. Nullam felis arcu, placerat at tempor nec, scelerisque quis lorem. Phasellus bibendum cursus nunc, nec cursus risus suscipit id. Nullam erat ex, aliquam at venenatis eget, tincidunt ut odio.

                Donec efficitur finibus augue. Quisque id quam dui. Maecenas eros eros, sagittis ac ultricies a, malesuada ac orci. Donec in metus ligula. Praesent ultrices a magna a sagittis. Curabitur mollis sollicitudin dignissim. Pellentesque gravida, elit a rutrum commodo, enim ex fermentum nisi, commodo congue massa mauris sed sapien. Proin gravida aliquet metus eu gravida. In ultrices rhoncus mattis. Praesent elementum pellentesque libero id varius.

                Sed ornare ex turpis, vehicula semper nulla sodales eu. In hac habitasse platea dictumst. Donec magna eros, euismod sit amet metus nec, consequat laoreet sapien. Pellentesque ac orci feugiat, placerat purus pretium, maximus ex. Maecenas a magna quis lacus pellentesque tincidunt. Nam rutrum turpis a convallis consequat. Praesent vitae enim nulla. Donec a dapibus risus. Vestibulum mauris nunc, dignissim non varius vitae, pretium vel velit. Donec eu lorem sed lacus faucibus bibendum.
              </p>
            </div>
          </div>

          <div class="flex flex-col space-y-6 w-2/6 justify-start">
            <div class="rounded-lg shadow-lg bg-white py-11 px-16 space-y-8">
              <p class="text-2xl font-medium">Bilety</p>
              <div class="text-center">
                <p class="font-medium text-base p-[15px] bg-brand-dark text-white rounded-2xl">KUP BILET</p>
              </div>
            </div>

            <div class="rounded-lg shadow-lg bg-white space-y-6 py-11 px-16 overflow-visible">
              <div class="-mx-16 -mt-11 bg-red-200 h-96 rounded-2xl">
                <Map />
              </div>
              <div class="w-10/12 content-center">
                <p class="font-medium text-3xl">{{ venueName }}</p>
                <p class="font-normal text-gray-400 text-xl">{{ venueAddress }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
