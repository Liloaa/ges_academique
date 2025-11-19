<script setup>
import { Head, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  inscription: Object,
  eleves: Array,
  salles: Array,
  annees: Array,
})

const form = reactive({
  eleve_id: props.inscription.eleve_id,
  salle_id: props.inscription.salle_id,
  annee_scolaire_id: props.inscription.annee_scolaire_id,
  date_inscription: props.inscription.date_inscription,
  etat: props.inscription.etat,
})

function update() {
  router.put(route('inscriptions.update', props.inscription.id), form, {
    onSuccess: () => router.visit(route('inscriptions.index')),
    onError: (err) => console.error('Erreur modification :', err),
  })
}
</script>

<template>
  <Head title="Modifier inscription" />

  <div class="p-6 max-w-3xl mx-auto bg-white shadow rounded-lg space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">✏️ Modifier une inscription</h1>

    <form @submit.prevent="update" class="space-y-4">
      <div>
        <label class="block mb-1">Élève :</label>
        <input
          type="text"
          :value="props.inscription.eleve.nom + ' ' + props.inscription.eleve.prenom"
          disabled
          class="w-full border rounded p-2 bg-gray-100"
        />
      </div>

      <div>
        <label class="block mb-1">Salle :</label>
        <select v-model="form.salle_id" class="w-full border rounded p-2">
          <option v-for="s in props.salles" :key="s.id" :value="s.id">
            {{ s.nomSalle }}
          </option>
        </select>
      </div>

      <div>
        <label class="block mb-1">Année scolaire :</label>
        <select v-model="form.annee_scolaire_id" class="w-full border rounded p-2">
          <option v-for="a in props.annees" :key="a.id" :value="a.id">
            {{ a.libelle }}
          </option>
        </select>
      </div>

      <div>
        <label class="block mb-1">Date d’inscription :</label>
        <input
          type="date"
          v-model="form.date_inscription"
          class="w-full border rounded p-2"
        />
      </div>

      <div>
        <label class="block mb-1">État :</label>
        <select v-model="form.etat" class="w-full border rounded p-2">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <div class="flex justify-between items-center pt-4">
        <button
          type="button"
          @click="router.visit(route('inscriptions.index'))"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </button>

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
