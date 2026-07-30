<template>
  <Head title="Mon Bulletin" />

  <div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
      <!-- En-tête -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 border-b border-gray-200">
          <div class="flex justify-between items-start">
            <div>
              <button
                @click="router.visit(route('eleve.notes.index'))"
                class="mb-4 px-4 py-2 text-gray-600 hover:text-gray-800 transition flex items-center gap-2"
              >
                ← Retour aux notes
              </button>
              <h1 class="text-2xl font-bold text-gray-800">📄 Mon Bulletin Scolaire</h1>
              <p class="text-gray-600">Année scolaire {{ inscriptionActive?.annee?.libelle }}</p>
            </div>
            <div class="flex gap-3">
              <button
                @click="telechargerPDF"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2"
                :disabled="telechargementEnCours"
              >
                <span v-if="telechargementEnCours">
                  <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                <span v-else>📥 Télécharger PDF</span>
              </button>
              <button
                @click="imprimerBulletin"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2"
              >
                🖨️ Imprimer
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bulletin principal -->
      <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden" id="bulletin-a-imprimer">
        <!-- En-tête du bulletin -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-6">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-2xl font-bold">BULLETIN SCOLAIRE</h2>
              <p class="text-blue-100 mt-1">Établissement Secondaire</p>
            </div>
            <div class="text-right bg-white/10 p-3 rounded-lg backdrop-blur-sm">
              <p class="text-xs opacity-90">Année Scolaire</p>
              <p class="text-lg font-bold">{{ inscriptionActive?.annee?.libelle }}</p>
            </div>
          </div>
        </div>

        <!-- Informations élève -->
        <div class="p-6 border-b border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase">INFORMATIONS DE L'ÉLÈVE</h3>
              <div class="space-y-2">
                <div class="flex text-sm">
                  <span class="w-32 text-gray-600">Nom & Prénom :</span>
                  <span class="font-medium">{{ eleve.nom }} {{ eleve.prenom }}</span>
                </div>
                <div class="flex text-sm">
                  <span class="w-32 text-gray-600">Matricule :</span>
                  <span class="font-medium">{{ eleve.matricule }}</span>
                </div>
                <div class="flex text-sm">
                  <span class="w-32 text-gray-600">Date de naissance :</span>
                  <span class="font-medium">{{ formatDate(eleve.date_naissance) }}</span>
                </div>
              </div>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase">INFORMATIONS SCOLAIRES</h3>
              <div class="space-y-2">
                <div class="flex text-sm">
                  <span class="w-32 text-gray-600">Niveau :</span>
                  <span class="font-medium">{{ inscriptionActive?.salle?.niveau?.nomNiveau }}</span>
                </div>
                <div class="flex text-sm">
                  <span class="w-32 text-gray-600">Classe :</span>
                  <span class="font-medium">{{ inscriptionActive?.salle?.nomSalle }}</span>
                </div>
                <div class="flex text-sm">
                  <span class="w-32 text-gray-600">Cycle :</span>
                  <span class="font-medium">{{ inscriptionActive?.salle?.niveau?.cycle }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notes détaillées -->
        <div class="p-6">
          <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase">RÉSULTATS SCOLAIRES</h3>
          
          <!-- Tableau des notes -->
          <div class="overflow-x-auto mb-4">
            <table class="min-w-full border border-gray-300 text-xs">
              <thead class="bg-gray-50">
                <tr>
                  <th class="border border-gray-300 px-2 py-2 text-left font-medium text-gray-700">
                    Matière
                  </th>
                  <th class="border border-gray-300 px-2 py-2 text-center font-medium text-gray-700">
                    Coef.
                  </th>
                  <th class="border border-gray-300 px-2 py-2 text-center font-medium text-gray-700">
                    1er Trim.
                  </th>
                  <th class="border border-gray-300 px-2 py-2 text-center font-medium text-gray-700">
                    2ème Trim.
                  </th>
                  <th class="border border-gray-300 px-2 py-2 text-center font-medium text-gray-700">
                    3ème Trim.
                  </th>
                  <th class="border border-gray-300 px-2 py-2 text-center font-medium text-gray-700">
                    Moy. Ann.
                  </th>
                  <th class="border border-gray-300 px-2 py-2 text-center font-medium text-gray-700">
                    Appréciation
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="matiere in resultats.matieres"
                  :key="matiere.matiere.id"
                  class="hover:bg-gray-50"
                >
                  <td class="border border-gray-300 px-2 py-2 font-medium">
                    {{ matiere.matiere.nomMatiere }}
                  </td>
                  <td class="border border-gray-300 px-2 py-2 text-center">
                    {{ matiere.coefficient }}
                  </td>
                  <td class="border border-gray-300 px-2 py-2 text-center">
                    {{ matiere.trimestres['1er'] !== null ? matiere.trimestres['1er'].toFixed(2) : '-' }}
                  </td>
                  <td class="border border-gray-300 px-2 py-2 text-center">
                    {{ matiere.trimestres['2ème'] !== null ? matiere.trimestres['2ème'].toFixed(2) : '-' }}
                  </td>
                  <td class="border border-gray-300 px-2 py-2 text-center">
                    {{ matiere.trimestres['3ème'] !== null ? matiere.trimestres['3ème'].toFixed(2) : '-' }}
                  </td>
                  <td class="border border-gray-300 px-2 py-2 text-center font-bold">
                    {{ matiere.moyenne_annuelle !== null && matiere.moyenne_annuelle !== 0 ? matiere.moyenne_annuelle.toFixed(2) : '-' }}
                  </td>
                  <td class="border border-gray-300 px-2 py-2 text-center text-xs">
                    {{ getAppreciation(matiere.moyenne_annuelle) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Résumé des moyennes -->
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div
              v-for="(moyenne, trimestre) in resultats.moyennes_trimestrielles"
              :key="trimestre"
              class="bg-gray-50 p-3 rounded border border-gray-200"
            >
              <p class="text-xs text-gray-600 mb-1">Moyenne {{ trimestre }}</p>
              <div class="flex items-center justify-between">
                <p class="text-lg font-bold text-gray-800">{{ moyenne.toFixed(2) }}/20</p>
                <span :class="getNoteBadgeClass(moyenne)" class="px-1 py-0.5 rounded text-xs font-medium">
                  {{ getNoteBadgeText(moyenne) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Moyenne générale et appréciation -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded border border-blue-200">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-xs text-gray-600 uppercase tracking-wide">MOYENNE GÉNÉRALE</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ moyenneGenerale.toFixed(2) }}/20</p>
                <p class="text-gray-600 text-xs mt-1">Sur la base des trois trimestres</p>
              </div>
              <div class="text-center">
                <div :class="getAppreciationClass(appreciation)" class="px-4 py-2 rounded">
                  <p class="text-lg font-bold text-white">{{ appreciation }}</p>
                  <p class="text-white/90 text-xs mt-0.5">Appréciation générale</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pied de page -->
        <div class="p-6 border-t border-gray-200 bg-gray-50">
          <div class="grid grid-cols-3 gap-4 text-center">
            <div>
              <p class="text-xs text-gray-600 mb-1">Le Chef d'Établissement</p>
              <div class="border-t border-gray-300 pt-2 mt-1">
                <p class="text-gray-500 text-xs">Signature et cachet</p>
              </div>
            </div>
            <div>
              <p class="text-xs text-gray-600 mb-1">Le Responsable Pédagogique</p>
              <div class="border-t border-gray-300 pt-2 mt-1">
                <p class="text-gray-500 text-xs">Signature</p>
              </div>
            </div>
            <div>
              <p class="text-xs text-gray-600 mb-1">Les Parents/Responsable</p>
              <div class="border-t border-gray-300 pt-2 mt-1">
                <p class="text-gray-500 text-xs">Signature</p>
              </div>
            </div>
          </div>
          <div class="mt-4 pt-3 border-t border-gray-300 text-center">
            <p class="text-gray-500 text-xs">Bulletin généré le {{ new Date().toLocaleDateString('fr-FR') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import EleveLayout from '@/Layouts/EleveLayout.vue'
import { ref } from 'vue'

defineOptions({ layout: EleveLayout })

const props = defineProps({
  eleve: Object,
  inscriptionActive: Object,
  resultats: Object,
  moyenneGenerale: Number,
  appreciation: String,
})

const telechargementEnCours = ref(false)

// Méthode de téléchargement en PDF
// Le PDF est désormais généré côté serveur (DomPDF) via BulletinService,
// ce qui garantit un rendu identique à celui utilisé par l'administration
// et permet une génération en masse (ex: tous les bulletins d'une classe).
const telechargerPDF = async () => {
  try {
    telechargementEnCours.value = true

    const response = await fetch(route('eleve.notes.bulletin.pdf'), {
      headers: { Accept: 'application/pdf' },
    })

    if (!response.ok) {
      throw new Error('Le serveur n\'a pas pu générer le bulletin.')
    }

    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const lien = document.createElement('a')
    lien.href = url
    lien.download = `bulletin_${props.eleve.nom}_${props.eleve.prenom}_${props.inscriptionActive?.annee?.libelle || 'annee_scolaire'}.pdf`
    document.body.appendChild(lien)
    lien.click()
    lien.remove()
    window.URL.revokeObjectURL(url)

  } catch (error) {
    console.error('Erreur lors de la génération du PDF:', error)
    alert('Erreur lors de la génération du PDF. Veuillez réessayer.')
  } finally {
    telechargementEnCours.value = false
  }
}

// Méthode d'impression (existante)
const imprimerBulletin = () => {
  const printContent = document.getElementById('bulletin-a-imprimer').innerHTML
  const originalContent = document.body.innerHTML
  
  document.body.innerHTML = printContent
  window.print()
  document.body.innerHTML = originalContent
  window.location.reload()
}

// Formater la date (existante)
const formatDate = (dateString) => {
  if (!dateString) return 'Non renseignée'
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR')
}

// Méthodes pour le style (existantes)
const getNoteBadgeClass = (note) => {
  if (note >= 16) return 'bg-green-100 text-green-800'
  if (note >= 14) return 'bg-blue-100 text-blue-800'
  if (note >= 12) return 'bg-indigo-100 text-indigo-800'
  if (note >= 10) return 'bg-yellow-100 text-yellow-800'
  if (note >= 8) return 'bg-orange-100 text-orange-800'
  return 'bg-red-100 text-red-800'
}

const getNoteBadgeText = (note) => {
  if (note >= 16) return 'Excellent'
  if (note >= 14) return 'Très bien'
  if (note >= 12) return 'Bien'
  if (note >= 10) return 'Assez bien'
  if (note >= 8) return 'Passable'
  return 'Insuffisant'
}

const getAppreciationClass = (appreciation) => {
  switch (appreciation) {
    case 'Excellent': return 'bg-gradient-to-r from-green-500 to-emerald-600'
    case 'Très Bien': return 'bg-gradient-to-r from-blue-500 to-indigo-600'
    case 'Bien': return 'bg-gradient-to-r from-indigo-400 to-purple-500'
    case 'Assez Bien': return 'bg-gradient-to-r from-yellow-400 to-orange-500'
    case 'Passable': return 'bg-gradient-to-r from-orange-400 to-red-500'
    default: return 'bg-gradient-to-r from-red-400 to-pink-500'
  }
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
</script>