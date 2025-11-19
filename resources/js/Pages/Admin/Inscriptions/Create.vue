<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  eleves: Array,
  salles: Array,
  annees: Array,
})

const form = reactive({
  eleve_id: '',
  salle_id: '',
  annee_scolaire_id: '',
  date_inscription: '',
  etat: 'active',
})

// Objet pour stocker les erreurs côté client
const errors = reactive({
  eleve_id: '',
  salle_id: '',
  annee_scolaire_id: '',
  date_inscription: '',
  etat: '',
})

function submit() {
  // On réinitialise les erreurs avant la soumission
  Object.keys(errors).forEach((key) => (errors[key] = ''))

  router.post(route('inscriptions.store'), form, {
    onError: (e) => {
      Object.keys(e).forEach((key) => {
        if (errors[key] !== undefined) {
          // Si c’est un tableau, on prend le premier message, sinon on prend directement la chaîne
          errors[key] = Array.isArray(e[key]) ? e[key][0] : e[key];
        }
      });
    },
    onSuccess: () => router.visit(route('inscriptions.index')),
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
        <select v-model="form.eleve_id" class="w-full border rounded p-2">
          <option value="">-- Sélectionner un élève --</option>
          <option v-for="e in props.eleves" :key="e.id" :value="e.id">
            {{ e.nom }} {{ e.prenom }}
          </option>
        </select>
        <p v-if="errors.eleve_id" class="text-red-600 text-sm mt-1">{{ errors.eleve_id }}</p>
      </div>

      <!-- Salle -->
      <div>
        <label class="block mb-1">Salle :</label>
        <select v-model="form.salle_id" class="w-full border rounded p-2">
          <option value="">-- Sélectionner une salle --</option>
          <option v-for="s in props.salles" :key="s.id" :value="s.id">
            {{ s.nomSalle }}
          </option>
        </select>
        <p v-if="errors.salle_id" class="text-red-600 text-sm mt-1">{{ errors.salle_id }}</p>
      </div>

      <!-- Année scolaire -->
      <div>
        <label class="block mb-1">Année scolaire :</label>
        <select v-model="form.annee_scolaire_id" class="w-full border rounded p-2">
          <option value="">-- Sélectionner une année --</option>
          <option v-for="a in props.annees" :key="a.id" :value="a.id">
            {{ a.libelle }}
          </option>
        </select>
        <p v-if="errors.annee_scolaire_id" class="text-red-600 text-sm mt-1">{{ errors.annee_scolaire_id }}</p>
      </div>

      <!-- Date inscription -->
      <div>
        <label class="block mb-1">Date d’inscription :</label>
        <input
          type="date"
          v-model="form.date_inscription"
          class="w-full border rounded p-2"
        />
        <p v-if="errors.date_inscription" class="text-red-600 text-sm mt-1">{{ errors.date_inscription }}</p>
      </div>

      <!-- État -->
      <div>
        <label class="block mb-1">État :</label>
        <select v-model="form.etat" class="w-full border rounded p-2">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <p v-if="errors.etat" class="text-red-600 text-sm mt-1">{{ errors.etat }}</p>
      </div>

      <!-- Boutons -->
      <div class="flex justify-between items-center pt-4">
        <Link
          href="/admin/inscriptions"
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
