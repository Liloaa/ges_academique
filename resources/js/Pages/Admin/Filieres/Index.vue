<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })


defineProps({
  filieres: Array,
})

const confirmDelete = (id) => {
  if (confirm('Voulez-vous vraiment supprimer cette filière ?')) {
    router.delete(`/admin/filieres/${id}`)
  }
}
</script>

<template>
  <Head title="Gestion des Filières" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Filières</h1>
      <Link
        href="/admin/filieres/create"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        ➕ Ajouter une filière
      </Link>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
          <tr>
            <th class="px-6 py-3">#</th>
            <th class="px-6 py-3">Nom de la Filière</th>
            <th class="px-6 py-3">Description</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(filiere, index) in filieres"
            :key="filiere.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-6 py-3">{{ index + 1 }}</td>
            <td class="px-6 py-3 font-medium text-gray-900">{{ filiere.nomFiliere }}</td>
            <td class="px-6 py-3">
              {{ filiere.description || '—' }}
            </td>
            <td class="px-6 py-3 text-center">
              <Link
                :href="`/admin/filieres/${filiere.id}/edit`"
                class="text-blue-600 hover:text-blue-800 font-medium"
              >
                🖋️ Modifier
              </Link>
              <button
                @click="confirmDelete(filiere.id)"
                class="text-red-600 hover:text-red-800 font-medium"
              >
                ❌ Supprimer
              </button>
            </td>
          </tr>
          <tr v-if="filieres.length === 0">
            <td colspan="4" class="text-center text-gray-500 py-6">
              Aucune filière enregistrée.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
