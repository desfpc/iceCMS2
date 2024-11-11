const { createApp } = Vue
import App from '/js/vendor/admin/files/files-edit.js'
import Validation from '/js/vuebootstrap/validation.js'
import Aform from '/js/vuebootstrap/aform.js'

const app = createApp(App)

app.use(Validation, {});
app.component("Aform", Aform);

app.mount("#app");