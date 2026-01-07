<template>
  <EnseignantLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Saisie des notes - {{ matiere.nomMatiere }}
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ salle.nomSalle }} ({{ salle.niveau.nomNiveau }}) • 
            Trimestre: {{ trimestre }} • 
            Année scolaire: {{ anneeActive.libelle }}
          </p>
        </div>
        <div class="flex space-x-2">
          <Link 
            :href="route('enseignant.notes.index')"
            class="btn-secondary"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour
          </Link>
          <Link 
            :href="route('enseignant.notes.historique', matiere.id)"
            class="btn-secondary"
          >
            <ChartBarIcon class="h-4 w-4 mr-2" />
            Historique
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête de la saisie -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div class="mb-4 md:mb-0">
                <h3 class="text-lg font-medium text-gray-900">
                  {{ matiere.nomMatiere }}
                </h3>
                <p class="text-gray-600">
                  Coefficient: {{ matiere.coefficient }} • 
                  Effectif: {{ inscriptions.length }} élèves
                </p>
              </div>
              
              <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                <!-- Sélecteur de trimestre -->
                <div class="flex items-center space-x-2">
                  <label class="text-sm font-medium text-gray-700">Trimestre:</label>
                  <select 
                    v-model="currentTrimestre"
                    @change="changeTrimestre"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                  >
                    <option v-for="t in trimestres" :key="t" :value="t">
                      {{ t }}
                    </option>
                  </select>
                </div>
                
                <!-- Statistiques rapides -->
                <div class="flex items-center space-x-4">
                  <div class="text-center">
                    <div class="text-xs text-gray-500">Moyenne</div>
                    <div class="text-sm font-semibold text-blue-600">
                      {{ calculateAverage() }}
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="text-xs text-gray-500">Notes saisies</div>
                    <div class="text-sm font-semibold text-green-600">
                      {{ countEnteredNotes() }}/{{ inscriptions.length }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Formulaire de saisie -->
        <form @submit.prevent="submitNotes">
          <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <!-- En-tête du tableau -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">
                  Liste des élèves
                </h3>
                <div class="text-sm text-gray-600">
                  {{ new Date().toLocaleDateString('fr-FR') }}
                </div>
              </div>
            </div>

            <!-- Tableau des notes -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      #
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Élève
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Note actuelle
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nouvelle note
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Commentaire
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr 
                    v-for="(inscription, index) in inscriptions" 
                    :key="inscription.id"
                    :class="{'bg-gray-50': index % 2 === 0}"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ index + 1 }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0">
                          <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-800 font-semibold">
                            {{ getInitials(inscription.eleve) }}
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ inscription.eleve.nom }} {{ inscription.eleve.prenom }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ inscription.eleve.matricule }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div v-if="getExistingNote(inscription)" class="flex items-center">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                          :class="{
                            'bg-green-100 text-green-800': getExistingNote(inscription) >= 10,
                            'bg-yellow-100 text-yellow-800': getExistingNote(inscription) >= 8 && getExistingNote(inscription) < 10,
                            'bg-red-100 text-red-800': getExistingNote(inscription) < 8
                          }">
                          {{ getExistingNote(inscription) }}/20
                        </span>
                        <span class="ml-2 text-xs text-gray-500">
                          ({{ formatDate(getNoteDate(inscription)) }})
                        </span>
                      </div>
                      <div v-else class="text-sm text-gray-400">
                        Non noté
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <input
                          v-model="notes[inscription.id]"
                          type="number"
                          min="0"
                          max="20"
                          step="0.25"
                          class="w-24 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                          placeholder="0.00 - 20.00"
                          @input="validateNote(inscription.id)"
                        />
                        <span class="text-sm text-gray-500">/20</span>
                      </div>
                      <div v-if="errors[inscription.id]" class="mt-1 text-xs text-red-600">
                        {{ errors[inscription.id] }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <input
                        v-model="comments[inscription.id]"
                        type="text"
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="Observations..."
                        maxlength="200"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pied de page du formulaire -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
              <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                  <div v-if="countEnteredNotes() > 0">
                    <span class="font-medium">{{ countEnteredNotes() }}</span> note(s) à enregistrer
                    <span v-if="calculateAverage() > 0" class="ml-4">
                      • Moyenne provisoire: <span class="font-medium text-blue-600">{{ calculateAverage() }}/20</span>
                    </span>
                  </div>
                  <div v-else>
                    Aucune note saisie
                  </div>
                </div>
                
                <div class="flex space-x-3">
                  <button
                    type="button"
                    @click="resetForm"
                    class="btn-secondary"
                  >
                    <ArrowPathIcon class="h-4 w-4 mr-2" />
                    Réinitialiser
                  </button>
                  
                  <button
                    type="submit"
                    :disabled="isSubmitting || countEnteredNotes() === 0"
                    :class="{
                      'btn-primary': !isSubmitting && countEnteredNotes() > 0,
                      'btn-disabled': isSubmitting || countEnteredNotes() === 0
                    }"
                  >
                    <template v-if="isSubmitting">
                      <ArrowPathIcon class="h-4 w-4 mr-2 animate-spin" />
                      Enregistrement...
                    </template>
                    <template v-else>
                      <CheckIcon class="h-4 w-4 mr-2" />
                      Enregistrer les notes ({{ countEnteredNotes() }})
                    </template>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <InformationCircleIcon class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">
                Instructions
              </h3>
              <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc pl-5 space-y-1">
                  <li>Les notes doivent être comprises entre 0 et 20</li>
                  <li>Vous pouvez utiliser des décimales (ex: 12.5, 15.75)</li>
                  <li>Laissez le champ vide pour ne pas attribuer de note</li>
                  <li>Cliquez sur "Réinitialiser" pour effacer toutes les saisies</li>
                  <li>Les notes existantes seront mises à jour</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </EnseignantLayout>
