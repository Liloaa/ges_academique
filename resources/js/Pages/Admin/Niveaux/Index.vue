<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  niveauxGroupes: Object,
})

const confirmDelete = (id) => {
  if (confirm('Voulez-vous vraiment supprimer ce niveau ?')) {
    router.delete(`/admin/niveaux/${id}`)
  }
}

const cycleLabels = {
  primaire: 'Primaire',
  college: 'Collège', 
  lycee: 'Lycée'
}
</script>

<template>
  <Head title="Gestion des Niveaux" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Niveaux</h1>
      <Link
        href="/admin/niveaux/create"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
      >
        ➕ Ajouter un Niveau
      </Link>
    </div>

    <!-- Message d'information -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
      <p class="text-blue-800">
        Les niveaux sont organisés par cycle d'enseignement. Sélectionnez un cycle pour gérer ses niveaux.
      </p>
    </div>

    <div class="space-y-8">
      <!-- Section pour chaque cycle -->
      <div v-for="(niveaux, cycle) in niveauxGroupes" :key="cycle" class="bg-white shadow-md rounded-lg">
        <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
              {{ cycleLabels[cycle] || cycle }}
            </h2>
            <span class="text-sm text-gray-600">
              {{ niveaux.length }} niveau(x)
            </span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
              <tr>
                <th class="px-6 py-3">#</th>
                <th class="px-6 py-3">Nom du Niveau</th>
                <th class="px-6 py-3">Description</th>
                <th v-if="cycle === 'lycee'" class="px-6 py-3">Filière</th>
                <th class="px-6 py-3 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(niveau, index) in niveaux"
                :key="niveau.id"
                class="border-b hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-3">{{ index + 1 }}</td>
                <td class="px-6 py-3 font-medium text-gray-900">{{ niveau.nomNiveau }}</td>
                <td class="px-6 py-3">{{ niveau.description || '—' }}</td>
                <td v-if="cycle === 'lycee'" class="px-6 py-3">
                  {{ niveau.filiere?.nomFiliere || '—' }}
                </td>
                <td class="px-6 py-3">
                  <div class="flex justify-center space-x-3">
                    <Link
                      :href="`/admin/niveaux/${niveau.id}/edit`"
                      class="text-blue-600 hover:text-blue-800 font-medium"
                    >
                      🖋️ Modifier
                    </Link>
                    <button
                      @click="confirmDelete(niveau.id)"
                      class="text-red-600 hover:text-red-800 font-medium"
                    >
                      ❌ Supprimer
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="niveaux.length === 0">
                <td :colspan="cycle === 'lycee' ? 5 : 4" class="text-center text-gray-500 py-8">
                  <div class="flex flex-col items-center">
                    <span class="text-lg mb-2">📚</span>
                    Aucun niveau enregistré dans ce cycle.
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