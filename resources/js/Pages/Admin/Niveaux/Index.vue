<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  niveaux: Array,
})

const confirmDelete = (id) => {
  if (confirm('Voulez-vous vraiment supprimer ce niveau ?')) {
    router.delete(`/admin/niveaux/${id}`)
  }
}
</script>

<template>
  <Head title="Gestion des Niveaux" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Niveaux</h1>
      <Link
        href="/admin/niveaux/create"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        ➕ Ajouter un niveau
      </Link>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
          <tr>
            <th class="px-6 py-3">#</th>
            <th class="px-6 py-3">Nom du Niveau</th>
            <th class="px-6 py-3">Description</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(niveau, index) in niveaux"
            :key="niveau.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-6 py-3">{{ index + 1 }}</td>
            <td class="px-6 py-3 font-medium text-gray-900">{{ niveau.nomNiveau }}</td>
            <td class="px-6 py-3">{{ niveau.description || '—' }}</td>
            <td class="px-6 py-3 text-center">
              <Link
                :href="`/admin/niveaux/${niveau.id}/edit`"
                class="text-blue-600 hover:underline mr-3"
              >
                ✏️ Modifier
              </Link>
              <button
                @click="confirmDelete(niveau.id)"
                class="text-red-600 hover:underline"
              >
                🗑️ Supprimer
              </button>
            </td>
          </tr>
          <tr v-if="niveaux.length === 0">
            <td colspan="4" class="text-center text-gray-500 py-6">
              Aucun niveau enregistré.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
