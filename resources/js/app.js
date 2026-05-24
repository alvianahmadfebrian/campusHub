import '../css/app.css'
import '../css/admin.css' 
import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'

createInertiaApp({
    title: (title) => title ? `${title} - CampusHub` : 'CampusHub',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('Link', Link)
            .mount(el)
    },
})
