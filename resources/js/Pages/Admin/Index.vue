<template>
  <AdminLayout>
    <!-- Main Content -->
    <main class="main-content">
      <!-- Section des statistiques principales -->
      <section class="stats-section">
        <div class="stats-grid">
          <!-- Carte 1: Total élèves -->
          <div class="stat-card">
            <div class="stat-header">
              <h3>Total Élèves</h3>
              <span class="stat-icon">👨‍🎓</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques.totalEleves }}</div>
              <p class="stat-desc">Élèves enregistrés dans le système</p>
            </div>
          </div>

          <!-- Carte 2: Inscrits cette année -->
          <div class="stat-card">
            <div class="stat-header">
              <h3>Inscrits {{ statistiques.anneeActive }}</h3>
              <span class="stat-icon">📚</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques.totalInscrits }}</div>
              <p class="stat-desc">Élèves actuellement inscrits</p>
            </div>
          </div>

          <!-- Carte 3: Taux de réussite -->
          <div class="stat-card">
            <div class="stat-header">
              <h3>Taux de Réussite</h3>
              <span class="stat-icon">📈</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques.tauxReussite }}%</div>
              <p class="stat-desc">Moyenne de l'établissement</p>
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: statistiques.tauxReussite + '%' }"></div>
              </div>
            </div>
          </div>

          <!-- Carte 4: Année scolaire -->
          <div class="stat-card">
            <div class="stat-header">
              <h3>Année Scolaire</h3>
              <span class="stat-icon">🗓️</span>
            </div>
            <div class="stat-content">
              <div class="stat-text">{{ statistiques.anneeActive }}</div>
              <p class="stat-desc">Année scolaire en cours</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Section des statistiques par cycle -->
      <section class="cycle-stats">
        <h3>Répartition par Cycle</h3>
        <div class="cycle-grid">
          <div class="cycle-card" v-for="(count, cycle) in statistiques.statistiquesCycles" :key="cycle">
            <div class="cycle-icon">
              <span v-if="cycle === 'primaire'">👶</span>
              <span v-else-if="cycle === 'college'">🧑‍🎓</span>
              <span v-else>👨‍🎓</span>
            </div>
            <div class="cycle-info">
              <div class="cycle-name">{{ cycle }}</div>
              <div class="cycle-count">{{ count }} élèves</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Section des actions rapides et dernières inscriptions -->
      <div class="content-grid">
        <!-- Actions rapides -->
        <div class="actions-section">
          <div class="stat-card actions-card">
            <div class="stat-header">
              <h3>Actions Rapides</h3>
            </div>
            <div class="quick-actions">
              <Link href="/admin/eleves/create" class="action-btn">
                <span class="action-icon">➕</span>
                <span>Ajouter Élève</span>
              </Link>
              <Link href="/admin/enseignants/create" class="action-btn">
                <span class="action-icon">👨‍🏫</span>
                <span>Ajouter Enseignant</span>
              </Link>
              <Link href="/admin/inscriptions/create" class="action-btn">
                <span class="action-icon">📝</span>
                <span>Nouvelle Inscription</span>
              </Link>
              <Link href="/admin/notes/create" class="action-btn">
                <span class="action-icon">📊</span>
                <span>Saisir Notes</span>
              </Link>
            </div>
          </div>
        </div>

        <!-- Dernières inscriptions -->
        <div class="recent-section">
          <div class="stat-card">
            <div class="stat-header">
              <h3>Dernières Inscriptions</h3>
              <Link href="/admin/inscriptions" class="view-all">Voir tout</Link>
            </div>
            <div class="recent-list">
              <div v-for="inscription in statistiques.dernieresInscriptions" :key="inscription.id" class="recent-item">
                <div class="recent-avatar">
                  {{ inscription.eleve.prenom.charAt(0) }}{{ inscription.eleve.nom.charAt(0) }}
                </div>
                <div class="recent-info">
                  <div class="recent-name">{{ inscription.eleve.prenom }} {{ inscription.eleve.nom }}</div>
                  <div class="recent-details">
                    {{ inscription.salle?.niveau?.nomNiveau || 'N/A' }} • 
                    {{ new Date(inscription.date_inscription).toLocaleDateString('fr-FR') }}
                  </div>
                </div>
              </div>
              <div v-if="statistiques.dernieresInscriptions.length === 0" class="empty-state">
                Aucune inscription récente
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Graphique des inscriptions mensuelles -->
      <section class="chart-section" v-if="statistiques.statistiquesMensuelles.length > 0">
        <div class="stat-card">
          <div class="stat-header">
            <h3>Inscriptions par Mois ({{ new Date().getFullYear() }})</h3>
          </div>
          <div class="chart-container">
            <div class="chart-bars">
              <div v-for="item in statistiques.statistiquesMensuelles" :key="item.mois" class="chart-bar">
                <div class="bar-label">{{ item.mois }}</div>
                <div class="bar-container">
                  <div class="bar-fill" :style="{ height: (item.nombre / getMaxInscriptions()) * 100 + '%' }">
                    <span class="bar-value">{{ item.nombre }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  statistiques: Object
})

