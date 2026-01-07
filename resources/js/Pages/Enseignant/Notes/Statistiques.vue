<template>
  <EnseignantLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Statistiques des notes
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Année scolaire: {{ anneeActive.annee_debut }}-{{ anneeActive.annee_fin }}
          </p>
        </div>
        <div class="flex space-x-2">
          <Link 
            :href="route('enseignant.notes.index')"
            class="btn-secondary"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour aux classes
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistiques générales -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium">Moyenne générale</p>
                <p class="text-3xl font-bold mt-2">
                  {{ moyenneGenerale.toFixed(2) }}
                </p>
                <p class="text-sm opacity-90 mt-1">sur 20</p>
              </div>
              <ChartBarIcon class="h-12 w-12 opacity-80" />
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium">Total notes saisies</p>
                <p class="text-3xl font-bold mt-2">
                  {{ totalEleves }}
                </p>
                <p class="text-sm opacity-90 mt-1">notes enregistrées</p>
              </div>
              <DocumentChartBarIcon class="h-12 w-12 opacity-80" />
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium">Matières enseignées</p>
                <p class="text-3xl font-bold mt-2">
                  {{ statistiquesMatieres.length }}
                </p>
                <p class="text-sm opacity-90 mt-1">matières</p>
              </div>
              <AcademicCapIcon class="h-12 w-12 opacity-80" />
            </div>
          </div>
        </div>

        <!-- Tableau des statistiques par matière -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
              Statistiques détaillées par matière
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Cliquez sur une matière pour voir l'historique détaillé
            </p>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Matière
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Niveau
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Notes saisies
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Moyenne
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Min/Max
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Taux réussite
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="stat in statistiquesMatieres" :key="stat.matiere.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ stat.matiere.nomMatiere }}
                    </div>
                    <div class="text-sm text-gray-500">
                      Coef. {{ stat.matiere.coefficient }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                      {{ stat.matiere.niveau.nomNiveau }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ stat.total_notes }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="text-sm font-medium text-gray-900">
                        {{ stat.moyenne.toFixed(2) }}
                      </div>
                      <div class="ml-2 w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" 
                             :style="{ width: `${(stat.moyenne / 20) * 100}%` }">
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm">
                      <span class="text-red-600 font-medium">{{ stat.note_min }}</span>
                      <span class="mx-1">/</span>
                      <span class="text-green-600 font-medium">{{ stat.note_max }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="text-sm font-medium"
                           :class="{
                             'text-green-600': stat.taux_reussite >= 50,
                             'text-yellow-600': stat.taux_reussite >= 30 && stat.taux_reussite < 50,
                             'text-red-600': stat.taux_reussite < 30
                           }">
                        {{ stat.taux_reussite }}%
                      </div>
                      <div class="ml-2 w-24 bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full"
                             :class="{
                               'bg-green-600': stat.taux_reussite >= 50,
                               'bg-yellow-600': stat.taux_reussite >= 30 && stat.taux_reussite < 50,
                               'bg-red-600': stat.taux_reussite < 30
                             }"
                             :style="{ width: `${stat.taux_reussite}%` }">
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <Link
                      :href="route('enseignant.notes.historique', stat.matiere.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Voir historique
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="statistiquesMatieres.length === 0" class="px-6 py-12 text-center">
            <ChartBarIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune statistique</h3>
            <p class="mt-1 text-sm text-gray-500">
              Aucune note n'a été saisie pour le moment.
            </p>
            <div class="mt-6">
              <Link
                :href="route('enseignant.notes.index')"
                class="btn-primary"
              >
                Commencer la saisie
              </Link>
            </div>
          </div>
        </div>

        <!-- Graphique des moyennes par matière
        <div v-if="statistiquesMatieres.length > 0" class="mt-8">
          <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Comparaison des moyennes par matière
            </h3>
            <div class="h-64 flex items-end space-x-2">
              <div v-for="(stat, index) in statistiquesMatieres" :key="stat.matiere.id"
                   class="flex-1 flex flex-col items-center">
                <div class="w-full bg-blue-100 rounded-t-lg relative group cursor-pointer"
                     :style="{ height: `${(stat.moyenne / 20) * 100}%` }"
                     :class="{
                       'bg-blue-200': index % 2 === 0,
                       'bg-blue-300': index % 3 === 0
                     }">
                  <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                    {{ stat.moyenne.toFixed(2) }}/20
                  </div>
                </div>
                <div class="mt-2 text-xs text-gray-600 text-center truncate w-full px-1">
                  {{ stat.matiere.nomMatiere }}
                </div>
              </div>
            </div>
          </div>
        </div>-->
      </div>
    </div>
  </EnseignantLayout>
</template>

<script setup>
import EnseignantLayout from '@/Layouts/EnseignantLayout.vue';
import { Link } from '@inertiajs/vue3';
import { 
  ChartBarIcon, 
  DocumentChartBarIcon, 
  AcademicCapIcon,
  ArrowLeftIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  enseignant: Object,
  anneeActive: Object,
  statistiquesMatieres: Array,
  moyenneGenerale: Number,
  totalEleves: Number,
});
</script>

<style scoped>
.btn-primary {
  @apply inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition;
}

.btn-secondary {
  @apply inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150;
}
</style>