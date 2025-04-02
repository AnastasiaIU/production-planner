import './assets/css/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'

const app = createApp(App)

// Initialize auth token if it exists
const token = getAuthToken();
if (token) {
  setAuthToken(token);
}

app.use(router)
app.use(createPinia())

app.mount('#app')
