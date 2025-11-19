<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  matiere: Object,
  filieres: Array,
  niveaux: Array,
  enseignants: Array,
})

const form = useForm({
  nomMatiere: props.matiere.nomMatiere,
  coefficient: props.matiere.coefficient,
  filiere_id: props.matiere.filiere_id || '',
  niveau_id: props.matiere.niveau_id || '',
  enseignant_id: props.matiere.enseignant_id || '',
})

function submit() {
  form.put(route('matieres.update', props.matiere.id))
}
</script>

<template>
  <div class="p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-bold mb-4">✏️ Modifier une matière</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label>Nom de la matière</label>
        <input v-model="form.nomMatiere" class="w-full border rounded p-2" required />
      </div>

      <div>
        <label>Coefficient</label>
        <input
          v-model="form.coefficient"
          type="number"
          step="0.1"
          min="0"
          class="w-full border rounded p-2"
        />
      </div>

      <div>
        <label>Filière</label>
        <select v-model="form.filiere_id" class="w-full border rounded p-2">
          <option value="">-- Choisir --</option>
          <option v-for="f in filieres" :key="f.id" :value="f.id">{{ f.nomFiliere }}</option>
        </select>
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
          <option v-for="e in enseignants" :key="e.id" :value="e.id">
            {{ e.nom }} {{ e.prenom }}
          </option>
        </select>
      </div>

      <div class="flex justify-between">
        <Link :href="route('matieres.index')" class="text-gray-600">← Retour</Link>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Mettre à jour</button>
      </div>
    </form>
  </div>
</template>
