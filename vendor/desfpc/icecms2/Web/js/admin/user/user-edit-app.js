const { createApp } = Vue
import App from '/js/vendor/admin/user/user-edit.js'
import Aform from '/js/vuebootstrap/aform.js'

const app = createApp(App)

app.component("Aform", Aform);

app.mount("#app");