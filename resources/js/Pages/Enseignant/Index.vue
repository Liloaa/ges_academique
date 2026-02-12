<template>
  <EnseignantLayout>
    <div class="main-content">
      <!-- ==================== -->
      <!-- PREMIÈRE INTERFACE (Dashboard) -->
      <!-- ==================== -->
      <!-- En-tête de l'enseignant -->
      <section class="enseignant-header" v-if="enseignant">
        <div class="enseignant-info">
          <div class="avatar">
            {{ enseignantInitiales }}
          </div>
          <div class="info">
            <h1>Bienvenue, {{ enseignant.prenom }} {{ enseignant.nom }}</h1>
            <p class="specialite">{{ enseignant.specialite || 'Enseignant' }}</p>
          </div>
        </div>
      </section>

      <!-- Statistiques de l'enseignant -->
      <section class="stats-section">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-header">
              <h3>Matieres Enseignées</h3>
              <span class="stat-icon">📚</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques?.totalMatieres || 0 }}</div>
              <p class="stat-desc">Matieres différentes</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <h3>Élèves</h3>
              <span class="stat-icon">👨‍🎓</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques?.totalEleves || 0 }}</div>
              <p class="stat-desc">Élèves au total</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <h3>Niveaux</h3>
              <span class="stat-icon">🏫</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques?.totalNiveaux || 0 }}</div>
              <p class="stat-desc">Niveaux différents</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Liste des classes attribuées -->
      <section class="classes-section">
        <div class="section-header">
          <h2>Mes Classes Attribuées</h2>
        </div>
        
        <div v-if="!classes || classes.length === 0" class="empty-state">
          <p>Aucune classe attribuée pour le moment.</p>
          <p>Contactez l'administration pour vous assigner des classes.</p>
        </div>

        <div v-else class="classes-grid">
          <div v-for="classe in classes" :key="classe.niveau?.id" class="class-card">
            <div class="class-header">
              <div class="class-title">
                <h3>{{ classe.niveau?.nom || 'N/A' }}</h3>
                <span class="cycle-badge">{{ classe.niveau?.cycle || 'N/A' }}</span>
              </div>
              <div class="class-info">
                <div class="info-item">
                  <span class="info-icon">🏫</span>
                  <span>Salle: {{ classe.salle?.nom || 'N/A' }}</span>
                </div>
                <div class="info-item">
                  <span class="info-icon">👥</span>
                  <span>Effectif: {{ classe.effectif || 0 }}/{{ classe.salle?.capacite || 0 }}</span>
                </div>
                <div v-if="classe.niveau?.filiere" class="info-item">
                  <span class="info-icon">📊</span>
                  <span>Filière: {{ classe.niveau.filiere }}</span>
                </div>
              </div>
            </div>

            <div class="class-body">
              <h4>Matieres enseignées :</h4>
              <div class="matieres-list">
                <div v-for="matiere in classe.matieres" :key="matiere.id" class="matiere-item">
                  <div class="matiere-name">{{ matiere.nom }}</div>
                  <div class="matiere-coeff">Coeff. {{ matiere.coefficient }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ==================== -->
      <!-- DEUXIÈME INTERFACE (Gestion des notes) -->
      <!-- ==================== -->
      <div class="gestion-notes-section">
        <!-- En-tête -->
        <div class="gestion-header mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
            Gestion des notes des élèves
          </h1>
          <p class="mt-2 text-gray-600">
            Bienvenue, {{ enseignant.prenom }} {{ enseignant.nom }}
            <span v-if="anneeActive" class="block text-sm text-indigo-600 mt-1">
              Année scolaire : {{ anneeActive.libelle }}
            </span>
          </p>
        </div>

        <!-- Boutons d'action -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <button
            @click="goToStatistiques"
            class="flex items-center justify-center gap-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-4 rounded-lg shadow hover:shadow-md transition"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="font-medium">Statistiques générales</span>
          </button>

          <a
            :href="route('enseignant.notes.historique', { matiere: matieres[0]?.id })"
            v-if="matieres.length > 0"
            class="flex items-center justify-center gap-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-4 rounded-lg shadow hover:shadow-md transition"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span class="font-medium">Historique des notes</span>
          </a>

          <div class="bg-white p-4 rounded-lg shadow flex items-center justify-center">
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">{{ totalClasses }}</div>
              <div class="text-sm text-gray-600">Classes assignées</div>
            </div>
          </div>
        </div>

        <!-- Section Matières enseignées -->
        <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden">
          <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              Matières enseignées
            </h2>
          </div>
          
          <div class="p-6">
            <div v-if="matieres.length === 0" class="text-center py-8">
              <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="mt-4 text-gray-500">Aucune matière assignée pour le moment.</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="matiere in matieres"
                :key="matiere.id"
                class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:shadow-md transition"
              >
                <div class="flex justify-between items-start mb-3">
                  <h3 class="font-bold text-lg text-gray-800">{{ matiere.nomMatiere }}</h3>
                  <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                    Coef. {{ matiere.coefficient }}
                  </span>
                </div>
                
                <div class="flex items-center text-gray-600 text-sm mb-4">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  <span>{{ matiere.niveau?.nom || 'Niveau non spécifié' }}</span>
                  <span class="mx-2">•</span>
                  <span class="capitalize">{{ matiere.niveau?.cycle }}</span>
                </div>

                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-500">
                    {{ getClassesForMatiere(matiere.id).length }} classes
                  </span>
                  <a
                    :href="route('enseignant.notes.historique', { matiere: matiere.id })"
                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center gap-1"
                  >
                    Historique
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section Classes assignées -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Mes classes
            </h2>
          </div>
          
          <div class="p-6">
            <div v-if="salles.length === 0" class="text-center py-8">
              <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 0A21.066 21.066 0 0012 13.5c-3.195 0-6.218-.804-9-2.213" />
              </svg>
              <p class="mt-4 text-gray-500">Aucune classe assignée pour le moment.</p>
            </div>

            <div v-else class="space-y-6">
              <div
                v-for="salle in salles"
                :key="salle.id"
                class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition"
              >
                <!-- En-tête de la classe -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                  <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                      <h3 class="text-lg font-bold text-gray-900">{{ salle.nomSalle }}</h3>
                      <div class="flex items-center mt-1 text-gray-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ salle.niveau?.nom }}</span>
                        <span class="mx-2">•</span>
                        <span class="capitalize">{{ salle.niveau?.cycle }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ salle.niveau?.filiere?.nom || 'Général' }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Matières dans cette classe -->
                <div class="p-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                      v-for="matiere in salle.matières_enseignees"
                      :key="matiere.id"
                      class="border border-gray-100 rounded-lg p-4 bg-white hover:border-indigo-200 hover:shadow-sm transition"
                    >
                      <div class="flex justify-between items-start mb-3">
                        <h4 class="font-bold text-gray-800">{{ matiere.nomMatiere }}</h4>
                        <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-0.5 rounded">
                          Coef. {{ matiere.coefficient }}
                        </span>
                      </div>
                      
                      <div class="flex items-center justify-between mt-4">
                        <div class="flex gap-2">
                          <a
                            :href="route('enseignant.notes.eleves', { salle: salle.id, matiere: matiere.id })"
                            class="inline-flex items-center gap-1 px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Saisir les notes
                          </a>
                          
                          <a
                            :href="route('enseignant.notes.historique', { matiere: matiere.id })"
                            class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Historique
                          </a>
                        </div>
                        
                        <div class="text-xs text-gray-500">
                          <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Trimestres : 
                            <span class="ml-1 font-medium">1er, 2ème, 3ème</span>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pied de page informatif -->
        <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
          <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <h3 class="font-bold text-blue-900 mb-2">Instructions importantes</h3>
              <ul class="text-blue-800 space-y-2">
                <li class="flex items-start">
                  <span class="mr-2">•</span>
                  <span>Les notes doivent être comprises entre 0 et 20</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">•</span>
                  <span>Vous ne pouvez saisir qu'une seule note par élève, par matière et par trimestre</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">•</span>
                  <span>Les notes sont modifiables à tout moment avant la clôture du trimestre</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">•</span>
                  <span>N'oubliez pas d'enregistrer vos saisies après chaque modification</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </EnseignantLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import EnseignantLayout from '@/Layouts/EnseignantLayout.vue'

// Props combinées des deux interfaces
const props = defineProps({
  // Props de la première interface
  classes: {
    type: Array,
    default: () => []
  },
  statistiques: {
    type: Object,
    default: () => ({})
  },
  prochainesEvaluations: {
    type: Array,
    default: () => []
  },
  enseignant: {
    type: Object,
    default: () => ({})
  },
  // Props de la deuxième interface
  matieres: {
    type: Array,
    default: () => []
  },
  salles: {
    type: Array,
    default: () => []
  },
  anneeActive: {
    type: Object,
    default: () => ({})
  }
})

// Calculer les initiales de l'enseignant (de la première interface)
const enseignantInitiales = computed(() => {
  if (!props.enseignant || !props.enseignant.prenom) return 'EN'
  return (props.enseignant.prenom?.charAt(0) || '') + (props.enseignant.nom?.charAt(0) || '')
})

// Formater la date (de la première interface)
const formatDate = (dateString, format) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  if (format === 'DD') {
    return date.getDate().toString().padStart(2, '0')
  }
  if (format === 'MMM') {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc']
    return months[date.getMonth()]
  }
  return date.toLocaleDateString('fr-FR')
}

