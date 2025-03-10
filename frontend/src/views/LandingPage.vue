<template>
  <div class="landing-page">
    <section class="hero-section">
      <div class="hero-content">
        <h1>Encuentra tu próximo vehículo</h1>
        <p>Descubre nuestra selección de vehículos de calidad a precios competitivos</p>
        <router-link to="/vehiculos" class="cta-button">Ver vehículos</router-link>
      </div>
    </section>

    <section class="destacados-section">
      <h2>Vehículos destacados</h2>
      <div v-if="loading" class="loading">
        <p>Cargando vehículos destacados...</p>
      </div>
      <div v-else-if="error" class="error-message">
        <p>{{ error }}</p>
      </div>
      <div v-else class="vehiculos-grid">
        <div v-for="vehiculo in vehiculosDestacados" :key="vehiculo.id" class="vehiculo-card">
          <img :src="vehiculo.imagen || 'https://via.placeholder.com/300x200'" :alt="vehiculo.marca + ' ' + vehiculo.modelo" class="vehiculo-imagen">
          <div class="vehiculo-info">
            <h3>{{ vehiculo.marca }} {{ vehiculo.modelo }}</h3>
            <p class="precio">{{ formatPrecio(vehiculo.precio) }}</p>
            <div class="detalles">
              <span>{{ vehiculo.anio }}</span>
              <span>{{ vehiculo.kilometros }} km</span>
              <span>{{ vehiculo.combustible }}</span>
            </div>
            <router-link :to="'/vehiculos/' + vehiculo.id" class="ver-detalles">Ver detalles</router-link>
          </div>
        </div>
      </div>
      <div class="ver-todos">
        <router-link to="/vehiculos" class="ver-todos-btn">Ver todos los vehículos</router-link>
      </div>
    </section>

    <section class="beneficios-section">
      <h2>Por qué elegirnos</h2>
      <div class="beneficios-grid">
        <div class="beneficio-card">
          <div class="icon">🚗</div>
          <h3>Vehículos de calidad</h3>
          <p>Todos nuestros vehículos pasan por un riguroso proceso de inspección</p>
        </div>
        <div class="beneficio-card">
          <div class="icon">💰</div>
          <h3>Precios competitivos</h3>
          <p>Ofrecemos los mejores precios del mercado para vehículos de segunda mano</p>
        </div>
        <div class="beneficio-card">
          <div class="icon">🔧</div>
          <h3>Garantía incluida</h3>
          <p>Todos nuestros vehículos incluyen garantía mecánica de mínimo 1 año</p>
        </div>
        <div class="beneficio-card">
          <div class="icon">👨‍💼</div>
          <h3>Asesoramiento personalizado</h3>
          <p>Te ayudamos a encontrar el vehículo que mejor se adapte a tus necesidades</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const vehiculosDestacados = ref([])
const loading = ref(true)
const error = ref(null)

// Formatear precio como moneda euro
const formatPrecio = (precio) => {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(precio)
}

// Cargar vehículos destacados al montar el componente
onMounted(async () => {
  try {
    console.log('Intentando cargar vehículos destacados...')
    // La URL debe apuntar al endpoint correspondiente en tu microservicio de vehículos
    const response = await axios.get('/api/vehiculos/destacados')
    console.log('Respuesta del servidor:', response.data)
    
    // Asegurarse de que la respuesta tenga el formato correcto
    if (Array.isArray(response.data)) {
      vehiculosDestacados.value = response.data
    } else if (response.data.data && Array.isArray(response.data.data)) {
      vehiculosDestacados.value = response.data.data
    } else {
      console.warn('El formato de la respuesta no es el esperado:', response.data)
      throw new Error('Formato de respuesta incorrecto')
    }
    
    loading.value = false
  } catch (err) {
    console.error('Error al cargar vehículos:', err)
    error.value = 'No se pudieron cargar los vehículos destacados. Por favor, inténtalo de nuevo más tarde.'
    loading.value = false
    
    // Datos de ejemplo en caso de error (por ahora usaremos esto hasta que el microservicio esté listo)
    vehiculosDestacados.value = [
      {
        id: 1,
        marca: 'Volkswagen',
        modelo: 'Golf',
        anio: 2020,
        precio: 18500,
        kilometros: 35000,
        combustible: 'Diesel',
        imagen: 'https://via.placeholder.com/300x200?text=Volkswagen+Golf'
      },
      {
        id: 2,
        marca: 'Toyota',
        modelo: 'Corolla',
        anio: 2021,
        precio: 20900,
        kilometros: 15000,
        combustible: 'Hybrid',
        imagen: 'https://via.placeholder.com/300x200?text=Toyota+Corolla'
      },
      {
        id: 3,
        marca: 'Ford',
        modelo: 'Focus',
        anio: 2019,
        precio: 14900,
        kilometros: 45000,
        combustible: 'Gasolina',
        imagen: 'https://via.placeholder.com/300x200?text=Ford+Focus'
      },
      {
        id: 4,
        marca: 'Renault',
        modelo: 'Clio',
        anio: 2020,
        precio: 13500,
        kilometros: 28000,
        combustible: 'Gasolina',
        imagen: 'https://via.placeholder.com/300x200?text=Renault+Clio'
      }
    ]
  }
})
</script>

<style scoped>
.landing-page {
  width: 100%;
}

.hero-section {
  background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://via.placeholder.com/1600x500');
  background-size: cover;
  background-position: center;
  color: white;
  text-align: center;
  padding: 4rem 2rem;
  margin-bottom: 3rem;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

.hero-content h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.hero-content p {
  font-size: 1.25rem;
  margin-bottom: 2rem;
}

.cta-button {
  display: inline-block;
  background-color: var(--accent-color);
  color: white;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: bold;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.cta-button:hover {
  background-color: #c92a35;
}

.destacados-section, .beneficios-section {
  padding: 2rem;
  margin-bottom: 3rem;
}

.destacados-section h2, .beneficios-section h2 {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 2rem;
  color: var(--primary-color);
}

.vehiculos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.vehiculo-card {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.vehiculo-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.vehiculo-imagen {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.vehiculo-info {
  padding: 1.5rem;
}

.vehiculo-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
}

.precio {
  color: var(--accent-color);
  font-weight: bold;
  font-size: 1.1rem;
  margin-bottom: 0.75rem;
}

.detalles {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  font-size: 0.9rem;
  color: #666;
}

.ver-detalles {
  display: inline-block;
  color: var(--primary-color);
  text-decoration: none;
  font-weight: bold;
}

.ver-detalles:hover {
  text-decoration: underline;
}

.ver-todos {
  text-align: center;
  margin-top: 2rem;
}

.ver-todos-btn {
  display: inline-block;
  background-color: var(--primary-color);
  color: white;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.ver-todos-btn:hover {
  background-color: #004494;
}

.loading, .error-message {
  text-align: center;
  padding: 2rem;
}

.error-message {
  color: #dc3545;
}

.beneficios-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
}

.beneficio-card {
  background-color: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.beneficio-card h3 {
  margin-bottom: 1rem;
  color: var(--primary-color);
}

@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2rem;
  }
  
  .hero-content p {
    font-size: 1rem;
  }
  
  .vehiculos-grid, .beneficios-grid {
    grid-template-columns: 1fr;
  }
}
</style>