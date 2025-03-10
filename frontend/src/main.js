import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import axios from 'axios'

// Importar componentes de página
import LandingPage from './views/LandingPage.vue'
import VehiculosPage from './views/VehiculosPage.vue'
import ContactoPage from './views/ContactoPage.vue'

// Configurar axios
axios.defaults.baseURL = import.meta.env.VITE_API_URL || ''

// Configurar rutas
const routes = [
  { path: '/', component: LandingPage },
  { path: '/vehiculos', component: VehiculosPage },
  { path: '/contacto', component: ContactoPage },
  // Ruta comodín para 404
  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Crear la aplicación Vue
const app = createApp(App)

// Usar el router
app.use(router)

// Proporcionar axios globalmente
app.provide('axios', axios)

// Montar la aplicación
app.mount('#app')