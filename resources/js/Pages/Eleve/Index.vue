<template>
  <EleveLayout>
    <div class="main-content">
      <!-- En-tête du tableau de bord -->
      <div class="dashboard-header">
        <h1 class="dashboard-title">Tableau de Bord</h1>
        <div class="header-info">
          <span v-if="dashboardData.inscription" class="info-badge">
            📅 Année scolaire : {{ dashboardData.inscription.annee.libelle }}
          </span>
          <span v-if="eleve" class="info-badge">
            👤 {{ eleve.nom }} {{ eleve.prenom }}
          </span>
        </div>
      </div>

      <!-- Section d'erreur -->
      <div v-if="error" class="alert alert-error">
        {{ error }}
      </div>

      <!-- Message si pas d'inscription active -->
      <div v-if="!dashboardData.inscription && !error" class="alert alert-warning">
        ⚠️ Aucune inscription active trouvée pour cette année scolaire.
      </div>

      <!-- Statistiques principales -->
      <div v-if="dashboardData.inscription" class="dashboard-grid">
        <!-- Carte : Salle et Niveau -->
        <div class="stat-card">
          <div class="stat-header">
            <h3>Salle & Niveau</h3>
            <div class="stat-icon">🏫</div>
          </div>
          <div class="stat-content">
            <div class="stat-value">
              <h4>{{ dashboardData.inscription.salle.nomSalle }}</h4>
              <p class="stat-description">
                Niveau : {{ dashboardData.inscription.salle.niveau.nomNiveau }}
                <span v-if="dashboardData.inscription.salle.niveau.filiere">
                  - {{ dashboardData.inscription.salle.niveau.filiere.nomFiliere }}
                </span>
              </p>
              <p class="stat-description">
                Cycle : {{ dashboardData.inscription.salle.niveau.cycle }}
              </p>
            </div>
          </div>
        </div>

        <!-- Carte : Moyenne Générale -->
        <div class="stat-card">
          <div class="stat-header">
            <h3>Moyenne Générale</h3>
            <div class="stat-icon">📊</div>
          </div>
          <div class="stat-content">
            <div class="stat-value">
              <h4 :class="getMoyenneClass(dashboardData.moyenne_generale)">
                {{ dashboardData.moyenne_generale.toFixed(2) }}/20
              </h4>
              <p class="stat-description">
                Appréciation : {{ dashboardData.appreciation }}
              </p>
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: (dashboardData.moyenne_generale * 5) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Carte : Taux de Réussite -->
        <div class="stat-card">
          <div class="stat-header">
            <h3>Taux de Réussite</h3>
            <div class="stat-icon">🎯</div>
          </div>
          <div class="stat-content">
            <div class="stat-value">
              <h4 :class="getTauxReussiteClass(dashboardData.taux_reussite)">
                {{ dashboardData.taux_reussite }}%
              </h4>
              <p class="stat-description">
                Matières validées cette année
              </p>
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: dashboardData.taux_reussite + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Carte : Notes Récentes -->
        <div class="stat-card full-width">
          <div class="stat-header">
            <h3>Dernières Notes</h3>
            <div class="stat-icon">📝</div>
          </div>
          <div class="stat-content">
            <div v-if="dashboardData.dernieres_notes.length > 0" class="notes-list">
              <div v-for="note in dashboardData.dernieres_notes" :key="note.id" class="note-item">
                <div class="note-matiere">{{ note.matiere }}</div>
                <div class="note-details">
                  <span class="note-value" :class="getNoteClass(note.note)">
                    {{ note.note }}/20
                  </span>
                  <span class="note-info">
                    Coef. {{ note.coefficient }} • {{ note.trimestre }} trimestre
                  </span>
                </div>
                <div class="note-date">{{ note.date }}</div>
              </div>
            </div>
            <div v-else class="empty-state">
              Aucune note enregistrée pour le moment.
            </div>
          </div>
        </div>

        <!-- Section : Statistiques détaillées -->
        <div class="stat-card full-width">
          <div class="stat-header">
            <h3>Statistiques des Notes</h3>
            <div class="stat-icon">📈</div>
          </div>
          <div class="stat-content">
            <div class="stats-grid">
              <div class="stat-item">
                <div class="stat-label">Total des notes</div>
                <div class="stat-number">{{ dashboardData.statistiques_notes.total_notes }}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Note maximale</div>
                <div class="stat-number">{{ dashboardData.statistiques_notes.moyenne_max }}/20</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Note minimale</div>
                <div class="stat-number">{{ dashboardData.statistiques_notes.moyenne_min }}/20</div>
              </div>
            </div>
            
            <!-- Graphique des moyennes par trimestre -->
            <div class="trimestre-stats">
              <h4>Moyennes par trimestre</h4>
              <div class="trimestre-list">
                <div v-for="(stats, trimestre) in dashboardData.statistiques_notes.notes_par_trimestre" 
                     :key="trimestre" class="trimestre-item">
                  <div class="trimestre-label">{{ trimestre }} trimestre</div>
                  <div class="trimestre-value">{{ stats.moyenne }}/20</div>
                  <div class="trimestre-count">{{ stats.nombre_notes }} notes</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions rapides -->
        <div class="stat-card actions-card">
          <div class="stat-header">
            <h3>Actions Rapides</h3>
          </div>
          <div class="quick-actions">
            <Link href="/eleve/notes/bulletin" class="action-btn primary">
              <span class="action-icon">📜</span>
              <span>Voir mon bulletin</span>
            </Link>
            <Link href="/eleve/notes" class="action-btn secondary">
              <span class="action-icon">📊</span>
              <span>Détail de mes notes</span>
            </Link>
            <Link href="/eleve/profile" class="action-btn tertiary">
              <span class="action-icon">👤</span>
              <span>Mon profil</span>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </EleveLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import EleveLayout from '@/Layouts/EleveLayout.vue'

