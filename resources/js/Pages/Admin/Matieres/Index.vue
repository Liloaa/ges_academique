<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  matieres: Array,
})

const search = ref('')

// Filtrage simple côté client
const filteredMatieres = computed(() =>
  props.matieres.filter(m =>
    m.nomMatiere.toLowerCase().includes(search.value.toLowerCase())
  )
)

function supprimer(id) {
  if (confirm('Voulez-vous vraiment supprimer cette matière ?')) {
    router.delete(route('matieres.destroy', id))
  }
}
</script>

<template>
  <Head title="Gestion des Matières" />

  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">📚 Gestion des Matières</h1>

    <div class="flex justify-between mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="🔍 Rechercher une matière..."
        class="border rounded px-3 py-2 w-1/3"
      />
      <Link
        :href="route('matieres.create')"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        ➕ Nouvelle matière
      </Link>
    </div>

    <table class="w-full border-collapse border text-sm">
      <thead class="bg-gray-100">
        <tr class="text-left">
          <th class="border p-2">#</th>
          <th class="border p-2">Nom</th>
          <th class="border p-2">Coefficient</th>
          <th class="border p-2">Filière</th>
          <th class="border p-2">Niveau</th>
          <th class="border p-2">Enseignant</th>
          <th class="border p-2 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(m, i) in filteredMatieres" :key="m.id" class="hover:bg-gray-50">
          <td class="border p-2">{{ i + 1 }}</td>
          <td class="border p-2">{{ m.nomMatiere }}</td>
          <td class="border p-2 text-center">{{ m.coefficient }}</td>
          <td class="border p-2">{{ m.filiere?.nomFiliere || '-' }}</td>
          <td class="border p-2">{{ m.niveau?.nomNiveau || '-' }}</td>
          <td class="border p-2">
            {{ m.enseignant ? m.enseignant.nom + ' ' + m.enseignant.prenom : '-' }}
          </td>
          <td class="border p-2 text-center">
            <Link
              :href="route('matieres.edit', m.id)"
              class="text-blue-600 hover:underline mr-2"
            >
              ✏️ Modifier
            </Link>
            <button @click="supprimer(m.id)" class="text-red-600 hover:underline">
              🗑️ Supprimer
            </button>
          </td>
        </tr>
        <tr v-if="filteredMatieres.length === 0">
          <td colspan="7" class="text-center text-gray-500 py-3">
            Aucune matière trouvée.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
