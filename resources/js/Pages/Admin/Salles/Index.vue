<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  salles: Array,
})

const confirmDelete = (id) => {
  if (confirm('Voulez-vous vraiment supprimer cette salle ?')) {
    router.delete(`/admin/salles/${id}`)
  }
}
</script>

<template>
  <Head title="Gestion des Salles" />

  <div class="p-8">
    <!-- 🔹 En-tête -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Salles</h1>
      <Link
        href="/admin/salles/create"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        ➕ Ajouter une salle
      </Link>
    </div>

    <!-- 🔹 Tableau -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
          <tr>
            <th class="px-6 py-3">#</th>
            <th class="px-6 py-3">Nom de la salle</th>
            <th class="px-6 py-3">Capacité</th>
            <th class="px-6 py-3">Effectif</th>
            <th class="px-6 py-3">Taux de remplissage</th>
            <th class="px-6 py-3">Niveau</th>
            <th class="px-6 py-3">Cycle</th>
            <th class="px-6 py-3">Filière</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(salle, index) in salles"
            :key="salle.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-6 py-3">{{ index + 1 }}</td>
            <td class="px-6 py-3 font-medium text-gray-900">{{ salle.nomSalle }}</td>
            <td class="px-6 py-3 text-center">{{ salle.capacite }}</td>
            <td class="px-6 py-3 text-center">{{ salle.effectif ?? 0 }}</td>

            <!-- Barre de remplissage -->
            <td class="px-6 py-3 text-center">
              <div class="flex items-center justify-center gap-2">
                <div class="w-24 bg-gray-200 rounded-full h-2">
                  <div
                    class="h-2 rounded-full transition-all"
                    :class="{
                      'bg-green-500': (salle.taux_remplissage || 0) <= 70,
                      'bg-yellow-500': (salle.taux_remplissage || 0) > 70 && (salle.taux_remplissage || 0) < 90,
                      'bg-red-500': (salle.taux_remplissage || 0) >= 90
                    }"
                    :style="{ width: (salle.taux_remplissage || 0) + '%' }"
                  ></div>
                </div>
                <span class="text-sm">{{ salle.taux_remplissage ?? 0 }}%</span>
              </div>
            </td>

            <td class="px-6 py-3">{{ salle.niveau?.nomNiveau || '—' }}</td>
            <td class="px-6 py-3">
              <span class="px-2 py-1 text-xs rounded-full" 
                :class="{
                  'bg-blue-100 text-blue-800': salle.niveau?.cycle === 'primaire',
                  'bg-green-100 text-green-800': salle.niveau?.cycle === 'college',
                  'bg-green-100 text-green-800': salle.niveau?.cycle === 'lycee'
                }">
                {{ salle.niveau?.cycle || '—' }}
              </span>
            </td>
            <td class="px-6 py-3">{{ salle.niveau?.filiere?.nomFiliere || '—' }}</td>

            <!-- Actions -->
            <td class="px-6 py-3 text-center">
              <Link
                :href="`/admin/salles/${salle.id}/edit`"
                class="text-blue-600 hover:text-blue-800 font-medium"
              >
                🖋️ Modifier
              </Link>
              <button
                @click="confirmDelete(salle.id)"
                class="text-red-600 hover:text-red-800 font-medium"
              >
                ❌ Supprimer
              </button>
            </td>
          </tr>

          <!-- Aucun résultat -->
          <tr v-if="salles.length === 0">
            <td colspan="9" class="text-center text-gray-500 py-6">
              Aucune salle enregistrée.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>