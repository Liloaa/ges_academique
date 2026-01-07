<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  eleve: Object,
  inscription: Object,
  resultats: Object,
  moyenneGenerale: Number,
  annees: Array,
  anneeCourante: Number,
})

const selectedAnnee = ref(props.anneeCourante)

const changerAnnee = () => {
  router.get(route('resultats.index', {
    eleve_id: props.eleve.id,
    annee_id: selectedAnnee.value
  }))
}

const trimestres = ['1er', '2ème', '3ème']

// Calculer le nombre de matières validées (moyenne >= 10)
const matieresValidees = computed(() => {
  return props.resultats.matieres.filter(matiere => 
    matiere.moyenne_annuelle >= 10
  ).length
})

// Obtenir l'appréciation selon la moyenne
const getAppreciation = (moyenne) => {
  if (moyenne >= 16) return 'Excellent'
  if (moyenne >= 14) return 'Très Bien'
  if (moyenne >= 12) return 'Bien'
  if (moyenne >= 10) return 'Assez Bien'
  if (moyenne >= 8) return 'Passable'
  return 'Insuffisant'
}

// Couleur selon la moyenne
const getCouleurMoyenne = (moyenne) => {
  if (moyenne >= 10) return 'text-green-600'
  if (moyenne >= 8) return 'text-yellow-600'
  return 'text-red-600'
}

// Couleur de fond selon la moyenne
const getBgCouleurMoyenne = (moyenne) => {
  if (moyenne >= 10) return 'bg-green-100 text-green-800'
  if (moyenne >= 8) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}
</script>

