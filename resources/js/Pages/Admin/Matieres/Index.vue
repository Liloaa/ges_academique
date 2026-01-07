<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  matieresGroupes: Object,
})

const confirmDelete = (id) => {
  if (confirm('Voulez-vous vraiment supprimer cette matière ?')) {
    router.delete(`/admin/matieres/${id}`)
  }
}

const cycleLabels = {
  primaire: 'Primaire',
  college: 'Collège', 
  lycee: 'Lycée'
}
</script>

<template>
  <Head title="Gestion des Matières" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Matières</h1>
      <Link
        href="/admin/matieres/create"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        ➕ Nouvelle matière
      </Link>
    </div>

    <div class="space-y-8">
      <!-- Section pour chaque cycle -->
      <div v-for="(matieres, cycle) in matieresGroupes" :key="cycle" class="bg-white shadow-md rounded-lg">
        <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
              {{ cycleLabels[cycle] || cycle }}
            </h2>
            <span class="text-sm text-gray-600">
              {{ matieres.length }} matière(s)
            </span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
              <tr>
                <th class="px-6 py-3">#</th>
                <th class="px-6 py-3">Nom de la matière</th>
                <th class="px-6 py-3">Coefficient</th>
                <th class="px-6 py-3">Niveau</th>
                <th class="px-6 py-3">Enseignant</th>
                <th class="px-6 py-3 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(matiere, index) in matieres"
                :key="matiere.id"
                class="border-b hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-3">{{ index + 1 }}</td>
                <td class="px-6 py-3 font-medium text-gray-900">{{ matiere.nomMatiere }}</td>
                <td class="px-6 py-3 text-center">{{ matiere.coefficient }}</td>
                <td class="px-6 py-3">{{ matiere.niveau.nomNiveau }}</td>
                <td class="px-6 py-3">
                  {{ matiere.enseignant ? `${matiere.enseignant.nom} ${matiere.enseignant.prenom}` : '—' }}
                </td>
                <td class="px-6 py-3">
                  <div class="flex justify-center space-x-3">
                    <Link
                      :href="`/admin/matieres/${matiere.id}/edit`"
                      class="text-blue-600 hover:text-blue-800 font-medium"
                    >
                      🖋️ Modifier
                    </Link>
                    <button
                      @click="confirmDelete(matiere.id)"
                      class="text-red-600 hover:text-red-800 font-medium"
                    >
                      ❌ Supprimer
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="matieres.length === 0">
                <td colspan="6" class="text-center text-gray-500 py-8">
                  <div class="flex flex-col items-center">
                    <span class="text-lg mb-2">📚</span>
                    Aucune matière enregistrée dans ce cycle.
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>