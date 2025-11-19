<template>
  <div class="admin-layout">
    <!-- Header avec infos utilisateur -->
    <header class="dashboard-header">
      <div class="header-left">
        <h1 class="greeting">Hello {{ user.name }}</h1>
        <p class="sub-greeting">Have a nice Day to Work !</p>
      </div>
      <div class="header-right">
        <Link :href="route('profile.edit')" class="user-profile">
          <div class="user-avatar">
            <img v-if="user.photo" :src="user.photo" :alt="user.name" class="avatar-image" />
            <div v-else class="avatar-placeholder">
              {{ getUserInitials(user.name) }}
            </div>
          </div>
          <span class="user-name">{{ user.name }}</span>
        </Link>
      </div>
    </header>

    <div class="layout-content">
      <!-- Sidebar -->
      <aside class="sidebar">
        <div class="sidebar-header">
          <div class="app-logo">🎓 GES-ÉTUDIANT</div>
          <p class="app-subtitle">Admin Panel</p>
        </div>
        
        <nav class="sidebar-nav">
          <ul class="nav-list">
            <li v-for="link in navLinks" :key="link.name" class="nav-item">
              <Link 
                :href="link.href" 
                class="nav-link"
                :class="{ 'active': $page.url === link.href }"
              >
                <span class="nav-icon">{{ link.icon }}</span>
                <span class="nav-text">{{ link.name }}</span>
              </Link>
            </li>
          </ul>
        </nav>

        <!-- Footer du sidebar -->
        <div class="sidebar-footer">
          <Link :href="route('logout')" method="post" as="button" class="logout-btn">
            <span class="logout-icon">🚪</span>
            <span class="logout-text">Déconnexion</span>
          </Link>
        </div>
      </aside>

      <!-- Contenu principal -->
      <main class="main-content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Récupérer l'utilisateur connecté depuis les props
const props = defineProps({
  user: {
    type: Object,
    default: () => ({
      name: 'Admin',
      photo: null
    })
  }
})

// Liens de navigation
const navLinks = [
  { name: 'Accueil', href: '/admin', icon: '📊' },
  //{ name: 'Utilisateurs & Rôles', href: '/admin/utilisateurs', icon: '👥' },
  { name: 'Élèves', href: '/admin/eleves', icon: '👩🏻‍🏫' },
  { name: 'Enseignants', href: '/admin/enseignants', icon: '👩🏻‍🔬' },
  { name: 'Filières', href: '/admin/filieres', icon: '🖋️' },
  { name: 'Niveaux', href: '/admin/niveaux', icon: '📚' },
  { name: 'Salles', href: '/admin/salles', icon: '🏠' },
  { name: 'Matières', href: '/admin/matieres', icon: '📖' },
  { name: 'Inscriptions', href: '/admin/inscriptions', icon: '📇' },
  { name: 'Années scolaires', href: '/admin/anneesscolaires', icon: '📅' },
  { name: 'Notes', href: '/admin/notes', icon: '📝' },
  //{ name: 'Statistiques', href: '/admin/statistiques', icon: '📈' },
  { name: 'Messages', href: '/admin/messages', icon: '🔔' },
  //{ name: 'Paramètres', href: '/admin/parametres', icon: '⚙' },
]

// Fonction pour obtenir les initiales de l'utilisateur
const getUserInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
}
</script>

<style scoped>
.admin-layout {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

/* Header */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba(255, 255, 255, 0.95);
  padding: 20px 30px;
  border-radius: 15px;
  margin-bottom: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
}

.header-left .greeting {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0;
}

.header-left .sub-greeting {
  color: #718096;
  margin: 5px 0 0 0;
  font-size: 1rem;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  color: inherit;
  transition: transform 0.2s ease;
}

.user-profile:hover {
  transform: translateY(-2px);
}

.user-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  color: white;
  font-weight: 600;
  font-size: 1.2rem;
}

.user-name {
  font-weight: 600;
  color: #2d3748;
  font-size: 1.1rem;
}

/* Layout principal */
.layout-content {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 20px;
}

/* Sidebar */
.sidebar {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  display: flex;
  flex-direction: column;
  height: calc(100vh - 140px);
}

.sidebar-header {
  padding: 25px 20px 20px;
  border-bottom: 1px solid #e2e8f0;
}

.app-logo {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 5px;
}

.app-subtitle {
  color: #718096;
  font-size: 0.9rem;
  margin: 0;
}

.sidebar-nav {
  flex: 1;
  padding: 15px 0;
  overflow-y: auto;
}

.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-item {
  margin-bottom: 5px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 15px;
  text-decoration: none;
  color: #4a5568;
  border-radius: 10px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.nav-link:hover {
  background: rgba(102, 126, 234, 0.1);
  color: #667eea;
  border-left-color: #667eea;
  transform: translateX(5px);

}

.nav-link.active {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.nav-icon {
  font-size: 1.2rem;
  width: 20px;
  text-align: center;
}

.nav-text {
  font-size: 0.95rem;
}

.sidebar-footer {
  padding: 20px;
  border-top: 1px solid #e2e8f0;
  margin-top: auto;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 12px 15px;
  background: rgba(239, 68, 68, 0.1);
  color: #e53e3e;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  text-decoration: none;
}

.logout-btn:hover {
  background: rgba(239, 68, 68, 0.2);
  transform: translateX(5px);
}

.logout-icon {
  font-size: 1.2rem;
}

/* Contenu principal */
.main-content {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  overflow-y: auto;
  height: calc(100vh - 140px);
}

/* Responsive */
@media (max-width: 768px) {
  .admin-layout {
    padding: 10px;
  }
  
  .layout-content {
    grid-template-columns: 1fr;
  }
  
  .sidebar {
    height: auto;
    order: 2;
  }
  
  .main-content {
    order: 1;
    height: auto;
    min-height: 60vh;
  }
  
  .dashboard-header {
    flex-direction: column;
    gap: 15px;
    text-align: center;
    padding: 15px 20px;
  }
}

@media (max-width: 480px) {
  .admin-layout {
    padding: 5px;
  }
  
  .main-content {
    padding: 20px 15px;
  }
  
  .sidebar-header {
    padding: 20px 15px 15px;
  }
  
  .nav-link {
    padding: 10px 15px;
  }
}
</style>