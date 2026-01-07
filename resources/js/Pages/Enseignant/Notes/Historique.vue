<template>
  <EnseignantLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historique des notes - {{ matiere.nomMatiere }}
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ matiere.niveau.nomNiveau }} • Année scolaire: {{ anneeActive.annee_debut }}-{{ anneeActive.annee_fin }}
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
        <!-- Informations matière -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-gray-900">
                  {{ matiere.nomMatiere }}
                </h3>
                <p class="text-gray-600">
                  Coefficient: {{ matiere.coefficient }} • Niveau: {{ matiere.niveau.nomNiveau }}
                </p>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500">Enseignant</p>
                <p class="font-medium">{{ enseignant.nom }} {{ enseignant.prenom }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sélecteur de trimestre -->
        <div class="mb-6">
          <div class="bg-white shadow-sm rounded-lg p-4">
            <div class="flex space-x-2 overflow-x-auto pb-2">
              <button
                v-for="trim in trimestres"
                :key="trim"
                @click="activeTrimestre = trim"
                :class="[
                  'px-4 py-2 rounded-lg font-medium transition-colors',
                  activeTrimestre === trim
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                {{ trim }} trimestre
              </button>
            </div>
          </div>
        </div>

        <!-- Statistiques -->
        <div v-if="statistiques[activeTrimestre]" class="mb-6">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
              <div class="text-center">
                <p class="text-sm text-gray-500">Moyenne de classe</p>
                <p class="text-2xl font-bold text-blue-600">
                  {{ statistiques[activeTrimestre].moyenne_classe }}
                </p>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
              <div class="text-center">
                <p class="text-sm text-gray-500">Note maximale</p>
                <p class="text-2xl font-bold text-green-600">
                  {{ statistiques[activeTrimestre].note_max }}
                </p>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
              <div class="text-center">
                <p class="text-sm text-gray-500">Note minimale</p>
                <p class="text-2xl font-bold text-red-600">
                  {{ statistiques[activeTrimestre].note_min }}
                </p>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
              <div class="text-center">
                <p class="text-sm text-gray-500">Élèves ≥ 10/20</p>
                <p class="text-2xl font-bold text-purple-600">
                  {{ statistiques[activeTrimestre].notes_sup_10 }}
                </p>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
              <div class="text-center">
                <p class="text-sm text-gray-500">Total élèves</p>
                <p class="text-2xl font-bold text-gray-800">
                  {{ statistiques[activeTrimestre].total_eleves }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tableau des notes -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
              Notes du {{ activeTrimestre }} trimestre
            </h3>
          </div>

          <div v-if="notesParTrimestre[activeTrimestre]?.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Élève
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Classe
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Note
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date saisie
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="note in notesParTrimestre[activeTrimestre]" :key="note.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div>
                        <div class="text-sm font-medium text-gray-900">
                          {{ note.inscription.eleve.nom }} {{ note.inscription.eleve.prenom }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ note.inscription.eleve.matricule }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ note.inscription.salle.nomSalle }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ note.inscription.salle.niveau.nomNiveau }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="{
                        'bg-green-100 text-green-800': note.note >= 10,
                        'bg-red-100 text-red-800': note.note < 10
                      }">
                      {{ note.note }}/20
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(note.date_saisie) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <Link
                        :href="route('enseignant.notes.edit', note.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        Modifier
                      </Link>
                      <button
                        @click="confirmDelete(note)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Supprimer
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-else class="px-6 py-12 text-center">
            <AcademicCapIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune note</h3>
            <p class="mt-1 text-sm text-gray-500">
              Aucune note n'a été saisie pour ce trimestre.
            </p>
            <div class="mt-6">
              <Link
                :href="route('enseignant.notes.index')"
                class="btn-primary"
              >
                Saisir des notes
              </Link>
            </div>
          </div>
        </div>

        <!-- Modal de confirmation de suppression -->
        <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
          <template #title>
            Supprimer la note
          </template>
          <template #content>
            Êtes-vous sûr de vouloir supprimer la note de {{ noteToDelete?.inscription?.eleve?.nom }} 
            {{ noteToDelete?.inscription?.eleve?.prenom }} ({{ noteToDelete?.note }}/20) ?
            Cette action est irréversible.
          </template>
          <template #footer>
            <button @click="showDeleteModal = false" type="button" class="btn-secondary">
              Annuler
            </button>
            <button @click="deleteNote" type="button" class="btn-danger ml-2">
              Supprimer
            </button>
          </template>
        </ConfirmationModal>
      </div>
    </div>
  </EnseignantLayout>
</template>

<script setup>
import EnseignantLayout from '@/Layouts/EnseignantLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import { AcademicCapIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  enseignant: Object,
  matiere: Object,
  anneeActive: Object,
  notesParTrimestre: Object,
  statistiques: Object,
  trimestres: Array,
});

const activeTrimestre = ref('1er');
const showDeleteModal = ref(false);
const noteToDelete = ref(null);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

const confirmDelete = (note) => {
  noteToDelete.value = note;
  showDeleteModal.value = true;
};

const deleteNote = () => {
  if (noteToDelete.value) {
    router.delete(route('enseignant.notes.destroy', noteToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false;
        noteToDelete.value = null;
      },
      preserveScroll: true
    });
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

.btn-danger {
  @apply inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150;
}
</style>