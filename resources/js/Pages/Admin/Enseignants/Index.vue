<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  enseignants: Array,
})

const search = ref('')

const filteredEnseignants = computed(() => {
  return props.enseignants.filter((ens) => {
    const term = search.value.toLowerCase()
    return (
      ens.nom.toLowerCase().includes(term) ||
      ens.prenom.toLowerCase().includes(term) ||
      ens.email.toLowerCase().includes(term) ||
      ens.matriculeEnseig.toLowerCase().includes(term)
    )
  })
})

const confirmDelete = async (id) => {
  if (confirm('Voulez-vous vraiment supprimer cet enseignant ?')) {
    try {
      await router.delete(route('enseignants.destroy', id))
    } catch (error) {
      console.error('Erreur lors de la suppression :', error)
      alert('❌ Erreur : ' + (error.message || 'Suppression échouée'))
    }
  }
}
</script>

<template>
  <Head title="Gestion des Enseignants" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">👨‍🏫 Gestion des Enseignants</h1>
      <Link
        :href="route('enseignants.create')"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        ➕ Ajouter un enseignant
      </Link>
    </div>

    <div class="flex gap-4 mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="🔍 Rechercher un enseignant..."
        class="border border-gray-300 rounded px-3 py-2 w-1/2 focus:ring-2 focus:ring-blue-400"
      />
      <div class="ml-auto text-gray-700 font-semibold">
        Total : {{ filteredEnseignants.length }}
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
            <th class="px-6 py-3">Grade</th>
            <th class="px-6 py-3">Spécialité</th>
            <th class="px-6 py-3">Téléphone</th>
            <th class="px-6 py-3">Email</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(ens, index) in filteredEnseignants"
            :key="ens.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-6 py-3">{{ index + 1 }}</td>
            <td class="px-6 py-3 font-medium">{{ ens.matriculeEnseig }}</td>
            <td class="px-6 py-3">{{ ens.nom }} {{ ens.prenom }}</td>
            <td class="px-6 py-3">{{ ens.sexe || '—' }}</td>
            <td class="px-6 py-3">{{ ens.grade || '—' }}</td>
            <td class="px-6 py-3">{{ ens.specialite || '—' }}</td>
            <td class="px-6 py-3">{{ ens.telephone || '—' }}</td>
            <td class="px-6 py-3">{{ ens.email }}</td>
            <td class="px-6 py-3 text-center">
              <Link
                :href="route('enseignants.edit', ens.id)"
                class="text-blue-600 hover:underline mr-3"
              >
                ✏️ Modifier
              </Link>
              <button
                @click="confirmDelete(ens.id)"
                class="text-red-600 hover:underline"
              >
                🗑️ Supprimer
              </button>
            </td>
          </tr>

          <tr v-if="filteredEnseignants.length === 0">
            <td colspan="7" class="text-center text-gray-500 py-6">
              Aucun enseignant trouvé.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
