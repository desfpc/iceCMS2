const { createApp } = Vue
import App from '/js/vendor/admin/user/user.js'
import Atable from '/js/vuebootstrap/atable.js'

const app = createApp(App)

app.component("Atable", Atable);

app.mount("#app");