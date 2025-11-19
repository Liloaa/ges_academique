<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  inscriptions: Array,
  filtre: Object,
})

const search = ref({
  annee_scolaire_id: '',
  salle_id: '',
  etat: '',
})

function filtrer() {
  router.get(route('inscriptions.index'), search.value, {
    preserveScroll: true,
    preserveState: true,
  })
}

function supprimer(id) {
  if (confirm('Confirmer la suppression de cette inscription ?')) {
    router.delete(route('inscriptions.destroy', id), {
      onError: (err) => console.error('Erreur suppression :', err),
    })
  }
}
</script>

<template>
  <Head title="Inscriptions scolaires" />

  <div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des inscriptions</h1>
      <button
        @click="router.visit(route('inscriptions.create'))"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
      >
        ➕ Nouvelle inscription
      </button>
    </div>

    <!-- 🔍 Filtres -->
    <div class="flex flex-wrap gap-4 bg-gray-100 p-4 rounded-lg">
      <select v-model="search.annee_scolaire_id" class="p-2 border rounded">
        <option value="">-- Année scolaire --</option>
        <option
          v-for="a in filtre.annees"
          :key="a.id"
          :value="a.id"
        >
          {{ a.libelle }}
        </option>
      </select>

      <select v-model="search.salle_id" class="p-2 border rounded">
        <option value="">-- Salle --</option>
        <option
          v-for="s in filtre.salles"
          :key="s.id"
          :value="s.id"
        >
          {{ s.nomSalle }}
        </option>
      </select>

      <select v-model="search.etat" class="p-2 border rounded">
        <option value="">-- État --</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>

      <button
        @click="filtrer"
        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
      >
        🔎 Filtrer
      </button>
    </div>

    <!-- 📋 Tableau -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full border text-sm bg-white shadow rounded-lg">
        <thead class="bg-gray-200 text-gray-700">
          <tr>
            <th class="px-4 py-2 text-left">Élève</th>
            <th class="px-4 py-2 text-left">Salle</th>
            <th class="px-4 py-2 text-left">Année</th>
            <th class="px-4 py-2 text-left">Date</th>
            <th class="px-4 py-2 text-left">État</th>
            <th class="px-4 py-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="ins in props.inscriptions"
            :key="ins.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-4 py-2">{{ ins.eleve.nom }} {{ ins.eleve.prenom }}</td>
            <td class="px-4 py-2">{{ ins.salle?.nomSalle }}</td>
            <td class="px-4 py-2">{{ ins.annee_scolaire?.libelle }}</td>
            <td class="px-4 py-2">{{ ins.date_inscription }}</td>
            <td class="px-4 py-2">
              <span
                :class="ins.etat === 'active' ? 'text-green-600' : 'text-red-500'"
              >
                {{ ins.etat }}
              </span>
            </td>
            <td class="px-4 py-2 flex justify-center gap-2">
              <button
                @click="router.visit(route('inscriptions.edit', ins.id))"
                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
              >
                ✏️ Modifier
              </button>
              <button
                @click="supprimer(ins.id)"
                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
              >
                🗑️ Supprimer
              </button>
            </td>
          </tr>
    
          <tr v-if="!props.inscriptions.length">
            <td colspan="9" class="text-center text-gray-500 py-6">
              Aucun élève trouvé.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
