<template>
  <div class="vehiculos-page">
    <h1>Catálogo de Vehículos</h1>
    
    <div class="vehiculos-grid">
      <div v-for="vehiculo in vehiculos" :key="vehiculo.id" class="vehiculo-card">
        <img :src="vehiculo.imagen" :alt="vehiculo.marca + ' ' + vehiculo.modelo" class="vehiculo-imagen">
        <div class="vehiculo-info">
          <h3>{{ vehiculo.marca }} {{ vehiculo.modelo }}</h3>
          <p class="precio">{{ formatPrecio(vehiculo.precio) }}</p>
          <div class="detalles">
            <span>{{ vehiculo.anio }}</span>
            <span>{{ vehiculo.kilometros }} km</span>
            <span>{{ vehiculo.combustible }}</span>
          </div>
          <a href="#" class="ver-detalles">Ver detalles</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const vehiculos = ref([
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
])

// Formatear precio como moneda euro
const formatPrecio = (precio) => {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(precio)
}
</script>

<style scoped>
.vehiculos-page h1 {
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
</style>