const { createApp, ref } = Vue
import App from '/js/vendor/authorize/profile.js'
import Validation from '/js/vuebootstrap/validation.js'
import Validate from '/js/vuebootstrap/validate.js'

const app = createApp(App)

app.component("Validation", Validation);
app.component("Validate", Validate);
app.mount("#app");