// ====================
// FONCTIONS DE LA DEUXIÈME INTERFACE
// ====================

// Calculer le nombre total de classes
const totalClasses = computed(() => {
  return props.salles.length
})

// Fonction pour récupérer les classes pour une matière donnée
const getClassesForMatiere = (matiereId) => {
  return props.salles.filter(salle => 
    salle.matieres_enseignees?.some(m => m.id === matiereId)
  )
}

// Navigation vers les statistiques
const goToStatistiques = () => {
  router.get(route('enseignant.notes.statistiques'))
}
</script>

<style scoped>
/* Styles de la première interface */
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

/* En-tête enseignant */
.enseignant-header {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 12px;
  padding: 25px;
  color: white;
}

.enseignant-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.avatar {
  width: 70px;
  height: 70px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  font-weight: 600;
}

.info h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 600;
}

.specialite {
  margin: 5px 0 0;
  opacity: 0.9;
  font-size: 1rem;
}

/* Statistiques */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

.stat-icon {
  font-size: 1.5rem;
}

.stat-content {
  text-align: center;
}

.stat-number {
  font-size: 2.2rem;
  font-weight: 700;
  color: #2d3748;
  margin: 10px 0;
}

.stat-desc {
  color: #718096;
  font-size: 0.9rem;
}

