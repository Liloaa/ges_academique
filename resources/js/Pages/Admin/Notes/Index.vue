<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  notes: Array,
  groupedNotes: Object,
  filters: Object,
  trimestres: Array,
})

const selectedTrimestre = ref(props.filters.trimestre || '')
const search = ref('')

// Filtrer les notes
const filteredNotes = computed(() => {
  let filtered = props.notes

  if (selectedTrimestre.value) {
    filtered = filtered.filter(note => note.trimestre === selectedTrimestre.value)
  }

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    filtered = filtered.filter(note =>
      note.inscription.eleve.nom.toLowerCase().includes(searchLower) ||
      note.inscription.eleve.prenom.toLowerCase().includes(searchLower) ||
      note.matiere.nomMatiere.toLowerCase().includes(searchLower)
    )
  }

  return filtered
})

// Grouper les notes filtrées
const filteredGroupedNotes = computed(() => {
  const grouped = {}
  filteredNotes.value.forEach(note => {
    if (!grouped[note.inscription_id]) {
      grouped[note.inscription_id] = {}
    }
    if (!grouped[note.inscription_id][note.trimestre]) {
      grouped[note.inscription_id][note.trimestre] = []
    }
    grouped[note.inscription_id][note.trimestre].push(note)
  })
  return grouped
})

const confirmDelete = async (id) => {
  if (confirm('Voulez-vous vraiment supprimer cette note ?')) {
    try {
      await router.delete(`/admin/notes/${id}`)
    } catch (error) {
      console.error('Erreur lors de la suppression :', error)
      alert('Erreur : ' + error.message)
    }
  }
}

const filtrer = () => {
  router.get(route('notes.index'), {
    trimestre: selectedTrimestre.value,
    inscription_id: props.filters.inscription_id
  })
}
</script>

<template>
  <Head title="Gestion des Notes" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Notes</h1>
      <div class="flex gap-4">
        <Link
          v-if="filters.inscription_id"
          :href="route('notes.create', { inscription_id: filters.inscription_id })"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
        >
          📝 Saisir les notes
        </Link>
      </div>
    </div>

    <!-- Filtres -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Trimestre</label>
          <select 
            v-model="selectedTrimestre" 
            @change="filtrer"
            class="w-full border border-gray-300 rounded px-3 py-2"
          >
            <option value="">Tous les trimestres</option>
            <option v-for="trimestre in trimestres" :key="trimestre" :value="trimestre">
              {{ trimestre }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
          <input
            v-model="search"
            type="text"
            placeholder="Nom, prénom ou matière..."
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>
        <div class="flex items-end">
          <!-- CORRECTION : Remplacer href par :href avec route() -->
          <Link
            v-if="filters.inscription_id"
            :href="route('notes.index')"
            class="w-full bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition text-center"
          >
            📋 Voir toutes les notes
          </Link>
        </div>
      </div>
    </div>

    <!-- Affichage groupé par élève et trimestre -->
    <div class="space-y-6">
      <div 
        v-for="(trimestres, inscriptionId) in filteredGroupedNotes" 
        :key="inscriptionId"
        class="bg-white shadow-md rounded-lg"
      >
        <!-- En-tête de l'élève -->
        <div 
          v-for="(notes, trimestre) in trimestres" 
          :key="trimestre"
          class="border-b"
        >
          <div class="bg-gray-50 px-6 py-4">
            <div class="flex justify-between items-center">
              <div>
                <h3 class="text-lg font-semibold text-gray-800">
                  {{ notes[0].inscription.eleve.nom }} {{ notes[0].inscription.eleve.prenom }}
                </h3>
                <p class="text-sm text-gray-600">
                  {{ notes[0].inscription.salle.niveau.nomNiveau }} - 
                  {{ notes[0].inscription.salle.nomSalle }} - 
                  <span class="font-medium">{{ trimestre }} trimestre</span>
                </p>
              </div>
              <div class="flex gap-2">
                <Link
                  :href="route('notes.create', { 
                    inscription_id: notes[0].inscription_id,
                    trimestre: trimestre 
                  })"
                  class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition"
                >
                  🖋️ Saisir
                </Link>
              </div>
            </div>
          </div>

          <!-- Notes de l'élève pour ce trimestre -->
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700">
              <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                <tr>
                  <th class="px-6 py-3">Matière</th>
                  <th class="px-6 py-3 text-center">Coefficient</th>
                  <th class="px-6 py-3 text-center">Note</th>
                  <th class="px-6 py-3">Enseignant</th>
                  <th class="px-6 py-3 text-center">Date</th>
                  <th class="px-6 py-3 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="note in notes"
                  :key="note.id"
                  class="border-b hover:bg-gray-50"
                >
                  <td class="px-6 py-3 font-medium text-gray-900">
                    {{ note.matiere.nomMatiere }}
                  </td>
                  <td class="px-6 py-3 text-center">
                    {{ note.matiere.coefficient }}
                  </td>
                  <td class="px-6 py-3 text-center">
                    <span 
                      class="font-bold text-lg"
                      :class="{
                        'text-green-600': note.note >= 10,
                        'text-red-600': note.note < 10
                      }"
                    >
                      {{ note.note }}/20
                    </span>
                  </td>
                  <td class="px-6 py-3">
                    {{ note.enseignant ? `${note.enseignant.nom} ${note.enseignant.prenom}` : '—' }}
                  </td>
                  <td class="px-6 py-3 text-center">
                    {{ note.date_saisie }}
                  </td>
                  <td class="px-6 py-3 text-center">
                    <button
                      @click="confirmDelete(note.id)"
                      class="text-red-600 hover:text-red-800 transition-colors"
                    >
                      ❌ Supprimer
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Message si pas de notes -->
    <div v-if="filteredNotes.length === 0" class="bg-white p-8 rounded-lg shadow-md text-center">
      <div class="text-gray-500 text-lg mb-4">📝</div>
      <p class="text-gray-600">Aucune note trouvée.</p>
      <Link
        v-if="filters.inscription_id"
        :href="route('notes.create', { inscription_id: filters.inscription_id })"
        class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        Saisir les premières notes
      </Link>
    </div>
  </div>
</template>