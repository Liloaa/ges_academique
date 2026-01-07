<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <!-- En-tête -->
    <div class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
        Gestion des notes
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
</template>

<script setup>
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import EnseignantLayout from '@/Layouts/EnseignantLayout.vue'

defineOptions({ layout: EnseignantLayout })

const props = defineProps({
  enseignant: Object,
  matieres: Array,
  salles: Array,
  anneeActive: Object
})

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

/* Gradient pour les boutons */
.bg-gradient-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.bg-gradient-success {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
}
</style>