const getMaxInscriptions = () => {
  if (!props.statistiques.statistiquesMensuelles.length) return 1
  return Math.max(...props.statistiques.statistiquesMensuelles.map(item => item.nombre))
}
</script>

<style scoped>
/* Contenu principal */
.main-content {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* Grille de statistiques principales */
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
  padding-bottom: 10px;
  border-bottom: 1px solid #e2e8f0;
}

.stat-header h3 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #718096;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-icon {
  font-size: 1.5rem;
}

.stat-content {
  text-align: center;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2d3748;
  margin: 10px 0;
}

.stat-text {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2d3748;
  margin: 10px 0;
}

.stat-desc {
  color: #718096;
  font-size: 0.9rem;
  margin: 5px 0 15px;
}

/* Barre de progression */
.progress-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
  margin-top: 10px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(135deg, #10b981, #34d399);
  border-radius: 4px;
  transition: width 0.5s ease;
}

/* Statistiques par cycle */
.cycle-stats {
  margin-top: 20px;
}

.cycle-stats h3 {
  color: #2d3748;
  margin-bottom: 15px;
  font-size: 1.2rem;
}

.cycle-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.cycle-card {
  background: white;
  padding: 15px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 15px;
  border: 1px solid #e2e8f0;
}

.cycle-icon {
  font-size: 2rem;
}

.cycle-info {
  flex: 1;
}

.cycle-name {
  text-transform: capitalize;
  font-weight: 600;
  color: #2d3748;
}

.cycle-count {
  color: #718096;
  font-size: 0.9rem;
}

/* Grille de contenu */
.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

/* Actions rapides */
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

/* Dernières inscriptions */
.view-all {
  color: #667eea;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
}

.recent-list {
  margin-top: 15px;
}

.recent-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 12px 0;
  border-bottom: 1px solid #f1f1f1;
}

.recent-item:last-child {
  border-bottom: none;
}

.recent-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
}

.recent-info {
  flex: 1;
}

.recent-name {
  font-weight: 600;
  color: #2d3748;
}

.recent-details {
  color: #718096;
  font-size: 0.85rem;
}

.empty-state {
  text-align: center;
  color: #a0aec0;
  padding: 30px;
  font-style: italic;
}

/* Graphique */
.chart-container {
  margin-top: 20px;
}

.chart-bars {
  display: flex;
  align-items: flex-end;
  gap: 20px;
  height: 200px;
  padding: 20px 0;
}

.chart-bar {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.bar-label {
  color: #718096;
  font-size: 0.85rem;
  text-align: center;
}

.bar-container {
  width: 100%;
  height: 150px;
  background: #f7fafc;
  border-radius: 8px;
  position: relative;
  overflow: hidden;
}

.bar-fill {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, #4299e1, #63b3ed);
  border-radius: 8px;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding-top: 5px;
  transition: height 0.5s ease;
}

.bar-value {
  color: white;
  font-size: 0.8rem;
  font-weight: 600;
}
</style>