/* Section des classes */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h2 {
  color: #2d3748;
  font-size: 1.5rem;
  margin: 0;
}

.classes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 20px;
}

.class-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.class-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
}

.class-header {
  padding: 20px;
  background: linear-gradient(135deg, #f6f8ff, #edf2f7);
  border-bottom: 1px solid #e2e8f0;
}

.class-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.class-title h3 {
  margin: 0;
  color: #2d3748;
  font-size: 1.3rem;
}

.cycle-badge {
  background: #667eea;
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: uppercase;
}

.class-info {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #4a5568;
  font-size: 0.9rem;
}

.info-icon {
  font-size: 1rem;
  opacity: 0.7;
}

.class-body {
  padding: 20px;
}

.class-body h4 {
  margin: 0 0 15px;
  color: #4a5568;
  font-size: 1rem;
}

.matieres-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.matiere-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 15px;
  background: #f7fafc;
  border-radius: 8px;
  border-left: 4px solid #667eea;
}

.matiere-name {
  font-weight: 500;
  color: #2d3748;
}

.matiere-coeff {
  background: #e2e8f0;
  color: #4a5568;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: 500;
}

/* États vides */
.empty-state {
  text-align: center;
  padding: 40px;
  color: #a0aec0;
}

.empty-state p {
  margin: 10px 0;
}

/* ==================== */
/* Styles pour la section gestion des notes */
/* ==================== */
.gestion-notes-section {
  margin-top: 40px;
  padding-top: 40px;
  border-top: 2px solid #e2e8f0;
}

/* Style pour les liens de navigation */
a {
  transition: all 0.2s ease;
}

/* Animation pour les cartes */
.transition {
  transition: all 0.3s ease;
}

/* Style pour les badges */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
  .grid {
    grid-template-columns: 1fr;
  }
  
  .flex-col-mobile {
    flex-direction: column;
  }
  
  .text-center-mobile {
    text-align: center;
  }
  
  .classes-grid,
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>