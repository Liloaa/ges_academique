<template>
  <div class="p-8 bg-gray-100 min-h-screen">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📘 Mes Notes</h1>

    <div v-if="notes.length === 0" class="text-gray-600 italic">
      Vous n’avez encore aucune note enregistrée.
    </div>

    <table v-else class="min-w-full bg-white shadow rounded-lg overflow-hidden">
      <thead class="bg-blue-600 text-white">
        <tr>
          <th class="px-6 py-3 text-left">Matière</th>
          <th class="px-6 py-3 text-left">Trimestre</th>
          <th class="px-6 py-3 text-left">Note</th>
          <th class="px-6 py-3 text-left">Enseignant</th>
          <th class="px-6 py-3 text-left">Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="n in notes" :key="n.id" class="border-b hover:bg-gray-50">
          <td class="px-6 py-3">{{ n.matiere?.nomMatiere || '—' }}</td>
          <td class="px-6 py-3">{{ n.trimestre }}</td>
          <td class="px-6 py-3 font-bold text-blue-600">{{ n.note }}/20</td>
          <td class="px-6 py-3">{{ n.enseignant ? n.enseignant.nom + ' ' + n.enseignant.prenom : '—' }}</td>
          <td class="px-6 py-3">{{ formatDate(n.date_saisie) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  notes: Array,
  eleve: Object,
})

function formatDate(date) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString()
}
</script>
