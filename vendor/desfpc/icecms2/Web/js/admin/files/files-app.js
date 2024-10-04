const { createApp } = Vue
import App from '/js/vendor/admin/files/files.js'
import Atable from '/js/vuebootstrap/atable.js'

const app = createApp(App)

app.component("Atable", Atable);

app.mount("#app");