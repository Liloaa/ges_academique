<script setup>
import { useForm, Link } from '@inertiajs/vue3' // Importez useForm
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  eleves: Array,
  salles: Array,
  annees: Array,
})

// Utilisez useForm au lieu de reactive pour gérer les erreurs automatiquement
const form = useForm({
  eleve_id: '',
  salle_id: '',
  annee_scolaire_id: '',
  date_inscription: '',
  etat: 'active',
})

function submit() {
  form.post(route('inscriptions.store'), {
    onSuccess: () => {
      // Redirection après succès (optionnel)
      form.reset()
    },
    onError: (errors) => {
      console.log('Erreurs:', errors)
    }
  })
}
</script>

<template>
  <Head title="Nouvelle inscription" />

  <div class="p-6 max-w-3xl mx-auto bg-white shadow rounded-lg space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">➕ Nouvelle inscription</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <!-- Élève -->
      <div>
        <label class="block mb-1">Élève :</label>
        <select v-model="form.eleve_id" class="w-full border rounded p-2" :class="{ 'border-red-500': form.errors.eleve_id }">
          <option value="">-- Sélectionner un élève --</option>
          <option v-for="e in eleves" :key="e.id" :value="e.id">
            {{ e.nom }} {{ e.prenom }}
          </option>
        </select>
        <p v-if="form.errors.eleve_id" class="text-red-600 text-sm mt-1">{{ form.errors.eleve_id }}</p>
      </div>

      <!-- Salle -->
      <div>
        <label class="block mb-1">Salle :</label>
        <select v-model="form.salle_id" class="w-full border rounded p-2" :class="{ 'border-red-500': form.errors.salle_id }">
          <option value="">-- Sélectionner une salle --</option>
          <option v-for="s in salles" :key="s.id" :value="s.id">
            {{ s.nomSalle }}
          </option>
        </select>
        <p v-if="form.errors.salle_id" class="text-red-600 text-sm mt-1">{{ form.errors.salle_id }}</p>
      </div>

      <!-- Année scolaire -->
      <div>
        <label class="block mb-1">Année scolaire :</label>
        <select v-model="form.annee_scolaire_id" class="w-full border rounded p-2" :class="{ 'border-red-500': form.errors.annee_scolaire_id }">
          <option value="">-- Sélectionner une année --</option>
          <option v-for="a in annees" :key="a.id" :value="a.id">
            {{ a.libelle }}
          </option>
        </select>
        <p v-if="form.errors.annee_scolaire_id" class="text-red-600 text-sm mt-1">{{ form.errors.annee_scolaire_id }}</p>
      </div>

      <!-- Date inscription -->
      <div>
        <label class="block mb-1">Date d'inscription :</label>
        <input
          type="date"
          v-model="form.date_inscription"
          class="w-full border rounded p-2"
          :class="{ 'border-red-500': form.errors.date_inscription }"
          required
        />
        <p v-if="form.errors.date_inscription" class="text-red-600 text-sm mt-1">{{ form.errors.date_inscription }}</p>
      </div>

      <!-- État -->
      <div>
        <label class="block mb-1">État :</label>
        <select v-model="form.etat" class="w-full border rounded p-2">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <p v-if="form.errors.etat" class="text-red-600 text-sm mt-1">{{ form.errors.etat }}</p>
      </div>

      <!-- Messages d'erreur généraux -->
      <div v-if="form.hasErrors" class="bg-red-50 border border-red-200 text-red-700 p-3 rounded">
        <p class="font-semibold">Veuillez corriger les erreurs ci-dessous :</p>
        <ul v-if="Object.keys(form.errors).length > 1" class="list-disc ml-4 mt-1">
          <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
        </ul>
      </div>

      <!-- Boutons -->
      <div class="flex justify-between items-center pt-4">
        <Link
          :href="route('inscriptions.index')"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Enregistrement...' : '💾 Enregistrer' }}
        </button>
      </div>
    </form>
  </div>
</template>