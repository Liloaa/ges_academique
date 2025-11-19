<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">📝 Détail de la Note</h1>
    <div class="bg-white rounded-lg shadow p-6 space-y-2">
      <p><strong>Matière :</strong> {{ note.matiere.nomMatiere }}</p>
      <p><strong>Trimestre :</strong> {{ note.trimestre }}</p>
      <p><strong>Note :</strong> <span class="text-blue-600 font-bold">{{ note.note }}/20</span></p>
      <p><strong>Enseignant :</strong> {{ note.enseignant.nom }} {{ note.enseignant.prenom }}</p>
      <p><strong>Date :</strong> {{ formatDate(note.date_saisie) }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({ note: Object })

function formatDate(date) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString()
}
</script>
