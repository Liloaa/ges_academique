<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  eleves: Array,
})

const search = ref('')
const selectedSalle = ref('')

const filteredEleves = computed(() => {
  return props.eleves.filter((eleve) => {
    const matchesSearch =
      eleve.nom.toLowerCase().includes(search.value.toLowerCase()) ||
      eleve.prenom.toLowerCase().includes(search.value.toLowerCase()) ||
      eleve.matricule.toLowerCase().includes(search.value.toLowerCase())

    const matchesSalle =
      !selectedSalle.value || eleve.salle?.nomSalle === selectedSalle.value

    return matchesSearch && matchesSalle
  })
})

const effectif = computed(() => filteredEleves.value.length)

const confirmDelete = async (id) => {
  if (confirm('Voulez-vous vraiment supprimer cet élève ?')) {
    try {
      await router.delete(`/admin/eleves/${id}`)
    } catch (error) {
      console.error('Erreur lors de la suppression :', error)
      alert('Erreur : ' + error.message)
    }
  }
}
</script>

<template>
  <Head title="Gestion des Élèves" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Élèves</h1>
      <Link
        href="/admin/eleves/create"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        ➕ Ajouter un élève
      </Link>
    </div>

    <div class="flex gap-4 mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="🔍 Rechercher un élève..."
        class="border border-gray-300 rounded px-3 py-2 w-1/2 focus:outline-none focus:ring-2 focus:ring-blue-400"
      />

      <select
        v-model="selectedSalle"
        class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
      >
        <option value="">Toutes les salles</option>
        <option
          v-for="salle in [...new Set(props.eleves.map(e => e.salle?.nomSalle).filter(Boolean))]"
          :key="salle"
          :value="salle"
        >
          {{ salle }}
        </option>
      </select>

      <div class="ml-auto text-gray-700 font-semibold">
        Effectif : {{ effectif }}
      </div>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
          <tr>
            <th class="px-6 py-3">#</th>
            <th class="px-6 py-3">Matricule</th>
            <th class="px-6 py-3">Nom & Prénom</th>
            <th class="px-6 py-3">Sexe</th>
            <th class="px-6 py-3">Filière</th>
            <th class="px-6 py-3">Niveau</th>
            <th class="px-6 py-3">Salle</th>
            <th class="px-6 py-3">Année scolaire</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(eleve, index) in filteredEleves"
            :key="eleve.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-6 py-3">{{ index + 1 }}</td>
            <td class="px-6 py-3 font-medium text-gray-900">{{ eleve.matricule }}</td>
            <td class="px-6 py-3">{{ eleve.nom }} {{ eleve.prenom }}</td>
            <td class="px-6 py-3">{{ eleve.sexe || '—' }}</td>
            <td class="px-6 py-3">{{ eleve.filiere?.nomFiliere || '—' }}</td>
            <td class="px-6 py-3">{{ eleve.niveau?.nomNiveau || '—' }}</td>
            <td class="px-6 py-3">{{ eleve.salle?.nomSalle || '—' }}</td>
            <td class="px-6 py-3">{{ eleve.annee?.libelle || '—' }}</td>
           <td class="px-6 py-3 text-center">
              <div class="flex justify-center gap-3">
                <!-- Historique -->
                <Link
                  :href="route('eleves.historique', eleve.id)"
                  class="px-2 py-1 bg-indigo-100 text-indigo-600 rounded hover:bg-indigo-200 transition"
                >
                  📜 Historique
                </Link>

                <!-- Modifier -->
                <Link
                  :href="`/admin/eleves/${eleve.id}/edit`"
                  class="px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition"
                >
                  ✏️ Modifier
                </Link>

                <!-- Supprimer -->
                <button
                  @click="confirmDelete(eleve.id)"
                  class="px-2 py-1 bg-red-100 text-red-600 rounded hover:bg-red-200 transition"
                >
                  🗑️ Supprimer
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="filteredEleves.length === 0">
            <td colspan="9" class="text-center text-gray-500 py-6">
              Aucun élève trouvé.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
