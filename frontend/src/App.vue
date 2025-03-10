<template>
  <div class="app-container">
    <header class="header-container">
      <div class="logo-container">
        <img src="./assets/logo.png" alt="Logo Concesionario" class="logo" />
        <h1>Concesionario de Vehículos</h1>
      </div>
      <nav class="main-nav">
        <router-link to="/" class="nav-link">Inicio</router-link>
        <router-link to="/vehiculos" class="nav-link">Vehículos</router-link>
        <router-link to="/contacto" class="nav-link">Contacto</router-link>
      </nav>
    </header>
    
    <main>
      <router-view />
    </main>

    <footer class="footer-container">
      <p>&copy; 2025 Concesionario de Vehículos. Todos los derechos reservados.</p>
    </footer>
  </div>
</template>

<script setup>
// No se necesita lógica adicional por ahora
</script>

<style>
:root {
  --primary-color: #0056b3;
  --secondary-color: #f8f9fa;
  --text-color: #333;
  --accent-color: #e63946;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Arial', sans-serif;
  color: var(--text-color);
  line-height: 1.6;
}

.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.header-container {
  background-color: var(--primary-color);
  color: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo-container {
  display: flex;
  align-items: center;
}

.logo {
  height: 40px;
  margin-right: 1rem;
}

.main-nav {
  display: flex;
  gap: 1.5rem;
}

.nav-link {
  color: white;
  text-decoration: none;
  padding: 0.5rem;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.nav-link:hover, .router-link-active {
  background-color: rgba(255, 255, 255, 0.2);
}

main {
  flex: 1;
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.footer-container {
  background-color: var(--secondary-color);
  padding: 1.5rem;
  text-align: center;
  margin-top: auto;
  border-top: 1px solid #ddd;
}

@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    padding: 1rem;
  }
  
  .logo-container {
    margin-bottom: 1rem;
  }
  
  .main-nav {
    width: 100%;
    justify-content: center;
  }
}
</style>