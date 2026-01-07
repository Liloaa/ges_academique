<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  statistiques: Array,
  annees: Array,
  anneeCourante: Number,
})

const selectedAnnee = ref(props.anneeCourante)

const changerAnnee = () => {
  router.get(route('resultats.statistiques', {
    annee_id: selectedAnnee.value
  }))
}

// Calculer les statistiques globales
const statsGlobales = computed(() => {
  if (!props.statistiques.length) return null

  const totalEleves = props.statistiques.reduce((sum, stat) => sum + stat.nombre_eleves, 0)
  const moyenneEtablissement = props.statistiques.reduce((sum, stat) => sum + stat.moyenne_generale, 0) / props.statistiques.length
  const meilleureMoyenne = Math.max(...props.statistiques.map(stat => stat.moyenne_max))
  const pireMoyenne = Math.min(...props.statistiques.map(stat => stat.moyenne_min))

  return {
    totalEleves,
    moyenneEtablissement: moyenneEtablissement.toFixed(2),
    meilleureMoyenne: meilleureMoyenne.toFixed(2),
    pireMoyenne: pireMoyenne.toFixed(2),
    nombreNiveaux: props.statistiques.length
  }
})

// Obtenir la couleur selon la moyenne
const getCouleurMoyenne = (moyenne) => {
  if (moyenne >= 12) return 'text-green-600'
  if (moyenne >= 10) return 'text-yellow-600'
  return 'text-red-600'
}

