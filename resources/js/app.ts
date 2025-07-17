import '../css/app.css'
import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import Layout from '@/Layouts/Layout.vue'
import AppHead from '@/Components/AppHead.vue'
import 'leaflet/dist/leaflet.css'
import { Icon } from 'leaflet'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: async (name) => {
    const page = await resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
    )
    page.default.layout = page.default.layout || Layout
    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .component('AppHead', AppHead)
      .component('InertiaLink', Link)
      .use(plugin)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})

delete (Icon.Default.prototype as any)._getIconUrl
Icon.Default.mergeOptions({
  iconRetinaUrl:   new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
  iconUrl:         new URL('leaflet/dist/images/marker-icon.png',   import.meta.url).href,
  shadowUrl:       new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
})
