const { createApp } = Vue
import App from '/js/vendor/admin/user/user.js'
import Validation from '/js/vuebootstrap/validation.js'
import Validate from '/js/vuebootstrap/validate.js'
import Tabs from '/js/vuebootstrap/tabs.js'

const app = createApp(App)

app.use(Validation, {});
app.component("Validate", Validate);
app.component("Tabs", Tabs);

app.mount("#app");