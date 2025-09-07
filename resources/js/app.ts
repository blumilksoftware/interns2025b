import '../css/app.css'
import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import Layout from '@/Layouts/Layout.vue'
import AppHead from '@/Components/AppHead.vue'
import 'leaflet/dist/leaflet.css'
import { Icon } from 'leaflet'
import { createI18n } from 'vue-i18n'
import en from '@/Locales/en.json'
import pl from '@/Locales/pl.json'
import axios from 'axios'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

const savedLocale = localStorage.getItem('locale') ?? 'en'
axios.defaults.headers.common['Accept-Language'] = savedLocale

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
    const initialLocale = savedLocale || (props.initialPage.props.locale as string) || 'en'

    const i18n = createI18n({
      legacy: false,
      globalInjection: true,
      locale: initialLocale,
      fallbackLocale: 'en',
      messages: { en, pl },
    })

    createApp({ render: () => h(App, props) })
      .component('AppHead', AppHead)
      .component('InertiaLink', Link)
      .use(plugin)
      .use(i18n)
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
