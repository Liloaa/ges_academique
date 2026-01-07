<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  inscription: Object,
  matieres: Array,
  enseignants: Array,
  trimestres: Array,
  notesGrouped: Object,
  trimestreActuel: String,
})

const form = reactive({
  inscription_id: props.inscription.id,
  notes: []
})

// Initialiser le formulaire avec toutes les notes existantes
const initialiserNotes = () => {
  props.matieres.forEach(matiere => {
    // Chercher la note pour chaque trimestre
    props.trimestres.forEach(trimestre => {
      const noteExistante = props.notesGrouped[trimestre]?.find(n => n.matiere_id === matiere.id)
      form.notes.push({
        id: noteExistante?.id || null,
        matiere_id: matiere.id,
        matiere_nom: matiere.nomMatiere,
        coefficient: matiere.coefficient,
        trimestre: trimestre,
        note: noteExistante ? noteExistante.note : '',
        enseignant_id: noteExistante ? noteExistante.enseignant_id : '',
      })
    })
  })
}

initialiserNotes()

const errors = reactive({})
const isSubmitting = ref(false)
const selectedTrimestre = ref(props.trimestreActuel)

const submit = () => {
  isSubmitting.value = true
  
  // Filtrer seulement les notes du trimestre sélectionné si on veut mettre à jour un trimestre spécifique
  const notesToSubmit = selectedTrimestre.value 
    ? form.notes.filter(n => n.trimestre === selectedTrimestre.value)
    : form.notes

  const submitData = {
    inscription_id: form.inscription_id,
    notes: notesToSubmit
  }

  router.put(route('notes.update', form.inscription_id), submitData, {
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

// Filtrer les notes par trimestre
const notesFiltrees = computed(() => {
  return form.notes.filter(note => note.trimestre === selectedTrimestre.value)
})

// Calculer la moyenne pour le trimestre sélectionné
const moyenneTrimestre = computed(() => {
  const notesAvecCoefficient = notesFiltrees.value.filter(n => n.note && n.note !== '')
  if (notesAvecCoefficient.length === 0) return 0

  const totalPoints = notesAvecCoefficient.reduce((sum, n) => sum + (parseFloat(n.note) * n.coefficient), 0)
  const totalCoefficients = notesAvecCoefficient.reduce((sum, n) => sum + n.coefficient, 0)
  
  return totalCoefficients > 0 ? (totalPoints / totalCoefficients).toFixed(2) : 0
})

const nombreNotesSaisies = computed(() => {
  return notesFiltrees.value.filter(n => n.note && n.note !== '').length
})

// Obtenir l'appréciation
const appreciation = computed(() => {
  const moyenne = parseFloat(moyenneTrimestre.value)
  if (moyenne >= 16) return 'Excellent'
  if (moyenne >= 14) return 'Très Bien'
  if (moyenne >= 12) return 'Bien'
  if (moyenne >= 10) return 'Assez Bien'
  if (moyenne >= 8) return 'Passable'
  return 'Insuffisant'
})

const couleurAppreciation = computed(() => {
  const moyenne = parseFloat(moyenneTrimestre.value)
  if (moyenne >= 10) return 'text-green-600 bg-green-100'
  if (moyenne >= 8) return 'text-yellow-600 bg-yellow-100'
  return 'text-red-600 bg-red-100'
})
</script>

<template>
  <Head :title="`Modification des notes - ${inscription.eleve.nom} ${inscription.eleve.prenom}`" />

  <div class="p-8 max-w-6xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 mb-2">
            ✏️ Modification des notes
          </h1>
          <div class="space-y-1 text-gray-600">
            <p><strong>Élève :</strong> {{ inscription.eleve.nom }} {{ inscription.eleve.prenom }}</p>
            <p><strong>Niveau :</strong> {{ inscription.salle.niveau.nomNiveau }}</p>
            <p><strong>Salle :</strong> {{ inscription.salle.nomSalle }}</p>
            <p><strong>Année scolaire :</strong> {{ inscription.annee.libelle }}</p>
          </div>
        </div>
        <div class="text-right space-y-2">
          <!-- Sélecteur de trimestre -->
          <div class="bg-blue-50 p-3 rounded-lg">
            <p class="text-sm text-blue-800 mb-1">Trimestre</p>
            <select 
              v-model="selectedTrimestre" 
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
      <div class="grid grid-cols-4 gap-4 mt-4">
        <div class="bg-gray-50 p-3 rounded text-center">
          <div class="text-2xl font-bold text-gray-800">{{ nombreNotesSaisies }}</div>
          <div class="text-sm text-gray-600">Notes saisies</div>
        </div>
        <div class="bg-green-50 p-3 rounded text-center">
          <div class="text-2xl font-bold text-green-600">{{ moyenneTrimestre }}/20</div>
          <div class="text-sm text-green-600">Moyenne</div>
        </div>
        <div class="bg-blue-50 p-3 rounded text-center">
          <div class="text-2xl font-bold text-blue-600">{{ matieres.length }}</div>
          <div class="text-sm text-blue-600">Matières</div>
        </div>
        <div class="p-3 rounded text-center" :class="couleurAppreciation">
          <div class="text-lg font-bold">{{ appreciation }}</div>
          <div class="text-sm">Appréciation</div>
        </div>
      </div>
    </div>

    <!-- Formulaire de modification -->
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
          v-for="(noteItem, index) in notesFiltrees" 
          :key="`${noteItem.matiere_id}-${noteItem.trimestre}`"
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
                  :class="{ 
                    'border-green-300 bg-green-50': noteItem.note && noteItem.note >= 10,
                    'border-red-300 bg-red-50': noteItem.note && noteItem.note < 10,
                    'border-red-500': errors[`notes.${index}.note`] 
                  }"
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
            {{ nombreNotesSaisies }} / {{ matieres.length }} matières renseignées pour le {{ selectedTrimestre }} trimestre
          </div>
          <div class="flex space-x-3">
            <Link
              :href="route('notes.index', { inscription_id: inscription.id })"
              class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition"
            >
              ← Retour
            </Link>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition disabled:opacity-50"
            >
              {{ isSubmitting ? 'Mise à jour...' : '✅ Mettre à jour' }}
            </button>
          </div>
        </div>
      </div>
    </form>

    <!-- Navigation entre trimestres -->
    <div class="mt-6 bg-white shadow-md rounded-lg p-4">
      <h3 class="font-semibold text-gray-800 mb-3">📅 Navigation par trimestre</h3>
      <div class="flex space-x-2">
        <button
          v-for="trimestre in trimestres"
          :key="trimestre"
          @click="selectedTrimestre = trimestre"
          class="px-4 py-2 rounded transition"
          :class="selectedTrimestre === trimestre 
            ? 'bg-blue-600 text-white' 
            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
        >
          {{ trimestre }} trimestre
        </button>
      </div>
    </div>

    <!-- Résumé des autres trimestres -->
    <div class="mt-6 grid grid-cols-3 gap-4">
      <div 
        v-for="trimestre in trimestres.filter(t => t !== selectedTrimestre)" 
        :key="trimestre"
        class="bg-white shadow rounded-lg p-4"
      >
        <h4 class="font-semibold text-gray-800 mb-2">{{ trimestre }} trimestre</h4>
        <div v-if="notesGrouped[trimestre]">
          <p class="text-sm text-gray-600">
            {{ notesGrouped[trimestre].length }} note(s) saisie(s)
          </p>
          <button
            @click="selectedTrimestre = trimestre"
            class="mt-2 text-blue-600 hover:text-blue-800 text-sm"
          >
            → Modifier ce trimestre
          </button>
        </div>
        <div v-else class="text-sm text-gray-500">
          Aucune note saisie
          <button
            @click="selectedTrimestre = trimestre"
            class="mt-1 text-green-600 hover:text-green-800 text-sm block"
          >
            → Saisir les notes
          </button>
        </div>
      </div>
    </div>
  </div>
</template>