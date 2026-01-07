<template>
  <Head title="Mes Notes" />

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- En-tête -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                📊 Mes Notes et Résultats
              </h1>
              <p class="text-gray-600 mt-1">
                Consultation de vos notes par trimestre et de votre moyenne générale
              </p>
            </div>
            <button
              @click="router.visit(route('eleve.notes.bulletin'))"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2"
            >
              📄 Voir mon bulletin
            </button>
          </div>
        </div>
      </div>

      <!-- Informations élève -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-sm text-gray-500">Matricule</p>
              <p class="font-semibold">{{ eleve.matricule }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-sm text-gray-500">Niveau actuel</p>
              <p class="font-semibold">{{ inscriptionActive?.salle?.niveau?.nomNiveau || 'Non défini' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-sm text-gray-500">Année scolaire</p>
              <p class="font-semibold">{{ inscriptionActive?.annee?.libelle || 'Non définie' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Moyennes par trimestre -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">📈 Moyennes par trimestre</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
              v-for="(moyenne, trimestre) in resultats.moyennes_trimestrielles"
              :key="trimestre"
              class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-100"
            >
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-sm text-gray-600 uppercase tracking-wide">{{ trimestre }} Trimestre</p>
                  <p class="text-3xl font-bold text-gray-800 mt-2">{{ moyenne.toFixed(2) }}/20</p>
                </div>
                <div
                  :class="getNoteClass(moyenne)"
                  class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold"
                >
                  {{ getNoteText(moyenne) }}
                </div>
              </div>
              <p class="text-sm text-gray-500 mt-3">
                {{ getAppreciation(moyenne) }}
              </p>
            </div>
          </div>

          <!-- Moyenne générale -->
          <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-sm text-gray-600 uppercase tracking-wide">Moyenne Générale</p>
                <p class="text-4xl font-bold text-gray-800">{{ moyenneGenerale.toFixed(2) }}/20</p>
                <p class="text-gray-600 mt-1">{{ getAppreciation(moyenneGenerale) }}</p>
              </div>
              <div class="relative w-32 h-32">
                <svg class="w-full h-full" viewBox="0 0 36 36">
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
                <div class="absolute inset-0 flex items-center justify-center">
                  <span class="text-2xl font-bold" :class="getProgressTextColor(moyenneGenerale)">
                    {{ ((moyenneGenerale / 20) * 100).toFixed(0) }}%
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Détail des notes par matière -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">📚 Détail des notes par matière</h2>
          
          <div v-if="resultats.matieres.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Matière
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Coefficient
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    1er Trimestre
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    2ème Trimestre
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    3ème Trimestre
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Moyenne Annuelle
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Appréciation
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="matiere in resultats.matieres"
                  :key="matiere.matiere.id"
                  class="hover:bg-gray-50 transition-colors"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900">{{ matiere.matiere.nomMatiere }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                    {{ matiere.coefficient }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getNoteCellClass(matiere.trimestres['1er'])" class="px-2 py-1 rounded text-sm">
                      {{ matiere.trimestres['1er'] !== null ? matiere.trimestres['1er'].toFixed(2) : '-' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getNoteCellClass(matiere.trimestres['2ème'])" class="px-2 py-1 rounded text-sm">
                      {{ matiere.trimestres['2ème'] !== null ? matiere.trimestres['2ème'].toFixed(2) : '-' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getNoteCellClass(matiere.trimestres['3ème'])" class="px-2 py-1 rounded text-sm">
                      {{ matiere.trimestres['3ème'] !== null ? matiere.trimestres['3ème'].toFixed(2) : '-' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getNoteCellClass(matiere.moyenne_annuelle)" class="px-3 py-1 rounded font-medium">
                      {{ matiere.moyenne_annuelle !== null && matiere.moyenne_annuelle !== 0 ? matiere.moyenne_annuelle.toFixed(2) : '-' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                    {{ getAppreciation(matiere.moyenne_annuelle) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-else class="text-center py-12">
            <div class="text-gray-400 text-5xl mb-4">📚</div>
            <p class="text-gray-500 text-lg">Aucune note disponible pour le moment.</p>
            <p class="text-gray-400 mt-2">Vos notes apparaîtront ici une fois saisies par vos enseignants.</p>
          </div>
        </div>
      </div>

      <!-- Légende -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 border-t border-gray-200">
          <div class="flex items-center justify-center gap-6 text-sm">
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 bg-green-100 text-green-800 rounded text-xs flex items-center justify-center">10+</span>
              <span class="text-gray-600">Bonne note</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 bg-yellow-100 text-yellow-800 rounded text-xs flex items-center justify-center">8-10</span>
              <span class="text-gray-600">Note moyenne</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 bg-red-100 text-red-800 rounded text-xs flex items-center justify-center">0-8</span>
              <span class="text-gray-600">Note à améliorer</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 bg-gray-100 text-gray-800 rounded text-xs flex items-center justify-center">-</span>
              <span class="text-gray-600">Note non saisie</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import EleveLayout from '@/Layouts/EleveLayout.vue'
import { computed } from 'vue'

defineOptions({ layout: EleveLayout })

const props = defineProps({
  eleve: Object,
  inscriptionActive: Object,
  resultats: Object,
  moyenneGenerale: Number,
})

// Méthodes pour le style des notes
const getNoteClass = (note) => {
  if (note >= 16) return 'bg-gradient-to-br from-green-500 to-emerald-600'
  if (note >= 14) return 'bg-gradient-to-br from-blue-500 to-indigo-600'
  if (note >= 12) return 'bg-gradient-to-br from-indigo-400 to-purple-500'
  if (note >= 10) return 'bg-gradient-to-br from-yellow-400 to-orange-500'
  if (note >= 8) return 'bg-gradient-to-br from-orange-400 to-red-500'
  return 'bg-gradient-to-br from-red-400 to-pink-500'
}

const getNoteText = (note) => {
  if (note >= 16) return 'A+'
  if (note >= 14) return 'A'
  if (note >= 12) return 'B'
  if (note >= 10) return 'C'
  if (note >= 8) return 'D'
  return 'E'
}

const getAppreciation = (note) => {
  if (note >= 16) return 'Excellent'
  if (note >= 14) return 'Très Bien'
  if (note >= 12) return 'Bien'
  if (note >= 10) return 'Assez Bien'
  if (note >= 8) return 'Passable'
  if (note > 0) return 'Insuffisant'
  return 'Non évalué'
}

const getNoteCellClass = (note) => {
  if (note === null || note === 0) return 'bg-gray-100 text-gray-800'
  if (note >= 10) return 'bg-green-100 text-green-800'
  if (note >= 8) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

const getProgressColor = (moyenne) => {
  if (moyenne >= 16) return '#10b981'
  if (moyenne >= 14) return '#3b82f6'
  if (moyenne >= 12) return '#8b5cf6'
  if (moyenne >= 10) return '#f59e0b'
  if (moyenne >= 8) return '#f97316'
  return '#ef4444'
}

const getProgressTextColor = (moyenne) => {
  if (moyenne >= 16) return 'text-green-600'
  if (moyenne >= 14) return 'text-blue-600'
  if (moyenne >= 12) return 'text-indigo-600'
  if (moyenne >= 10) return 'text-yellow-600'
  if (moyenne >= 8) return 'text-orange-600'
  return 'text-red-600'
}
</script>