<template>
  <Head :title="`Résultats - ${eleve.nom} ${eleve.prenom}`" />

  <div class="p-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">📊 Résultats Scolaires</h1>
        <p class="text-gray-600">{{ eleve.nom }} {{ eleve.prenom }} - {{ eleve.matricule }}</p>
      </div>
      <div class="flex gap-4">
        <select 
          v-model="selectedAnnee" 
          @change="changerAnnee"
          class="border border-gray-300 rounded px-3 py-2"
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
        <Link
          href="/admin/resultats"
          class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition"
        >
          📊 Vue générale
        </Link>
        <Link
          href="/admin/inscriptions"
          class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>
      </div>
    </div>

    <!-- Information de l'inscription -->
    <div v-if="inscription" class="bg-white p-6 rounded-lg shadow-md mb-6">
      <div class="grid grid-cols-4 gap-4 text-sm">
        <div>
          <strong>Niveau :</strong> {{ inscription.salle.niveau.nomNiveau }}
        </div>
        <div>
          <strong>Salle :</strong> {{ inscription.salle.nomSalle }}
        </div>
        <div>
          <strong>Année scolaire :</strong> {{ inscription.annee.libelle }}
        </div>
        <div>
          <strong>Moyenne générale :</strong>
          <span class="text-lg font-bold ml-2" :class="getCouleurMoyenne(moyenneGenerale)">
            {{ moyenneGenerale }}/20
          </span>
        </div>
      </div>
    </div>

    <!-- Résumé des moyennes trimestrielles -->
    <div v-if="resultats.matieres.length > 0" class="grid grid-cols-4 gap-4 mb-6">
      <div 
        v-for="trimestre in trimestres" 
        :key="trimestre"
        class="bg-white p-4 rounded-lg shadow text-center"
      >
        <div class="text-sm text-gray-600 mb-1">{{ trimestre }} Trimestre</div>
        <div class="text-2xl font-bold" :class="getCouleurMoyenne(resultats.moyennes_trimestrielles[trimestre])">
          {{ resultats.moyennes_trimestrielles[trimestre] }}/20
        </div>
        <div class="text-xs mt-1" :class="getBgCouleurMoyenne(resultats.moyennes_trimestrielles[trimestre]) + ' px-2 py-1 rounded-full'">
          {{ getAppreciation(resultats.moyennes_trimestrielles[trimestre]) }}
        </div>
      </div>
      <div class="bg-blue-50 p-4 rounded-lg shadow text-center">
        <div class="text-sm text-blue-600 mb-1">Moyenne Générale</div>
        <div class="text-2xl font-bold text-blue-600">{{ moyenneGenerale }}/20</div>
        <div class="text-xs mt-1 bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
          {{ getAppreciation(moyenneGenerale) }}
        </div>
      </div>
    </div>

    <!-- Tableau des résultats détaillés -->
    <div v-if="resultats.matieres.length > 0" class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 text-left">Matière</th>
              <th class="px-4 py-3 text-center">Coeff.</th>
              <th 
                v-for="trimestre in trimestres" 
                :key="trimestre"
                class="px-4 py-3 text-center"
              >
                {{ trimestre }} Trim.
              </th>
              <th class="px-4 py-3 text-center">Moy. Annuelle</th>
              <th class="px-4 py-3 text-center">Appréciation</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="(resultat, index) in resultats.matieres" 
              :key="resultat.matiere.id"
              :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
              class="border-t"
            >
              <td class="px-4 py-3 font-medium">{{ resultat.matiere.nomMatiere }}</td>
              <td class="px-4 py-3 text-center">{{ resultat.coefficient }}</td>
              <td 
                v-for="trimestre in trimestres" 
                :key="trimestre"
                class="px-4 py-3 text-center"
              >
                <span 
                  v-if="resultat.trimestres[trimestre] !== null"
                  :class="getCouleurMoyenne(resultat.trimestres[trimestre])"
                >
                  {{ resultat.trimestres[trimestre] }}
                </span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="px-4 py-3 text-center font-semibold">
                <span :class="getCouleurMoyenne(resultat.moyenne_annuelle)">
                  {{ resultat.moyenne_annuelle.toFixed(2) }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="getBgCouleurMoyenne(resultat.moyenne_annuelle) + ' px-2 py-1 rounded-full text-xs'">
                  {{ getAppreciation(resultat.moyenne_annuelle) }}
                </span>
              </td>
            </tr>
          </tbody>
          <!-- Ligne des moyennes trimestrielles -->
          <tfoot class="bg-gray-100 font-semibold">
            <tr>
              <td class="px-4 py-3">Moyenne trimestrielle</td>
              <td class="px-4 py-3 text-center">-</td>
              <td 
                v-for="trimestre in trimestres" 
                :key="trimestre"
                class="px-4 py-3 text-center"
                :class="getCouleurMoyenne(resultats.moyennes_trimestrielles[trimestre])"
              >
                {{ resultats.moyennes_trimestrielles[trimestre] }}
              </td>
              <td class="px-4 py-3 text-center" :class="getCouleurMoyenne(moyenneGenerale)">
                {{ moyenneGenerale }}
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="getBgCouleurMoyenne(moyenneGenerale) + ' px-2 py-1 rounded-full text-xs'">
                  {{ getAppreciation(moyenneGenerale) }}
                </span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Message si pas de résultats -->
    <div v-else class="bg-white p-8 rounded-lg shadow-md text-center">
      <div class="text-gray-500 text-lg mb-4">📝</div>
      <p class="text-gray-600">Aucune note enregistrée pour cet élève.</p>
      <Link
        :href="route('notes.create', { inscription_id: inscription?.id })"
        class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        Saisir des notes
      </Link>
    </div>

    <!-- Résumé général -->
    <div v-if="resultats.matieres.length > 0" class="mt-6 grid grid-cols-3 gap-4">
      <div class="bg-blue-50 p-4 rounded-lg text-center">
        <div class="text-2xl font-bold text-blue-600">{{ moyenneGenerale }}/20</div>
        <div class="text-blue-800">Moyenne Générale</div>
      </div>
      <div class="bg-green-50 p-4 rounded-lg text-center">
        <div class="text-2xl font-bold text-green-600">
          {{ matieresValidees }}/{{ resultats.matieres.length }}
        </div>
        <div class="text-green-800">Matières Validées</div>
      </div>
      <div class="bg-purple-50 p-4 rounded-lg text-center">
        <div class="text-2xl font-bold text-purple-600">
          {{ getAppreciation(moyenneGenerale) }}
        </div>
        <div class="text-purple-800">Appréciation Générale</div>
      </div>
    </div>
  </div>
</template>