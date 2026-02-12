<template>
  <EleveLayout>
    <div class="combined-dashboard">
      <!-- ==================== -->
      <!-- PREMIÈRE INTERFACE (Dashboard) -->
      <!-- ==================== -->
      <div class="dashboard-container">
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
                    <span class="note-value" :class="getNoteColorClass(note.note)">
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

      <!-- ==================== -->
      <!-- DEUXIÈME INTERFACE (Détail des notes) -->
      <!-- ==================== -->
      <div class="notes-detail-section">
        <!-- En-tête -->
        <div class="notes-header">
          <div class="flex justify-between items-center">
            <div>
              <h2 class="notes-title">📊 Mes Notes et Résultats</h2>
              <p class="notes-subtitle">
                Consultation de vos notes par trimestre et de votre moyenne générale
              </p>
            </div>
            <!--<button
              @click="router.visit(route('eleve.notes.bulletin'))"
              class="bulletin-btn"
            >
              📄 Voir mon bulletin
            </button>-->
          </div>
        </div>

        <!-- Informations élève -->
        <div class="info-grid">
          <div class="info-card">
            <p class="info-label">Matricule</p>
            <p class="info-value">{{ eleve.matricule }}</p>
          </div>
          <div class="info-card">
            <p class="info-label">Niveau actuel</p>
            <p class="info-value">{{ inscriptionActive?.salle?.niveau?.nomNiveau || 'Non défini' }}</p>
          </div>
          <div class="info-card">
            <p class="info-label">Année scolaire</p>
            <p class="info-value">{{ inscriptionActive?.annee?.libelle || 'Non définie' }}</p>
          </div>
        </div>

        <!-- Moyennes par trimestre -->
        <div class="trimestre-section">
          <h3 class="section-title">📈 Moyennes par trimestre</h3>
          <div class="trimestre-grid">
            <div
              v-for="(moyenne, trimestre) in resultats.moyennes_trimestrielles"
              :key="trimestre"
              class="trimestre-card"
            >
              <div class="trimestre-header">
                <div>
                  <p class="trimestre-label">{{ trimestre }} Trimestre</p>
                  <p class="trimestre-moyenne">{{ moyenne.toFixed(2) }}/20</p>
                </div>
                <div
                  :class="getNoteGradientClass(moyenne)"
                  class="note-badge"
                >
                  {{ getNoteLetter(moyenne) }}
                </div>
              </div>
              <p class="trimestre-appreciation">
                {{ getAppreciation(moyenne) }}
              </p>
            </div>
          </div>

          <!-- Moyenne générale -->
          <div class="moyenne-generale">
            <div class="flex justify-between items-center">
              <div>
                <p class="moyenne-label">Moyenne Générale</p>
                <p class="moyenne-value">{{ moyenneGenerale.toFixed(2) }}/20</p>
                <p class="moyenne-appreciation">{{ getAppreciation(moyenneGenerale) }}</p>
              </div>
              <div class="progress-circle">
                <svg class="w-32 h-32" viewBox="0 0 36 36">
                  <path
                    d="M18 2.0845
                      a 15.9155 15.9155 0 0 1 0 31.831
                      a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none"
                    stroke="#e5e7eb"
                    stroke-width="3"
                  />
                  <path
                    d="M18 2.0845
                      a 15.9155 15.9155 0 0 1 0 31.831
                      a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none"
                    :stroke="getProgressColor(moyenneGenerale)"
                    stroke-width="3"
                    stroke-dasharray="100"
                    :stroke-dashoffset="100 - (moyenneGenerale * 5)"
                  />
                </svg>
                <div class="circle-text">
                  <span :class="getProgressTextColor(moyenneGenerale)">
                    {{ ((moyenneGenerale / 20) * 100).toFixed(0) }}%
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Détail des notes par matière -->
        <div class="matieres-section">
          <h3 class="section-title">📚 Détail des notes par matière</h3>
          
          <div v-if="resultats.matieres.length > 0" class="matieres-table">
            <table>
              <thead>
                <tr>
                  <th>Matière</th>
                  <th>Coefficient</th>
                  <th>1er Trimestre</th>
                  <th>2ème Trimestre</th>
                  <th>3ème Trimestre</th>
                  <th>Moyenne Annuelle</th>
                  <th>Appréciation</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="matiere in resultats.matieres"
                  :key="matiere.matiere.id"
                >
                  <td>
                    <div class="matiere-name">{{ matiere.matiere.nomMatiere }}</div>
                  </td>
                  <td>
                    {{ matiere.coefficient }}
                  </td>
                  <td>
                    <span :class="getNoteCellClass(matiere.trimestres['1er'])" class="note-cell">
                      {{ matiere.trimestres['1er'] !== null ? matiere.trimestres['1er'].toFixed(2) : '-' }}
                    </span>
                  </td>
                  <td>
                    <span :class="getNoteCellClass(matiere.trimestres['2ème'])" class="note-cell">
                      {{ matiere.trimestres['2ème'] !== null ? matiere.trimestres['2ème'].toFixed(2) : '-' }}
                    </span>
                  </td>
                  <td>
                    <span :class="getNoteCellClass(matiere.trimestres['3ème'])" class="note-cell">
                      {{ matiere.trimestres['3ème'] !== null ? matiere.trimestres['3ème'].toFixed(2) : '-' }}
                    </span>
                  </td>
                  <td>
                    <span :class="getNoteCellClass(matiere.moyenne_annuelle)" class="moyenne-cell">
                      {{ matiere.moyenne_annuelle !== null && matiere.moyenne_annuelle !== 0 ? matiere.moyenne_annuelle.toFixed(2) : '-' }}
                    </span>
                  </td>
                  <td>
                    {{ getAppreciation(matiere.moyenne_annuelle) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-else class="empty-notes">
            <div class="empty-icon">📚</div>
            <p class="empty-title">Aucune note disponible pour le moment.</p>
            <p class="empty-subtitle">Vos notes apparaîtront ici une fois saisies par vos enseignants.</p>
          </div>
        </div>

        <!-- Légende -->
        <div class="legend">
          <div class="flex items-center justify-center gap-6">
            <div class="flex items-center gap-2">
              <span class="legend-badge excellent">10+</span>
              <span class="legend-text">Bonne note</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="legend-badge average">8-10</span>
              <span class="legend-text">Note moyenne</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="legend-badge weak">0-8</span>
              <span class="legend-text">Note à améliorer</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="legend-badge empty">-</span>
              <span class="legend-text">Note non saisie</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </EleveLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import EleveLayout from '@/Layouts/EleveLayout.vue'
import { computed } from 'vue'

defineProps({
  // Props de la première interface
  eleve: Object,
  dashboardData: Object,
  error: String,
  anneeActive: Object,
  
  // Props de la deuxième interface
  inscriptionActive: Object,
  resultats: Object,
  moyenneGenerale: Number,
})

// ====================
// FONCTIONS COMMUNES
// ====================

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

// Fonction pour déterminer la classe de couleur selon la note (pour la première interface)
const getNoteColorClass = (note) => {
  if (note >= 16) return 'excellent'
  if (note >= 14) return 'very-good'
  if (note >= 12) return 'good'
  if (note >= 10) return 'fair'
  if (note >= 8) return 'passable'
  return 'insufficient'
}

// Fonction pour obtenir l'appréciation
const getAppreciation = (note) => {
  if (note >= 16) return 'Excellent'
  if (note >= 14) return 'Très Bien'
  if (note >= 12) return 'Bien'
  if (note >= 10) return 'Assez Bien'
  if (note >= 8) return 'Passable'
  if (note > 0) return 'Insuffisant'
  return 'Non évalué'
}

// ====================
// FONCTIONS DE LA DEUXIÈME INTERFACE
// ====================

// Fonction pour déterminer la classe de gradient selon la note
const getNoteGradientClass = (note) => {
  if (note >= 16) return 'bg-gradient-to-br from-green-500 to-emerald-600'
  if (note >= 14) return 'bg-gradient-to-br from-blue-500 to-indigo-600'
  if (note >= 12) return 'bg-gradient-to-br from-indigo-400 to-purple-500'
  if (note >= 10) return 'bg-gradient-to-br from-yellow-400 to-orange-500'
  if (note >= 8) return 'bg-gradient-to-br from-orange-400 to-red-500'
  return 'bg-gradient-to-br from-red-400 to-pink-500'
}

// Fonction pour obtenir la lettre de note
const getNoteLetter = (note) => {
  if (note >= 16) return 'A+'
  if (note >= 14) return 'A'
  if (note >= 12) return 'B'
  if (note >= 10) return 'C'
  if (note >= 8) return 'D'
  return 'E'
}

// Fonction pour déterminer la classe CSS des cellules de note
const getNoteCellClass = (note) => {
  if (note === null || note === 0) return 'empty'
  if (note >= 10) return 'excellent'
  if (note >= 8) return 'average'
  return 'weak'
}

// Fonction pour déterminer la couleur de la barre de progression
const getProgressColor = (moyenne) => {
  if (moyenne >= 16) return '#10b981'
  if (moyenne >= 14) return '#3b82f6'
  if (moyenne >= 12) return '#8b5cf6'
  if (moyenne >= 10) return '#f59e0b'
  if (moyenne >= 8) return '#f97316'
  return '#ef4444'
}

// Fonction pour déterminer la couleur du texte de progression
const getProgressTextColor = (moyenne) => {
  if (moyenne >= 16) return 'text-green-600'
  if (moyenne >= 14) return 'text-blue-600'
  if (moyenne >= 12) return 'text-indigo-600'
  if (moyenne >= 10) return 'text-yellow-600'
  if (moyenne >= 8) return 'text-orange-600'
  return 'text-red-600'
}
</script>

<style scoped>
/* Styles communs */
.combined-dashboard {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  display: flex;
  flex-direction: column;
  gap: 40px;
}

/* ==================== */
/* PREMIÈRE INTERFACE STYLES */
/* ==================== */
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

/* ==================== */
/* DEUXIÈME INTERFACE STYLES */
/* ==================== */
.notes-detail-section {
  margin-top: 40px;
  padding-top: 40px;
  border-top: 2px solid #e2e8f0;
}

.notes-header {
  margin-bottom: 30px;
}

.notes-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 5px;
}

.notes-subtitle {
  color: #718096;
  font-size: 0.95rem;
}

.bulletin-btn {
  padding: 10px 20px;
  background: #4f46e5;
  color: white;
  border-radius: 8px;
  border: none;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.bulletin-btn:hover {
  background: #4338ca;
  transform: translateY(-2px);
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
  margin-bottom: 30px;
}

.info-card {
  background: #f8fafc;
  padding: 20px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}

.info-label {
  font-size: 0.9rem;
  color: #64748b;
  margin-bottom: 5px;
}

.info-value {
  font-weight: 600;
  color: #1e293b;
  font-size: 1.1rem;
}

.trimestre-section {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin-bottom: 30px;
}

.section-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 20px;
}

.trimestre-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.trimestre-card {
  background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
  padding: 20px;
  border-radius: 10px;
  border: 1px solid #bae6fd;
}

.trimestre-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.trimestre-label {
  font-size: 0.9rem;
  color: #0c4a6e;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.trimestre-moyenne {
  font-size: 2rem;
  font-weight: 700;
  color: #075985;
  margin-top: 5px;
}

.note-badge {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 1.2rem;
}

.trimestre-appreciation {
  font-size: 0.9rem;
  color: #0369a1;
}

.moyenne-generale {
  background: #f8fafc;
  padding: 25px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}

.moyenne-label {
  font-size: 0.9rem;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.moyenne-value {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 10px 0;
}

.moyenne-appreciation {
  color: #475569;
  font-size: 1rem;
}

.progress-circle {
  position: relative;
}

.circle-text {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: 700;
}

.matieres-section {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin-bottom: 30px;
  overflow-x: auto;
}

.matieres-table {
  overflow-x: auto;
}

.matieres-table table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}

.matieres-table th {
  background: #f1f5f9;
  padding: 12px 15px;
  text-align: left;
  font-weight: 600;
  color: #475569;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #e2e8f0;
}

.matieres-table td {
  padding: 15px;
  border-bottom: 1px solid #f1f5f9;
  color: #475569;
}

.matiere-name {
  font-weight: 500;
  color: #1e293b;
}

.note-cell, .moyenne-cell {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 4px;
  font-weight: 500;
}

.note-cell.excellent, .moyenne-cell.excellent {
  background: #d1fae5;
  color: #065f46;
}

.note-cell.average, .moyenne-cell.average {
  background: #fef3c7;
  color: #92400e;
}

.note-cell.weak, .moyenne-cell.weak {
  background: #fee2e2;
  color: #991b1b;
}

.note-cell.empty, .moyenne-cell.empty {
  background: #f1f5f9;
  color: #64748b;
}

.empty-notes {
  text-align: center;
  padding: 40px 20px;
}

.empty-icon {
  font-size: 4rem;
  color: #cbd5e1;
  margin-bottom: 20px;
}

.empty-title {
  font-size: 1.2rem;
  color: #64748b;
  margin-bottom: 10px;
}

.empty-subtitle {
  color: #94a3b8;
  font-size: 0.95rem;
}

.legend {
  padding: 15px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.legend-badge {
  width: 40px;
  height: 25px;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.8rem;
}

.legend-badge.excellent {
  background: #d1fae5;
  color: #065f46;
}

.legend-badge.average {
  background: #fef3c7;
  color: #92400e;
}

.legend-badge.weak {
  background: #fee2e2;
  color: #991b1b;
}

.legend-badge.empty {
  background: #f1f5f9;
  color: #64748b;
}

.legend-text {
  font-size: 0.9rem;
  color: #475569;
}

/* Responsive */
@media (max-width: 768px) {
  .combined-dashboard {
    padding: 20px;
  }
  
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .notes-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .bulletin-btn {
    width: 100%;
    justify-content: center;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .trimestre-grid {
    grid-template-columns: 1fr;
  }
  
  .moyenne-generale {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }
  
  .progress-circle {
    margin: 0 auto;
  }
}
</style>