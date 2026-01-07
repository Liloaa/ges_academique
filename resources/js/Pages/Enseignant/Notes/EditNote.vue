<template>
  <EnseignantLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
          <h1 class="text-2xl font-bold text-gray-800">Modifier une note</h1>
          <Link 
            :href="route('enseignant.notes.historique', note.matiere.id)"
            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
          >
            ← Retour
          </Link>
        </div>
        
        <!-- Informations -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <h3 class="text-lg font-semibold text-gray-700 mb-2">Élève</h3>
              <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                  <span class="text-blue-600 font-bold">
                    {{ getInitials(note.inscription.eleve) }}
                  </span>
                </div>
                <div>
                  <p class="font-medium">{{ note.inscription.eleve.nom }} {{ note.inscription.eleve.prenom }}</p>
                  <p class="text-sm text-gray-500">{{ note.inscription.eleve.matricule }}</p>
                </div>
              </div>
            </div>
            
            <div>
              <h3 class="text-lg font-semibold text-gray-700 mb-2">Matière</h3>
              <p class="font-medium">{{ note.matiere.nomMatiere }}</p>
              <p class="text-sm text-gray-500">Coefficient: {{ note.matiere.coefficient }}</p>
            </div>
            
            <div>
              <h3 class="text-lg font-semibold text-gray-700 mb-2">Classe</h3>
              <p class="font-medium">{{ note.inscription.salle.nomSalle }}</p>
              <p class="text-sm text-gray-500">{{ note.inscription.salle.niveau.nomNiveau }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulaire -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
          <form @submit.prevent="submitForm">
            <!-- Trimestre -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Trimestre *
              </label>
              <select
                v-model="form.trimestre"
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.trimestre }"
              >
                <option value="">Sélectionner un trimestre</option>
                <option value="1er">1er trimestre</option>
                <option value="2ème">2ème trimestre</option>
                <option value="3ème">3ème trimestre</option>
              </select>
              <p v-if="form.errors.trimestre" class="mt-1 text-sm text-red-600">
                {{ form.errors.trimestre }}
              </p>
            </div>

            <!-- Note -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Note /20 *
              </label>
              <div class="relative">
                <input
                  v-model="form.note"
                  type="number"
                  step="0.25"
                  min="0"
                  max="20"
                  placeholder="0.00 - 20.00"
                  class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.note }"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <span class="text-gray-500">/20</span>
                </div>
              </div>
              <p v-if="form.errors.note" class="mt-1 text-sm text-red-600">
                {{ form.errors.note }}
              </p>
            </div>

            <!-- Commentaire -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Commentaire (optionnel)
              </label>
              <textarea
                v-model="form.comment"
                rows="3"
                placeholder="Observations sur cette note..."
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
              ></textarea>
              <p class="mt-1 text-sm text-gray-500">
                {{ form.comment?.length || 0 }}/500 caractères
              </p>
            </div>

            <!-- Notes précédentes -->
            <div v-if="otherNotes.length > 0" class="mb-6">
              <h3 class="text-lg font-semibold text-gray-700 mb-3">Notes précédentes</h3>
              <div class="bg-gray-50 rounded p-4">
                <div class="space-y-2">
                  <div 
                    v-for="otherNote in otherNotes" 
                    :key="otherNote.id"
                    class="flex justify-between items-center"
                  >
                    <span class="text-gray-700">
                      {{ otherNote.trimestre }} trimestre
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                      :class="getNoteClass(otherNote.note)">
                      {{ otherNote.note }}/20
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
              <div class="space-x-3">
                <button
                  type="button"
                  @click="confirmDelete"
                  class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  Supprimer
                </button>
                
                <Link
                  :href="route('enseignant.notes.historique', note.matiere.id)"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                >
                  Annuler
                </Link>
              </div>
              
              <button
                type="submit"
                :disabled="form.processing"
                class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="form.processing">Enregistrement...</span>
                <span v-else>Mettre à jour</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </EnseignantLayout>
</template>

<script setup>
import EnseignantLayout from '@/Layouts/EnseignantLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const props = defineProps({
  enseignant: Object,
  note: Object,
  otherNotes: Array,
  trimestres: Array,
  eleve: Object,
  matiere: Object,
  classe: Object,
});

// Initialiser le formulaire
const form = useForm({
  note: props.note.note,
  trimestre: props.note.trimestre,
  comment: props.note.commentaire || '',
});

// Méthodes
const getInitials = (eleve) => {
  if (!eleve || !eleve.prenom || !eleve.nom) return '??';
  return (eleve.prenom.charAt(0) + eleve.nom.charAt(0)).toUpperCase();
};

const getNoteClass = (note) => {
  if (note >= 10) return 'bg-green-100 text-green-800';
  if (note >= 8) return 'bg-yellow-100 text-yellow-800';
  return 'bg-red-100 text-red-800';
};

const submitForm = () => {
  form.put(route('enseignant.notes.update', props.note.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Succès
    },
    onError: (errors) => {
      console.error('Erreurs:', errors);
    }
  });
};

const confirmDelete = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.')) {
    router.delete(route('enseignant.notes.destroy', props.note.id), {
      preserveScroll: true,
      onSuccess: () => {
        // Rediriger vers l'historique après suppression
        router.visit(route('enseignant.notes.historique', props.note.matiere_id));
      }
    });
  }
};

// Initialiser les valeurs si elles sont manquantes
onMounted(() => {
  if (!form.note && props.note) {
    form.note = props.note.note;
  }
  if (!form.trimestre && props.note) {
    form.trimestre = props.note.trimestre;
  }
});
</script>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>