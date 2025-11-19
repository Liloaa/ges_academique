<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  note: Object,
  matieres: Array,
  inscriptions: Array,
  enseignants: Array,
})

const form = reactive({
  inscription_id: props.note.inscription_id || '',
  matiere_id: props.note.matiere_id || '',
  enseignant_id: props.note.enseignant_id || '',
  trimestre: props.note.trimestre || '1er',
  note: props.note.note || '',
  date_saisie: props.note.date_saisie || '',
})

function submit() {
  router.put(route('notes.update', props.note.id), form, {
    onSuccess: () => console.log('✅ Note mise à jour avec succès.'),
    onError: (errors) => {
      console.error('❌ Erreur lors de la mise à jour :', errors)
      alert('Erreur : ' + JSON.stringify(errors, null, 2))
    },
    onFinish: () => console.log('🔁 Requête terminée.'),
  })
}
</script>

<template>
  <Head title="Modifier une note" />

  <div class="p-8 max-w-2xl mx-auto bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">✏️ Modifier la Note</h1>

    <form @submit.prevent="submit" class="space-y-5">
      <!-- Élève -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Élève</label>
        <select v-model="form.inscription_id" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
          <option disabled value="">Sélectionner un élève</option>
          <option v-for="ins in inscriptions" :key="ins.id" :value="ins.id">
            {{ ins.eleve.nom }} {{ ins.eleve.prenom }}
          </option>
        </select>
      </div>

      <!-- Matière -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
        <select v-model="form.matiere_id" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
          <option disabled value="">Sélectionner une matière</option>
          <option v-for="m in matieres" :key="m.id" :value="m.id">{{ m.nomMatiere }}</option>
        </select>
      </div>

      <!-- Trimestre -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Trimestre</label>
        <select v-model="form.trimestre" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
          <option>1er</option>
          <option>2ème</option>
          <option>3ème</option>
        </select>
      </div>

      <!-- Note -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Note (/20)</label>
        <input
          type="number"
          step="0.01"
          min="0"
          max="20"
          v-model="form.note"
          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
        />
      </div>

      <!-- Enseignant -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Enseignant</label>
        <select v-model="form.enseignant_id" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
          <option disabled value="">Sélectionner un enseignant</option>
          <option v-for="e in enseignants" :key="e.id" :value="e.id">
            {{ e.nom }} {{ e.prenom }}
          </option>
        </select>
      </div>

      <!-- Date de saisie -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Date de saisie</label>
        <input
          type="date"
          v-model="form.date_saisie"
          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
        />
      </div>

      <!-- Boutons -->
      <div class="flex justify-between items-center pt-4">
        <Link
          href="/admin/notes"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>

        <button
          type="submit"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
        >
          ✅ Mettre à jour
        </button>
      </div>
    </form>
  </div>
</template>
