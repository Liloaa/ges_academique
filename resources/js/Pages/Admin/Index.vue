<template>
  <div class="admin-dashboard">
    <!-- Header avec infos utilisateur -->
    <header class="dashboard-header">
      <div class="header-left">
        <h1 class="greeting">Hello {{ user.name }}</h1>
        <p class="sub-greeting">Have a nice Day to Work !</p>
      </div>
      <div class="header-right">
        <Link :href="route('admin.profile.edit')" class="user-profile">
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

    <div class="dashboard-content">
      <!-- Sidebar -->
      <aside class="sidebar">
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

      <!-- Main Content -->
      <main class="main-content">
        <!-- Profile Growth Section -->
        <section class="profile-growth">
          <div class="section-header">
            <h2>Profile Growth</h2>
            <p>Overall Information</p>
          </div>
          
          <!-- Stats Grid -->
          <div class="stats-grid">
            <!-- Revenue Card -->
            <div class="stat-card revenue-card">
              <div class="stat-header">
                <h3>Revenue</h3>
                <span class="stat-trend positive">+4.46%</span>
              </div>
              <div class="stat-value">$123,001</div>
              <div class="stat-chart">
                <!-- Simple chart placeholder -->
                <div class="chart-bar" style="height: 60%"></div>
                <div class="chart-bar" style="height: 80%"></div>
                <div class="chart-bar" style="height: 45%"></div>
                <div class="chart-bar" style="height: 90%"></div>
                <div class="chart-bar" style="height: 70%"></div>
                <div class="chart-bar" style="height: 85%"></div>
                <div class="chart-bar" style="height: 65%"></div>
              </div>
            </div>

            <!-- Subscribers Card -->
            <div class="stat-card">
              <div class="stat-header">
                <h3>New Subscribers</h3>
                <span class="stat-trend positive">+33.45%</span>
              </div>
              <div class="stat-value">5,095</div>
              <div class="stat-description">Monthly growth</div>
            </div>

            <!-- Syncons Card -->
            <div class="stat-card">
              <div class="stat-header">
                <h3>Syncons</h3>
                <span class="stat-trend negative">-112.46%</span>
              </div>
              <div class="stat-value">47,095</div>
              <div class="stat-description">Total connections</div>
            </div>

            <!-- Engagement Card -->
            <div class="stat-card">
              <div class="stat-header">
                <h3>Engagement</h3>
                <span class="stat-trend positive">+82.10%</span>
              </div>
              <div class="stat-value">25.81%</div>
              <div class="stat-description">Rate increase</div>
            </div>

            <!-- Projects Card -->
            <div class="stat-card projects-card">
              <div class="stat-header">
                <h3>Open Projects</h3>
              </div>
              <div class="projects-stats">
                <div class="project-stat">
                  <span class="stat-number">500</span>
                  <span class="stat-label">Successfully Completed</span>
                </div>
                <div class="project-stat">
                  <span class="stat-number">3,502</span>
                  <span class="stat-label">Ended This Month</span>
                </div>
                <div class="project-stat">
                  <span class="stat-number">$523,001</span>
                  <span class="stat-label">Revenue</span>
                </div>
              </div>
            </div>

            <!-- Weekly Activity Card -->
            <div class="stat-card activity-card">
              <div class="stat-header">
                <h3>Weekly Activity</h3>
              </div>
              <div class="activity-days">
                <div class="day" v-for="day in weeklyActivity" :key="day.name">
                  <span class="day-name">{{ day.name }}</span>
                  <span class="day-value">{{ day.value }}</span>
                </div>
              </div>
            </div>

            <!-- Watch Time Card -->
            <div class="stat-card">
              <div class="stat-header">
                <h3>Avg Watch time</h3>
                <span class="stat-trend positive">+4.46%</span>
              </div>
              <div class="stat-value">45.42</div>
              <div class="stat-description">Hours per week</div>
            </div>

            <!-- Quick Actions Card -->
            <div class="stat-card actions-card">
              <div class="stat-header">
                <h3>Quick Actions</h3>
              </div>
              <div class="quick-actions">
                <Link href="/admin/eleves/create" class="action-btn">
                  <span class="action-icon">➕</span>
                  <span>Add Student</span>
                </Link>
                <Link href="/admin/enseignants/create" class="action-btn">
                  <span class="action-icon">👨‍🏫</span>
                  <span>Add Teacher</span>
                </Link>
                <Link href="/admin/utilisateurs" class="action-btn">
                  <span class="action-icon">👥</span>
                  <span>Manage Users</span>
                </Link>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Données utilisateur (à adapter avec vos données réelles)
