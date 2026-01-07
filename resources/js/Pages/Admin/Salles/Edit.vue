<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  salle: Object,
  niveaux: Array
})

const form = useForm({
  nomSalle: props.salle.nomSalle || '',
  capacite: props.salle.capacite || '',
  niveau_id: props.salle.niveau_id || ''
})

function submit() {
  form.put(`/admin/salles/${props.salle.id}`)
}
</script>

<template>
  <div class="p-8 max-w-xl mx-auto bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">✏️ Modifier la salle</h1>

    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Nom de la salle *
        </label>
        <input 
          v-model="form.nomSalle" 
          type="text"
          required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
        <div v-if="form.errors.nomSalle" class="text-red-600 text-sm mt-1">
          {{ form.errors.nomSalle }}
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Capacité *
        </label>
        <input 
          v-model="form.capacite" 
          type="number" 
          min="1"
          required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
        <div v-if="form.errors.capacite" class="text-red-600 text-sm mt-1">
          {{ form.errors.capacite }}
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Niveau associé *
        </label>
        <select 
          v-model="form.niveau_id" 
          required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">-- Sélectionnez un niveau --</option>
          <option 
            v-for="niveau in niveaux" 
            :key="niveau.id" 
            :value="niveau.id"
          >
            {{ niveau.nomNiveau }} ({{ niveau.cycle }})
          </option>
        </select>
        <div v-if="form.errors.niveau_id" class="text-red-600 text-sm mt-1">
          {{ form.errors.niveau_id }}
        </div>
        <p class="text-sm text-gray-500 mt-1">
          Chaque niveau ne peut avoir qu'une seule salle
        </p>
      </div>

      <div class="flex justify-between items-center pt-4">
        <Link 
          href="/admin/salles" 
          class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>
        <button 
          type="submit"
          class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Mise à jour...' : '✅ Mettre à jour' }}
        </button>
      </div>
    </form>
  </div>
</template>