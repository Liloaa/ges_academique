<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  salle: Object,
  filieres: Array,
  niveaux: Array
})

const form = useForm({
  nomSalle: props.salle.nomSalle || '',
  capacite: props.salle.capacite || '',
  filiere_id: props.salle.filiere_id || '',
  niveau_id: props.salle.niveau_id || ''
})

function submit() {
  form.put(`/admin/salles/${props.salle.id}`)
}
</script>

<template>
  <div class="p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-bold mb-4">✏️ Modifier la salle</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label>Nom de la salle</label>
        <input v-model="form.nomSalle" class="w-full border rounded p-2" required />
      </div>

      <div>
        <label>Capacité</label>
        <input v-model="form.capacite" type="number" class="w-full border rounded p-2" required />
      </div>

      <div>
        <label>Filière</label>
        <select v-model="form.filiere_id" class="w-full border rounded p-2">
          <option value="">-- Choisir --</option>
          <option v-for="f in props.filieres" :key="f.id" :value="f.id">{{ f.nomFiliere }}</option>
        </select>
      </div>

      <div>
        <label>Niveau</label>
        <select v-model="form.niveau_id" class="w-full border rounded p-2">
          <option value="">-- Choisir --</option>
          <option v-for="n in props.niveaux" :key="n.id" :value="n.id">{{ n.nomNiveau }}</option>
        </select>
      </div>

      <div class="flex justify-between">
        <Link href="/admin/salles" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition">← Retour</Link>
        <button class="bg-green-600 text-white px-4 py-2 rounded">✅ Mettre à jour</button>
      </div>
    </form>
  </div>
</template>
