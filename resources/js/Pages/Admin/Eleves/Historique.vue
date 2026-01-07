<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  eleve: Object,
  inscriptions: Array,
})

const downloading = ref(false)
/*
function telechargerPDF() {
  try {
    downloading.value = true
    router.get(route('eleves.historique.pdf', props.eleve.id), {}, {
      onFinish: () => (downloading.value = false),
      onError: (err) => {
        console.error('Erreur génération PDF :', err)
        downloading.value = false
      },
    })
  } catch (err) {
    console.error('Erreur inattendue :', err)
    downloading.value = false
  }
}*/
</script>

<template>
  <Head :title="`Historique de ${props.eleve.nom}`" />

  <div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">
        🧾 Historique de l’élève :
        <span class="text-blue-600">{{ eleve.nom }} {{ eleve.prenom }}</span>
      </h1>

      <button
        @click="router.visit(route('eleves.index'))"
        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
      >
        ← Retour
      </button>
    </div>

    <!-- Informations élève -->
    <div class="bg-white p-4 rounded-lg shadow space-y-2">
      <p><strong>Matricule :</strong> {{ eleve.matricule }}</p>
    </div>

    <!-- Historique des inscriptions -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-700">📚 Inscriptions passées</h2>

        <!--<button
          @click="telechargerPDF"
          :disabled="downloading"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:opacity-60"
        >
          <span v-if="!downloading">⬇️ Télécharger PDF</span>
          <span v-else>⏳ Génération...</span>
        </button>-->
      </div>

      <table class="min-w-full border text-sm bg-white rounded-lg">
        <thead class="bg-gray-200 text-gray-700">
          <tr>
            <th class="px-4 py-2 text-left">Année scolaire</th>
            <th class="px-4 py-2 text-left">Salle</th>
            <th class="px-4 py-2 text-left">Date</th>
            <th class="px-4 py-2 text-left">État</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="ins in inscriptions"
            :key="ins.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-4 py-2">{{ ins.annee?.libelle }}</td>
            <td class="px-4 py-2">{{ ins.salle?.nomSalle }}</td>
            <td class="px-4 py-2">{{ ins.date_inscription }}</td>
            <td class="px-4 py-2">
              <span
                :class="ins.etat === 'active' ? 'text-green-600' : 'text-red-500'"
              >
                {{ ins.etat }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-if="!inscriptions.length" class="text-center text-gray-500 py-4">
        Aucune inscription enregistrée pour cet élève.
      </p>
    </div>
  </div>
</template>