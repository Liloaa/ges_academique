<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  inscriptionsGroupes: Object,
})

const confirmDelete = (id) => {
  if (confirm('Voulez-vous vraiment supprimer cette inscription ?')) {
    router.delete(`/admin/inscriptions/${id}`)
  }
}

const cycleLabels = {
  primaire: 'Primaire',
  college: 'Collège', 
  lycee: 'Lycée'
}
</script>

<template>
  <Head title="Gestion des Inscriptions" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Inscriptions</h1>
      <Link
        href="/admin/inscriptions/create"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        ➕ Nouvelle inscription
      </Link>
    </div>

    <div class="space-y-8">
      <!-- Section pour chaque cycle -->
      <div v-for="(inscriptions, cycle) in inscriptionsGroupes" :key="cycle" class="bg-white shadow-md rounded-lg">
        <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
              {{ cycleLabels[cycle] || cycle }}
            </h2>
            <span class="text-sm text-gray-600">
              {{ inscriptions.length }} inscription(s)
            </span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
              <tr>
                <th class="px-6 py-3">#</th>
                <th class="px-6 py-3">Élève</th>
                <th class="px-6 py-3">Salle</th>
                <th class="px-6 py-3">Niveau</th>
                <th class="px-6 py-3">Année scolaire</th>
                <th class="px-6 py-3">Date inscription</th>
                <th class="px-6 py-3">État</th>
                <th class="px-6 py-3 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(inscription, index) in inscriptions"
                :key="inscription.id"
                class="border-b hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-3">{{ index + 1 }}</td>
                <td class="px-6 py-3 font-medium text-gray-900">
                  {{ inscription.eleve.nom }} {{ inscription.eleve.prenom }}
                </td>
                <td class="px-6 py-3">{{ inscription.salle.nomSalle }}</td>
                <td class="px-6 py-3">{{ inscription.salle.niveau.nomNiveau }}</td>
                <td class="px-6 py-3">{{ inscription.annee.libelle }}</td>
                <td class="px-6 py-3">{{ inscription.date_inscription }}</td>
                <td class="px-6 py-3">
                  <span :class="inscription.etat === 'active' ? 'text-green-600' : 'text-red-500'">
                    {{ inscription.etat }}
                  </span>
                </td>
                <td class="px-6 py-3">
                  <div class="flex justify-center space-x-3">
                    <Link
                      :href="`/admin/inscriptions/${inscription.id}/edit`"
                      class="text-blue-600 hover:text-blue-800 transition-colors flex items-center"
                    >
                      <span class="mr-1">🖋️</span> Modifier
                    </Link>
                    <button
                      @click="confirmDelete(inscription.id)"
                      class="text-red-600 hover:text-red-800 transition-colors flex items-center"
                    >
                      <span class="mr-1">❌</span> Supprimer
                    </button>
                    <Link
                      :href="route('notes.create', { inscription_id: inscription.id })"
                      class="text-green-600 hover:text-green-800 transition-colors flex items-center"
                    >
                      <span class="mr-1">📝</span> Notes
                    </Link>
                    <Link
                      :href="`/admin/resultats?eleve_id=${inscription.eleve_id}&annee_id=${inscription.annee_scolaire_id}`"
                      class="text-purple-600 hover:text-purple-800 transition-colors flex items-center"
                    >
                      <span class="mr-1">📊</span> Résultats
                    </Link>
                  </div>
                </td>
              </tr>
              <tr v-if="inscriptions.length === 0">
                <td colspan="8" class="text-center text-gray-500 py-8">
                  <div class="flex flex-col items-center">
                    <span class="text-lg mb-2">📚</span>
                    Aucune inscription enregistrée dans ce cycle.
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