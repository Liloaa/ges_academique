<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  inscriptions: Array,
  annees: Array,
  niveaux: Array,
  filters: Object,
})

const search = ref(props.filters.search || '')
const selectedAnnee = ref(props.filters.annee_id || '')
const selectedNiveau = ref(props.filters.niveau_id || '')

const filtrer = () => {
  router.get(route('resultats.index'), {
    search: search.value,
    annee_id: selectedAnnee.value,
    niveau_id: selectedNiveau.value,
  })
}

const getAppreciationClass = (moyenne) => {
  if (moyenne >= 16) return 'text-green-600 bg-green-100'
  if (moyenne >= 14) return 'text-green-600 bg-green-50'
  if (moyenne >= 12) return 'text-blue-600 bg-blue-50'
  if (moyenne >= 10) return 'text-yellow-600 bg-yellow-50'
  if (moyenne >= 8) return 'text-orange-600 bg-orange-50'
  return 'text-red-600 bg-red-50'
}
</script>

<template>
  <Head title="Résultats Généraux" />

  <div class="p-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">📊 Résultats Généraux</h1>
      <Link
        href="/admin/resultats/statistiques"
        class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition"
      >
        📈 Statistiques
      </Link>
    </div>

    <!-- Filtres -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
      <div class="grid grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
          <input
            v-model="search"
            type="text"
            placeholder="Nom, prénom ou matricule..."
            class="w-full border border-gray-300 rounded px-3 py-2"
            @keyup.enter="filtrer"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Année scolaire</label>
          <select v-model="selectedAnnee" class="w-full border border-gray-300 rounded px-3 py-2">
            <option value="">Toutes les années</option>
            <option v-for="annee in annees" :key="annee.id" :value="annee.id">
              {{ annee.libelle }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Niveau</label>
          <select v-model="selectedNiveau" class="w-full border border-gray-300 rounded px-3 py-2">
            <option value="">Tous les niveaux</option>
            <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau.id">
              {{ niveau.nomNiveau }}
            </option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="filtrer"
            class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
          >
            🔍 Appliquer
          </button>
        </div>
      </div>
    </div>

    <!-- Tableau des résultats -->
    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3 text-left">Rang</th>
            <th class="px-4 py-3 text-left">Élève</th>
            <th class="px-4 py-3 text-left">Matricule</th>
            <th class="px-4 py-3 text-left">Niveau</th>
            <th class="px-4 py-3 text-left">Salle</th>
            <th class="px-4 py-3 text-center">Moyenne</th>
            <th class="px-4 py-3 text-center">Appréciation</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(inscription, index) in inscriptions" 
            :key="inscription.id"
            :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
            class="border-t hover:bg-blue-50 transition-colors"
          >
            <td class="px-4 py-3 text-center font-semibold">#{{ index + 1 }}</td>
            <td class="px-4 py-3 font-medium">
              {{ inscription.eleve.nom }} {{ inscription.eleve.prenom }}
            </td>
            <td class="px-4 py-3">{{ inscription.eleve.matricule }}</td>
            <td class="px-4 py-3">{{ inscription.salle.niveau.nomNiveau }}</td>
            <td class="px-4 py-3">{{ inscription.salle.nomSalle }}</td>
            <td class="px-4 py-3 text-center">
              <span class="font-bold text-lg" :class="getAppreciationClass(inscription.moyenne_generale)">
                {{ inscription.moyenne_generale }}/20
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getAppreciationClass(inscription.moyenne_generale)">
                {{ inscription.appreciation }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <Link
                :href="route('resultats.index', { eleve_id: inscription.eleve.id, annee_id: selectedAnnee })"
                class="text-blue-600 hover:text-blue-800 transition-colors"
              >
                📊 Détails
              </Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Message si pas de résultats -->
    <div v-if="inscriptions.length === 0" class="bg-white p-8 rounded-lg shadow-md text-center mt-6">
      <div class="text-gray-500 text-lg mb-4">📝</div>
      <p class="text-gray-600">Aucun résultat trouvé avec les critères sélectionnés.</p>
    </div>
  </div>
</template>