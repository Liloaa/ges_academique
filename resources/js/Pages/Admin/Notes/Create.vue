<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  inscription: Object,
  matieres: Array,
  enseignants: Array,
  trimestre: String,
  trimestres: Array,
  notesExistantes: Object,
})

// Initialiser le formulaire avec les matières
const form = reactive({
  inscription_id: props.inscription.id,
  trimestre: props.trimestre,
  notes: props.matieres.map(matiere => {
    const noteExistante = props.notesExistantes[matiere.id]
    return {
      matiere_id: matiere.id,
      matiere_nom: matiere.nomMatiere,
      coefficient: matiere.coefficient,
      note: noteExistante ? noteExistante.note : '',
      enseignant_id: noteExistante ? noteExistante.enseignant_id : '',
      note_id: noteExistante ? noteExistante.id : null
  }})
})

const errors = reactive({})
const isSubmitting = ref(false)

const submit = () => {
  isSubmitting.value = true
  router.post(route('notes.store'), form, {
    onSuccess: () => {
      isSubmitting.value = false
    },
    onError: (e) => {
      isSubmitting.value = false
      Object.assign(errors, e)
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

// Calculer la moyenne générale (pour affichage)
const moyenneGenerale = computed(() => {
  const notesAvecCoefficient = form.notes.filter(n => n.note && n.note !== '')
  if (notesAvecCoefficient.length === 0) return 0

  const totalPoints = notesAvecCoefficient.reduce((sum, n) => sum + (parseFloat(n.note) * n.coefficient), 0)
  const totalCoefficients = notesAvecCoefficient.reduce((sum, n) => sum + n.coefficient, 0)
  
  return totalCoefficients > 0 ? (totalPoints / totalCoefficients).toFixed(2) : 0
})

const nombreNotesSaisies = computed(() => {
  return form.notes.filter(n => n.note && n.note !== '').length
})
</script>

<template>
  <Head :title="`Saisie des notes - ${inscription.eleve.nom} ${inscription.eleve.prenom}`" />

  <div class="p-8 max-w-4xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 mb-2">
            📝 Saisie des notes
          </h1>
          <div class="space-y-1 text-gray-600">
            <p><strong>Élève :</strong> {{ inscription.eleve.nom }} {{ inscription.eleve.prenom }}</p>
            <p><strong>Niveau :</strong> {{ inscription.salle.niveau.nomNiveau }}</p>
            <p><strong>Salle :</strong> {{ inscription.salle.nomSalle }}</p>
          </div>
        </div>
        <div class="text-right">
          <div class="bg-blue-50 p-3 rounded-lg">
            <p class="text-sm text-blue-800">Trimestre</p>
            <select 
              v-model="form.trimestre" 
              class="bg-transparent border-none text-blue-800 font-bold text-lg focus:ring-0"
            >
              <option v-for="t in trimestres" :key="t" :value="t">
                {{ t }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="grid grid-cols-3 gap-4 mt-4">
        <div class="bg-gray-50 p-3 rounded text-center">
          <div class="text-2xl font-bold text-gray-800">{{ nombreNotesSaisies }}</div>
          <div class="text-sm text-gray-600">Notes saisies</div>
        </div>
        <div class="bg-green-50 p-3 rounded text-center">
          <div class="text-2xl font-bold text-green-600">{{ moyenneGenerale }}/20</div>
          <div class="text-sm text-green-600">Moyenne générale</div>
        </div>
        <div class="bg-blue-50 p-3 rounded text-center">
          <div class="text-2xl font-bold text-blue-600">{{ matieres.length }}</div>
          <div class="text-sm text-blue-600">Matières totales</div>
        </div>
      </div>
    </div>

    <!-- Formulaire de saisie -->
    <form @submit.prevent="submit" class="bg-white shadow-md rounded-lg overflow-hidden">
      <!-- En-tête du tableau -->
      <div class="bg-gray-100 px-6 py-3 border-b">
        <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-800">
          <div class="col-span-5">Matière</div>
          <div class="col-span-1 text-center">Coeff.</div>
          <div class="col-span-3 text-center">Note /20</div>
          <div class="col-span-3">Enseignant</div>
        </div>
      </div>

      <!-- Liste des matières -->
      <div class="divide-y">
        <div 
          v-for="(noteItem, index) in form.notes" 
          :key="noteItem.matiere_id"
          class="px-6 py-4 hover:bg-gray-50 transition-colors"
        >
          <div class="grid grid-cols-12 gap-4 items-center">
            <!-- Matière -->
            <div class="col-span-5">
              <label class="font-medium text-gray-900">
                {{ noteItem.matiere_nom }}
              </label>
            </div>

            <!-- Coefficient -->
            <div class="col-span-1 text-center">
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-medium">
                {{ noteItem.coefficient }}
              </span>
            </div>

            <!-- Note -->
            <div class="col-span-3">
              <div class="flex items-center space-x-2">
                <input
                  v-model="noteItem.note"
                  type="number"
                  step="0.01"
                  min="0"
                  max="20"
                  placeholder="0.00"
                  class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-500': errors[`notes.${index}.note`] }"
                />
                <span class="text-gray-500 whitespace-nowrap">/20</span>
              </div>
              <div v-if="errors[`notes.${index}.note`]" class="text-red-600 text-sm mt-1">
                {{ errors[`notes.${index}.note`] }}
              </div>
            </div>

            <!-- Enseignant -->
            <div class="col-span-3">
              <select
                v-model="noteItem.enseignant_id"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Sélectionner un enseignant</option>
                <option 
                  v-for="enseignant in enseignants" 
                  :key="enseignant.id" 
                  :value="enseignant.id"
                >
                  {{ enseignant.nom }} {{ enseignant.prenom }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Pied de page -->
      <div class="bg-gray-50 px-6 py-4 border-t">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-600">
            {{ nombreNotesSaisies }} / {{ matieres.length }} matières renseignées
          </div>
          <div class="flex space-x-3">
            <Link
              :href="route('inscriptions.index', { inscription_id: inscription.id })"
              class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition"
            >
              ← Retour
            </Link>
            <Link
              :href="route('notes.index', { inscription_id: inscription.id })"
              class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition"
            >
              Tout les notes
            </Link>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50"
            >
              {{ isSubmitting ? 'Enregistrement...' : '💾 Enregistrer les notes' }}
            </button>
          </div>
        </div>
      </div>
    </form>

    <!-- Instructions -->
    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
      <h3 class="font-semibold text-yellow-800 mb-2">💡 Instructions</h3>
      <ul class="text-sm text-yellow-700 space-y-1">
        <li>• Saisissez les notes sur 20 pour chaque matière</li>
        <li>• Les notes sont arrondies à 2 décimales</li>
        <li>• Les coefficients sont utilisés pour le calcul de la moyenne</li>
        <li>• Vous pouvez laisser une matière vide si aucune note n'est disponible</li>
      </ul>
    </div>
  </div>
</template>