// Obtenir la couleur de fond selon la moyenne
const getBgCouleurMoyenne = (moyenne) => {
  if (moyenne >= 12) return 'bg-green-100 text-green-800'
  if (moyenne >= 10) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

// Grouper les statistiques par cycle
const statsParCycle = computed(() => {
  const cycles = {}
  
  props.statistiques.forEach(stat => {
    if (!cycles[stat.cycle]) {
      cycles[stat.cycle] = {
        niveaux: [],
        totalEleves: 0,
        moyenneCycle: 0,
        moyenneMax: 0,
        moyenneMin: 20
      }
    }
    
    cycles[stat.cycle].niveaux.push(stat)
    cycles[stat.cycle].totalEleves += stat.nombre_eleves
    cycles[stat.cycle].moyenneCycle += stat.moyenne_generale
    cycles[stat.cycle].moyenneMax = Math.max(cycles[stat.cycle].moyenneMax, stat.moyenne_max)
    cycles[stat.cycle].moyenneMin = Math.min(cycles[stat.cycle].moyenneMin, stat.moyenne_min)
  })

  // Calculer la moyenne du cycle
  Object.keys(cycles).forEach(cycle => {
    cycles[cycle].moyenneCycle = (cycles[cycle].moyenneCycle / cycles[cycle].niveaux.length).toFixed(2)
  })

  return cycles
})
</script>

<template>
  <Head title="Statistiques des Résultats" />

  <div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">📈 Statistiques des Résultats</h1>
        <p class="text-gray-600 text-sm md:text-base">
          Vue d'ensemble des performances académiques par niveau
        </p>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <select 
          v-model="selectedAnnee" 
          @change="changerAnnee"
          class="border border-gray-300 rounded px-3 py-2 text-sm"
        >
          <option value="">Toutes les années</option>
          <option 
            v-for="annee in annees" 
            :key="annee.id" 
            :value="annee.id"
          >
            {{ annee.libelle }}
          </option>
        </select>
        <div class="flex gap-2">
          <Link
            href="/admin/resultats"
            class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition text-sm flex-1 text-center"
          >
            📊 Résultats
          </Link>
          <Link
            href="/admin"
            class="bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600 transition text-sm flex-1 text-center"
          >
            ← Dashboard
          </Link>
        </div>
      </div>
    </div>

    <!-- Statistiques globales -->
    <div v-if="statsGlobales" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl md:text-3xl font-bold text-blue-600">{{ statsGlobales.totalEleves }}</div>
        <div class="text-blue-800 text-sm md:text-base">Élèves inscrits</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl md:text-3xl font-bold" :class="getCouleurMoyenne(statsGlobales.moyenneEtablissement)">
          {{ statsGlobales.moyenneEtablissement }}/20
        </div>
        <div class="text-gray-800 text-sm md:text-base">Moyenne établissement</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl md:text-3xl font-bold text-green-600">{{ statsGlobales.meilleureMoyenne }}/20</div>
        <div class="text-green-800 text-sm md:text-base">Meilleure moyenne</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl md:text-3xl font-bold text-red-600">{{ statsGlobales.pireMoyenne }}/20</div>
        <div class="text-red-800 text-sm md:text-base">Plus basse moyenne</div>
      </div>
    </div>

    <!-- Statistiques par cycle -->
    <div v-if="Object.keys(statsParCycle).length > 0" class="space-y-6 mb-8">
      <div 
        v-for="(cycleStats, cycle) in statsParCycle" 
        :key="cycle"
        class="bg-white rounded-lg shadow-md overflow-hidden"
      >
        <div class="bg-gray-800 text-white px-4 py-3">
          <h2 class="text-lg font-semibold">Cycle {{ cycle }}</h2>
          <p class="text-gray-300 text-sm">
            {{ cycleStats.totalEleves }} élèves - Moyenne cycle : 
            <span :class="getCouleurMoyenne(cycleStats.moyenneCycle)">
              {{ cycleStats.moyenneCycle }}/20
            </span>
          </p>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left">Niveau</th>
                <th class="px-4 py-3 text-center">Élèves</th>
                <th class="px-4 py-3 text-center">Moyenne</th>
                <th class="px-4 py-3 text-center">Moy. Max</th>
                <th class="px-4 py-3 text-center">Moy. Min</th>
                <th class="px-4 py-3 text-center">Écart</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="(stat, index) in cycleStats.niveaux" 
                :key="stat.niveau"
                :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                class="border-t"
              >
                <td class="px-4 py-3 font-medium">{{ stat.niveau }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                    {{ stat.nombre_eleves }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center font-semibold">
                  <span :class="getCouleurMoyenne(stat.moyenne_generale)">
                    {{ stat.moyenne_generale }}/20
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="text-green-600 font-semibold">
                    {{ stat.moyenne_max }}/20
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="text-red-600 font-semibold">
                    {{ stat.moyenne_min }}/20
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                    {{ (stat.moyenne_max - stat.moyenne_min).toFixed(2) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Vue détaillée par niveau (alternative) -->
    <div v-if="statistiques.length > 0" class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-gray-800 text-white px-4 py-3">
        <h2 class="text-lg font-semibold">Détail par niveau</h2>
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 text-left">Niveau</th>
              <th class="px-4 py-3 text-center">Cycle</th>
              <th class="px-4 py-3 text-center">Élèves</th>
              <th class="px-4 py-3 text-center">Moyenne</th>
              <th class="px-4 py-3 text-center">Moy. Max</th>
              <th class="px-4 py-3 text-center">Moy. Min</th>
              <th class="px-4 py-3 text-center">Performance</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="(stat, index) in statistiques" 
              :key="stat.niveau"
              :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
              class="border-t"
            >
              <td class="px-4 py-3 font-medium">{{ stat.niveau }}</td>
              <td class="px-4 py-3 text-center">
                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">
                  {{ stat.cycle }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">{{ stat.nombre_eleves }}</td>
              <td class="px-4 py-3 text-center font-semibold">
                <span :class="getCouleurMoyenne(stat.moyenne_generale)">
                  {{ stat.moyenne_generale }}/20
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="text-green-600">
                  {{ stat.moyenne_max }}/20
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="text-red-600">
                  {{ stat.moyenne_min }}/20
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span 
                  v-if="stat.moyenne_generale >= 12"
                  class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs"
                >
                  Excellente
                </span>
                <span 
                  v-else-if="stat.moyenne_generale >= 10"
                  class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs"
                >
                  Bonne
                </span>
                <span 
                  v-else
                  class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs"
                >
                  À améliorer
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Message si pas de données -->
    <div v-else class="bg-white p-8 rounded-lg shadow-md text-center">
      <div class="text-gray-500 text-lg mb-4">📊</div>
      <p class="text-gray-600">Aucune donnée statistique disponible.</p>
      <p class="text-gray-500 text-sm mt-2">
        Les statistiques seront disponibles une fois que les notes seront saisies.
      </p>
    </div>

    <!-- Insights et recommandations -->
    <div v-if="statistiques.length > 0" class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Niveaux performants -->
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="font-semibold text-green-800 mb-3">🎯 Niveaux performants</h3>
        <div class="space-y-2">
          <div 
            v-for="stat in statistiques.filter(s => s.moyenne_generale >= 12)" 
            :key="stat.niveau"
            class="flex justify-between items-center"
          >
            <span class="text-green-700">{{ stat.niveau }}</span>
            <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-sm">
              {{ stat.moyenne_generale }}/20
            </span>
          </div>
          <p v-if="statistiques.filter(s => s.moyenne_generale >= 12).length === 0" class="text-green-700 text-sm">
            Aucun niveau n'atteint actuellement une performance excellente.
          </p>
        </div>
      </div>

      <!-- Niveaux à améliorer -->
      <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <h3 class="font-semibold text-red-800 mb-3">⚠️ Attention requise</h3>
        <div class="space-y-2">
          <div 
            v-for="stat in statistiques.filter(s => s.moyenne_generale < 10)" 
            :key="stat.niveau"
            class="flex justify-between items-center"
          >
            <span class="text-red-700">{{ stat.niveau }}</span>
            <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-sm">
              {{ stat.moyenne_generale }}/20
            </span>
          </div>
          <p v-if="statistiques.filter(s => s.moyenne_generale < 10).length === 0" class="text-red-700 text-sm">
            Aucun niveau ne nécessite une attention particulière.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>