const user = {
  name: 'John Doe',
  photo: null // L'admin pourra uploader une photo via l'application
}

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

// Activité hebdomadaire
const weeklyActivity = [
  { name: 'Mo', value: '12' },
  { name: 'Tu', value: '13' },
  { name: 'We', value: '15' },
  { name: 'Th', value: '14' },
  { name: 'Fr', value: '17' },
  { name: 'Sa', value: '16' },
  { name: 'Su', value: '15' }
]

// Fonction pour obtenir les initiales de l'utilisateur
const getUserInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
}
</script>

<style scoped>
.admin-dashboard {
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
.dashboard-content {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 20px;
}

/* Sidebar */
.sidebar {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  height: fit-content;
}

.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-item {
  margin-bottom: 8px;
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
}

.nav-text {
  font-size: 0.95rem;
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

.sidebar-footer {
  padding: 20px;
  border-top: 1px solid #e2e8f0;
}

/* Contenu principal */
.main-content {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
}

.section-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0;
}

.section-header p {
  color: #718096;
  margin: 5px 0 20px 0;
}

/* Grille de statistiques */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  border: 1px solid #e2e8f0;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.stat-header h3 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #718096;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-trend {
  font-size: 0.8rem;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 12px;
}

.stat-trend.positive {
  background: #c6f6d5;
  color: #22543d;
}

.stat-trend.negative {
  background: #fed7d7;
  color: #742a2a;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 5px;
}

.stat-description {
  font-size: 0.85rem;
  color: #718096;
}

/* Carte revenue avec graphique */
.revenue-card {
  grid-column: span 2;
}

.stat-chart {
  display: flex;
  align-items: end;
  gap: 8px;
  height: 80px;
  margin-top: 15px;
}

.chart-bar {
  flex: 1;
  background: linear-gradient(to top, #667eea, #764ba2);
  border-radius: 4px 4px 0 0;
  min-height: 20px;
  transition: height 0.3s ease;
}

.chart-bar:nth-child(odd) {
  background: linear-gradient(to top, #764ba2, #667eea);
}

/* Carte projets */
.projects-card {
  grid-column: span 2;
}

.projects-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
  margin-top: 10px;
}

.project-stat {
  text-align: center;
  padding: 15px;
  background: #f7fafc;
  border-radius: 8px;
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 0.8rem;
  color: #718096;
}

/* Carte activité */
.activity-card {
  grid-column: span 2;
}

.activity-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 10px;
  margin-top: 10px;
}

.day {
  text-align: center;
  padding: 10px 5px;
  background: #f7fafc;
  border-radius: 8px;
}

.day-name {
  display: block;
  font-size: 0.8rem;
  color: #718096;
  margin-bottom: 5px;
}

.day-value {
  display: block;
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
}

/* Actions rapides */
.actions-card {
  grid-column: span 2;
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
  margin-top: 10px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 15px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.action-icon {
  font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
}

@media (max-width: 768px) {
  .dashboard-content {
    grid-template-columns: 1fr;
  }
  
  .sidebar {
    order: 2;
  }
  
  .main-content {
    order: 1;
  }
  
  .revenue-card,
  .projects-card,
  .activity-card,
  .actions-card {
    grid-column: span 1;
  }
  
  .dashboard-header {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .admin-dashboard {
    padding: 10px;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .activity-days {
    grid-template-columns: repeat(4, 1fr);
  }
  
  .projects-stats {
    grid-template-columns: 1fr;
  }
}
</style>