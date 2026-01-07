<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

// Ajoutez defineProps pour recevoir les filières
const props = defineProps({
  filieres: Array
})

const form = useForm({
  nomNiveau: '',
  description: '',
  cycle: 'primaire',
  filiere_id: null,
})

const submit = () => {
  form.post('/admin/niveaux')
}
</script>

<template>
  <Head title="Ajouter un Niveau" />

  <div class="p-8 max-w-2xl mx-auto bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">➕ Ajouter un Niveau</h1>

    <form @submit.prevent="submit" class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Nom du Niveau *
        </label>
        <input
          type="text"
          v-model="form.nomNiveau"
          required
          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
          placeholder="Ex: 11ème A, 2nde S B..."
        />
        <div v-if="form.errors.nomNiveau" class="text-red-600 text-sm mt-1">
          {{ form.errors.nomNiveau }}
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Description
        </label>
        <textarea
          v-model="form.description"
          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
          placeholder="Description optionnelle..."
        ></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Cycle *
        </label>
        <select
          v-model="form.cycle"
          required
          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
        >
          <option value="primaire">Primaire</option>
          <option value="college">Collège</option>
          <option value="lycee">Lycée</option>
        </select>
        <div v-if="form.errors.cycle" class="text-red-600 text-sm mt-1">
          {{ form.errors.cycle }}
        </div>
      </div>

      <!-- Correction : utilisez props.filieres au lieu de $page.props.filieres -->
      <div v-if="form.cycle === 'lycee'">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Filière *
        </label>
        <select
          v-model="form.filiere_id"
          :required="form.cycle === 'lycee'"
          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
        >
          <option value="">Sélectionnez une filière</option>
          <option v-for="filiere in filieres" :key="filiere.id" :value="filiere.id">
            {{ filiere.nomFiliere }}
          </option>
        </select>
        <div v-if="form.errors.filiere_id" class="text-red-600 text-sm mt-1">
          {{ form.errors.filiere_id }}
        </div>
      </div>

      <div class="flex justify-between items-center pt-2">
        <Link href="/admin/niveaux" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition">
          ← Retour
        </Link>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Enregistrement...' : '💾Enregistrer le niveau' }}
        </button>
      </div>
    </form>
  </div>
</template>