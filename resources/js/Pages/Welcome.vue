<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
  <div class="welcome-container">
    <!-- En-tête avec le logo du ministère -->
    <header class="header">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo">
                    <img src="/images/men-logo.jpg" alt="Logo Ministère" class="logo-image" />
                </div>
                <div class="ministry-info">
                    <h1>Ministère de l'Éducation Nationale</h1>
                    <p>République de Madagascar</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Section principale -->
    <main class="main-content">
      <div class="hero-section">
        <div class="app-title">
          <h1>GES-ÉTUDIANT</h1>
          <p class="app-subtitle">Système de Gestion des Élèves</p>
        </div>

        <div class="description">
          <p>Application de gestion académique des élèves</p>
          <p>Gérez efficacement les informations des élèves, les inscriptions, les notes, les filières et les effectifs</p>
        </div>

        <!-- Boutons d'authentification -->
        <div class="auth-section">
          <div class="auth-buttons">
            <Link href="/login" class="btn btn-login">Se connecter</Link>
            <Link href="/register" class="btn btn-register">S'inscrire</Link>
          </div>
          <p class="auth-info">
            Accédez à votre espace personnel pour gérer vos informations académiques
          </p>
        </div>
      </div>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
      <div class="footer-content">
        <p>GES-ÉTUDIANT - Ministère de l'Éducation Nationale</p>
        <p>Tous droits réservés</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.welcome-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: 
    url('/images/men-profil.jpg') center/cover no-repeat;
  position: relative;
}

/* En-tête */
.header {
  background: linear-gradient(to right, rgba(199, 221, 197, 0.9), rgba(230, 238, 214, 0.9));
  color: #2c3e50;
  padding: 1rem 0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(5px);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  width: 70px;
  height: 70px;
  background-color: #FDFDFB;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border: 1px solid #E6EED6;
}

.logo-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 5px;
}

.ministry-info h1 {
  font-size: 1.4rem;
  margin-bottom: 0.25rem;
  color: #2c3e50;
}

.ministry-info p {
  font-size: 0.9rem;
  color: #5a6c7d;
}

/* Contenu principal */
.main-content {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.hero-section {
  text-align: center;
  max-width: 600px;
  width: 50%;
  background: linear-gradient(rgba(253, 253, 251, 0.85), rgba(230, 238, 214, 0.9));
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(230, 238, 214, 0.5);
}

.app-title h1 {
  font-size: 3rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.5rem;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
}

.app-subtitle {
  font-size: 1.2rem;
  color: #5a6c7d;
  margin-bottom: 2rem;
  font-weight: 500;
}

.description {
  margin-bottom: 3rem;
  padding: 1.5rem;
  background: rgba(241, 241, 211, 0.7);
  border-radius: 10px;
  border-left: 4px solid #C7DDC5;
}

.description p {
  font-size: 1.1rem;
  color: #5a6c7d;
  margin-bottom: 0.5rem;
}

.description p:first-child {
  font-weight: 600;
  color: #2c3e50;
}

/* Section d'authentification */
.auth-section {
  margin-top: 2rem;
}

.auth-buttons {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.75rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-block;
  font-size: 1rem;
  min-width: 150px;
  text-align: center;
  border: none;
  cursor: pointer;
}

.btn-login {
  background-color: #70c768;
  color: #2c3e50;
}

.btn-login:hover {
  background-color: #42993c;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-register {
  background-color: #E6EED6;
  color: #2c3e50;
  border: 2px solid #97d392;
}

.btn-register:hover {
  background-color: #dbe5c9;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.auth-info {
  color: #5a6c7d;
  font-size: 0.9rem;
  max-width: 400px;
  margin: 0 auto;
}

/* Pied de page */
.footer {
  background: rgba(222, 235, 195, 0.9);
  color: #1b2d3f;
  padding: 1.5rem 0;
  text-align: center;
  backdrop-filter: blur(5px);
  border-top: 1px solid rgba(199, 221, 197, 0.5);
}

.footer-content p {
  margin-bottom: 0.25rem;
  color: #5a6c7d;
}

.footer-content p:last-child {
  margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .logo-section {
    flex-direction: column;
    text-align: center;
    gap: 0.5rem;
  }
  
  .ministry-info h1 {
    font-size: 1.2rem;
  }
  
  .app-title h1 {
    font-size: 2.5rem;
  }
  
  .auth-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .btn {
    width: 200px;
  }
  
  .hero-section {
    padding: 2rem 1.5rem;
  }
}

@media (max-width: 480px) {
  .app-title h1 {
    font-size: 2rem;
  }
  
  .app-subtitle {
    font-size: 1rem;
  }
  
  .description p {
    font-size: 1rem;
  }
  
  .hero-section {
    padding: 1.5rem 1rem;
  }
}
</style>