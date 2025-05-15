const { createApp } = Vue
import App from '/js/vendor/authorize/profile.js'
import Validation from '/js/vuebootstrap/validation.js'
import Validate from '/js/vuebootstrap/validate.js'
import Tabs from '/js/vuebootstrap/tabs.js'
import CheckboxGroup from '/js/vuebootstrap/checkbox-group.js'
import ButtonLogin from '/js/vuetelega/button-login.js'

//const loginWithTelegram = async (user) => {}
const app = createApp(App)

app.use(Validation, {});
app.component("Validate", Validate);
app.component("Tabs", Tabs);
app.component("CheckboxGroup", CheckboxGroup);
app.component("ButtonLogin", ButtonLogin);

app.mount("#app");