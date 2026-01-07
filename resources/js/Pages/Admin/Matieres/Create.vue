<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps({ niveaux: Array, enseignants: Array })

const form = useForm({
  nomMatiere: '',
  coefficient: 1,
  niveau_id: '',
  enseignant_id: '',
})

function submit() {
  form.post(route('matieres.store'))
}
</script>

<template>
  <div class="p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-bold mb-4">➕ Ajouter une matière</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label>Nom de la matière</label>
        <input v-model="form.nomMatiere" class="w-full border rounded p-2" required />
      </div>

      <div>
        <label>Coefficient</label>
        <input v-model="form.coefficient" type="number" min="0" class="w-full border rounded p-2" />
      </div>

      <div>
        <label>Niveau</label>
        <select v-model="form.niveau_id" class="w-full border rounded p-2">
          <option value="">-- Choisir --</option>
          <option v-for="n in niveaux" :key="n.id" :value="n.id">{{ n.nomNiveau }}</option>
        </select>
      </div>

      <div>
        <label>Enseignant</label>
        <select v-model="form.enseignant_id" class="w-full border rounded p-2">
          <option value="">-- Choisir --</option>
          <option v-for="e in enseignants" :key="e.id" :value="e.id">{{ e.nom }} {{ e.prenom }}</option>
        </select>
      </div>

      <div class="flex justify-between">
        <Link :href="route('matieres.index')" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition">← Retour</Link>
        <button 
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition" 
          :disabled="form.processing"
        >
          {{ form.processing ? 'Création...' : '💾 Créer la matiere' }}
        </button>
      </div>
    </form>
  </div>
</template>