defineProps({
  eleve: Object,
  dashboardData: Object,
  error: String,
  anneeActive: Object
})

// Fonction pour déterminer la classe CSS selon la moyenne
const getMoyenneClass = (moyenne) => {
  if (moyenne >= 16) return 'excellent'
  if (moyenne >= 14) return 'very-good'
  if (moyenne >= 12) return 'good'
  if (moyenne >= 10) return 'fair'
  if (moyenne >= 8) return 'passable'
  return 'insufficient'
}

// Fonction pour déterminer la classe CSS selon le taux de réussite
const getTauxReussiteClass = (taux) => {
  if (taux >= 80) return 'excellent'
  if (taux >= 60) return 'good'
  if (taux >= 40) return 'fair'
  return 'insufficient'
}

// Fonction pour déterminer la classe CSS selon la note
const getNoteClass = (note) => {
  if (note >= 16) return 'excellent'
  if (note >= 14) return 'very-good'
  if (note >= 12) return 'good'
  if (note >= 10) return 'fair'
  if (note >= 8) return 'passable'
  return 'insufficient'
}
</script>

<style scoped>
.main-content {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
}

.dashboard-header {
  margin-bottom: 30px;
  border-bottom: 2px solid #f0f4f8;
  padding-bottom: 20px;
}

.dashboard-title {
  font-size: 2rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 10px;
}

.header-info {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.info-badge {
  background: #e2e8f0;
  padding: 8px 15px;
  border-radius: 20px;
  font-size: 0.9rem;
  color: #4a5568;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.full-width {
  grid-column: 1 / -1;
}

.stat-card {
  background: white;
  padding: 25px;
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
  margin-bottom: 20px;
}

.stat-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #718096;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-icon {
  font-size: 2rem;
}

.stat-content {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.stat-value h4 {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0;
  color: #2d3748;
}

.stat-description {
  color: #718096;
  font-size: 0.95rem;
  margin: 10px 0;
}

/* Classes pour les couleurs des notes */
.excellent { color: #10b981; }
.very-good { color: #3b82f6; }
.good { color: #8b5cf6; }
.fair { color: #f59e0b; }
.passable { color: #f97316; }
.insufficient { color: #ef4444; }

/* Barre de progression */
.progress-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
  margin-top: 15px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 4px;
  transition: width 0.5s ease;
}

/* Liste des notes */
.notes-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.note-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: #f7fafc;
  border-radius: 8px;
  border-left: 4px solid #667eea;
}

.note-matiere {
  font-weight: 600;
  color: #2d3748;
  flex: 1;
}

.note-details {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  margin: 0 20px;
}

.note-value {
  font-weight: 700;
  font-size: 1.2rem;
}

.note-info {
  font-size: 0.85rem;
  color: #718096;
}

.note-date {
  color: #a0aec0;
  font-size: 0.9rem;
}

/* Statistiques */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 20px;
  margin-bottom: 25px;
}

.stat-item {
  text-align: center;
  padding: 15px;
  background: #f7fafc;
  border-radius: 8px;
}

.stat-label {
  font-size: 0.9rem;
  color: #718096;
  margin-bottom: 5px;
}

.stat-number {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
}

/* Trimestres */
.trimestre-stats {
  margin-top: 20px;
}

.trimestre-stats h4 {
  font-size: 1rem;
  color: #718096;
  margin-bottom: 15px;
}

.trimestre-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
}

.trimestre-item {
  padding: 15px;
  background: #f7fafc;
  border-radius: 8px;
  text-align: center;
}

.trimestre-label {
  font-weight: 600;
  color: #4a5568;
  margin-bottom: 5px;
}

.trimestre-value {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 5px;
}

.trimestre-count {
  font-size: 0.85rem;
  color: #718096;
}

/* Actions rapides */
.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 15px;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.action-btn.primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.action-btn.secondary {
  background: #e2e8f0;
  color: #4a5568;
}

.action-btn.tertiary {
  background: #f7fafc;
  color: #4a5568;
  border: 1px solid #e2e8f0;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-icon {
  font-size: 1.2rem;
}

/* États vides */
.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #a0aec0;
  font-style: italic;
}

/* Alertes */
.alert {
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.alert-error {
  background: #fed7d7;
  color: #9b2c2c;
  border: 1px solid #fc8181;
}

.alert-warning {
  background: #feebc8;
  color: #9c4221;
  border: 1px solid #f6ad55;
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .stat-card {
    padding: 20px;
  }
  
  .note-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .note-details {
    align-items: flex-start;
    margin: 0;
  }
}
</style>