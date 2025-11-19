<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  matieres: Array,
  inscriptions: Array,
  enseignants: Array,
})

const form = reactive({
  inscription_id: '',
  matiere_id: '',
  enseignant_id: '',
  trimestre: '1er',
  note: '',
  date_saisie: '',
})

// Pour stocker les erreurs
const errors = reactive({
  inscription_id: '',
  matiere_id: '',
  trimestre: '',
  note: '',
})

function submit() {
  // On réinitialise les erreurs avant chaque soumission
  Object.keys(errors).forEach((key) => (errors[key] = ''))

  router.post(route('notes.store'), form, {
    onError: (e) => {
      // Vérifie si l’erreur est un tableau ou une chaîne
      Object.keys(e).forEach((key) => {
        if (errors[key] !== undefined) {
          errors[key] = Array.isArray(e[key]) ? e[key][0] : e[key]
        }
      })
    },
  })
}
</script>

<template>
  <Head title="Ajouter une note" />

  <div class="p-8 max-w-2xl mx-auto bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">➕ Nouvelle note</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <!-- Élève -->
      <div>
        <label>Élève</label>
        <select
          v-model="form.inscription_id"
          class="w-full border rounded px-3 py-2"
          :class="{ 'border-red-500': errors.inscription_id }"
        >
          <option disabled value="">Sélectionner un élève</option>
          <option
            v-for="ins in inscriptions"
            :value="ins.id"
            :key="ins.id"
          >
            {{ ins.eleve.nom }} {{ ins.eleve.prenom }}
          </option>
        </select>
        <p v-if="errors.inscription_id" class="text-red-500 text-sm mt-1">
          {{ errors.inscription_id }}
        </p>
      </div>

      <!-- Matière -->
      <div>
        <label>Matière</label>
        <select
          v-model="form.matiere_id"
          class="w-full border rounded px-3 py-2"
          :class="{ 'border-red-500': errors.matiere_id }"
        >
          <option disabled value="">Sélectionner une matière</option>
          <option v-for="m in matieres" :value="m.id" :key="m.id">
            {{ m.nomMatiere }}
          </option>
        </select>
        <p v-if="errors.matiere_id" class="text-red-500 text-sm mt-1">
          {{ errors.matiere_id }}
        </p>
      </div>

      <!-- Trimestre -->
      <div>
        <label>Trimestre</label>
        <select
          v-model="form.trimestre"
          class="w-full border rounded px-3 py-2"
          :class="{ 'border-red-500': errors.trimestre }"
        >
          <option>1er</option>
          <option>2ème</option>
          <option>3ème</option>
        </select>
        <p v-if="errors.trimestre" class="text-red-500 text-sm mt-1">
          {{ errors.trimestre }}
        </p>
      </div>

      <!-- Note -->
      <div>
        <label>Note</label>
        <input
          type="number"
          step="0.01"
          v-model="form.note"
          class="w-full border rounded px-3 py-2"
          :class="{ 'border-red-500': errors.note }"
        />
        <p v-if="errors.note" class="text-red-500 text-sm mt-1">
          {{ errors.note }}
        </p>
      </div>

      <!-- Enseignant -->
      <div>
        <label>Enseignant</label>
        <select v-model="form.enseignant_id" class="w-full border rounded px-3 py-2">
          <option disabled value="">Sélectionner un enseignant</option>
          <option v-for="e in enseignants" :value="e.id" :key="e.id">
            {{ e.nom }} {{ e.prenom }}
          </option>
        </select>
      </div>

      <!-- Date -->
      <div>
        <label>Date de saisie</label><br>
        <input v-model="form.date_saisie" type="date" class="border rounded px-3 py-2" />
      </div>

      <!-- Boutons -->
      <div class="flex justify-between items-center pt-6">
        <Link
          href="/admin/notes"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
          💾 Enregistrer
        </button>
      </div>
    </form>
  </div>
</template>
