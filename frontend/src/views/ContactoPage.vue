<template>
  <div class="contacto-page">
    <h1>Contacto</h1>
    
    <div class="contacto-container">
      <div class="formulario-contacto">
        <h2>Envíanos un mensaje</h2>
        <form @submit.prevent="enviarMensaje">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" v-model="formulario.nombre" required>
          </div>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" v-model="formulario.email" required>
          </div>
          
          <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" v-model="formulario.telefono">
          </div>
          
          <div class="form-group">
            <label for="asunto">Asunto</label>
            <input type="text" id="asunto" v-model="formulario.asunto" required>
          </div>
          
          <div class="form-group">
            <label for="mensaje">Mensaje</label>
            <textarea id="mensaje" v-model="formulario.mensaje" rows="5" required></textarea>
          </div>
          
          <button type="submit" class="enviar-btn">Enviar mensaje</button>
        </form>
      </div>
      
      <div class="info-contacto">
        <h2>Información de contacto</h2>
        
        <div class="contacto-info-item">
          <i class="icon">📍</i>
          <div>
            <h3>Dirección</h3>
            <p>Calle Ejemplo, 123</p>
            <p>28001, Madrid</p>
          </div>
        </div>
        
        <div class="contacto-info-item">
          <i class="icon">📞</i>
          <div>
            <h3>Teléfono</h3>
            <p>+34 91 123 45 67</p>
          </div>
        </div>
        
        <div class="contacto-info-item">
          <i class="icon">✉️</i>
          <div>
            <h3>Email</h3>
            <p>info@concesionario.com</p>
          </div>
        </div>
        
        <div class="contacto-info-item">
          <i class="icon">🕒</i>
          <div>
            <h3>Horario</h3>
            <p>Lunes a Viernes: 9:00 - 20:00</p>
            <p>Sábados: 10:00 - 14:00</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'

const formulario = reactive({
  nombre: '',
  email: '',
  telefono: '',
  asunto: '',
  mensaje: ''
})

const enviarMensaje = () => {
  alert('Mensaje enviado correctamente. Te contactaremos pronto.')
  
  // Reiniciar formulario
  formulario.nombre = ''
  formulario.email = ''
  formulario.telefono = ''
  formulario.asunto = ''
  formulario.mensaje = ''
}
</script>

<style scoped>
.contacto-page h1 {
  margin-bottom: 2rem;
  color: var(--primary-color);
}

.contacto-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.formulario-contacto, .info-contacto {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.formulario-contacto h2, .info-contacto h2 {
  margin-bottom: 1.5rem;
  color: var(--primary-color);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.form-group input, .form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.enviar-btn {
  background-color: var(--primary-color);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.enviar-btn:hover {
  background-color: #004494;
}

.contacto-info-item {
  display: flex;
  margin-bottom: 1.5rem;
}

.icon {
  font-size: 1.5rem;
  margin-right: 1rem;
  min-width: 40px;
  text-align: center;
}

.contacto-info-item h3 {
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

@media (max-width: 768px) {
  .contacto-container {
    grid-template-columns: 1fr;
  }
  
  .info-contacto {
    order: -1;
  }
}
</style>