</template>

<script setup>
import EnseignantLayout from '@/Layouts/EnseignantLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted } from 'vue';
import { 
  ArrowLeftIcon, 
  ChartBarIcon, 
  ArrowPathIcon, 
  CheckIcon,
  InformationCircleIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  enseignant: Object,
  matiere: Object,
  salle: Object,
  anneeActive: Object,
  inscriptions: Array,
  trimestre: String,
  trimestres: Array,
});

// Données réactives
const currentTrimestre = ref(props.trimestre);
const notes = reactive({});
const comments = reactive({});
const errors = reactive({});
const isSubmitting = ref(false);

// Initialiser les données des notes existantes
onMounted(() => {
  props.inscriptions.forEach(inscription => {
    const existingNote = getExistingNote(inscription);
    if (existingNote !== null) {
      notes[inscription.id] = existingNote;
    }
  });
});

// Méthodes
const getInitials = (eleve) => {
  return (eleve.prenom?.charAt(0) || '') + (eleve.nom?.charAt(0) || '');
};

const getExistingNote = (inscription) => {
  const note = inscription.notes && inscription.notes[currentTrimestre.value];
  return note ? note.note : null;
};

const getNoteDate = (inscription) => {
  const note = inscription.notes && inscription.notes[currentTrimestre.value];
  return note ? note.date_saisie : null;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

const validateNote = (inscriptionId) => {
  const value = notes[inscriptionId];
  
  if (value === '' || value === null || value === undefined) {
    delete errors[inscriptionId];
    return;
  }
  
  const numValue = parseFloat(value);
  
  if (isNaN(numValue)) {
    errors[inscriptionId] = 'Veuillez entrer un nombre valide';
  } else if (numValue < 0 || numValue > 20) {
    errors[inscriptionId] = 'La note doit être entre 0 et 20';
  } else {
    delete errors[inscriptionId];
  }
};

const countEnteredNotes = () => {
  return Object.values(notes).filter(value => 
    value !== '' && 
    value !== null && 
    value !== undefined && 
    !errors[Object.keys(notes).find(key => notes[key] === value)]
  ).length;
};

const calculateAverage = () => {
  const validNotes = Object.values(notes)
    .filter(value => value !== '' && value !== null && value !== undefined)
    .map(value => parseFloat(value))
    .filter(value => !isNaN(value));
  
  if (validNotes.length === 0) return 0;
  
  const sum = validNotes.reduce((acc, note) => acc + note, 0);
  const average = sum / validNotes.length;
  
  return average.toFixed(2);
};

const changeTrimestre = () => {
  router.get(route('enseignant.notes.eleves', {
    salle: props.salle.id,
    matiere: props.matiere.id,
    trimestre: currentTrimestre.value
  }));
};

const resetForm = () => {
  if (confirm('Voulez-vous vraiment réinitialiser toutes les notes saisies ?')) {
    Object.keys(notes).forEach(key => {
      notes[key] = '';
    });
    Object.keys(comments).forEach(key => {
      delete comments[key];
    });
    Object.keys(errors).forEach(key => {
      delete errors[key];
    });
  }
};

const submitNotes = async () => {
  // Valider toutes les notes
  Object.keys(notes).forEach(inscriptionId => {
    validateNote(inscriptionId);
  });
  
  // Vérifier s'il y a des erreurs
  if (Object.keys(errors).length > 0) {
    alert('Veuillez corriger les erreurs avant de soumettre.');
    return;
  }
  
  // Préparer les données
  const notesData = props.inscriptions.map(inscription => ({
    inscription_id: inscription.id,
    note: notes[inscription.id] || null,
    comment: comments[inscription.id] || null
  }));
  
  // Vérifier qu'au moins une note est saisie
  const hasNotes = notesData.some(item => item.note !== null && item.note !== '');
  if (!hasNotes) {
    alert('Veuillez saisir au moins une note avant de soumettre.');
    return;
  }
  
  isSubmitting.value = true;
  
  try {
    await router.post(route('enseignant.notes.store'), {
      salle_id: props.salle.id,
      matiere_id: props.matiere.id,
      trimestre: currentTrimestre.value,
      notes: notesData
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Recharger les données après succès
        router.reload({ only: ['inscriptions'] });
      },
      onError: (errors) => {
        console.error('Erreurs:', errors);
        alert('Une erreur est survenue lors de l\'enregistrement des notes.');
      },
      onFinish: () => {
        isSubmitting.value = false;
      }
    });
  } catch (error) {
    console.error('Erreur:', error);
    alert('Une erreur est survenue lors de l\'enregistrement des notes.');
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.btn-primary {
  @apply inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition;
}

.btn-secondary {
  @apply inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150;
}

.btn-disabled {
  @apply inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed opacity-50;
}

/* Style pour les inputs de note */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  opacity: 1;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}

/* Animation pour les lignes modifiées */
tr {
  transition: background-color 0.2s ease;
}

tr:hover {
  @apply bg-blue-50;
}
</style>