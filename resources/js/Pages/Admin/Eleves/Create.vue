<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  filieres: Array,
  niveaux: Array,
  salles: Array,
  annees: Array
})

const form = reactive({
  matricule: '',
  nom: '',
  prenom: '',
  date_naissance: '',
  email: '',
  sexe: '',
  adresse: '',
  telephone: '',
  filiere_id: '',
  niveau_id: '',
  salle_id: '',
  annee_scolaire_id: '',
  password: '',
  password_confirmation: ''
})

const submit = () => {
  router.post(route('eleves.store'), form, {
    onSuccess: () => {
      alert('✅ Élève enregistré avec succès !')
    },
    onError: (errors) => {
      console.error('❌ Erreur:', errors)
      alert('Erreur : ' + JSON.stringify(errors))
    }
  })
}
</script>

<template>
  <Head title="Ajouter un Élève" />

  <div class="p-6 max-w-4xl mx-auto bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">➕ Ajouter un Élève</h1>

    <form @submit.prevent="submit" class="space-y-4">

      <!-- Identité -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>Matricule</label>
          <input v-model="form.matricule" type="text" class="input" required />
        </div>
        <div>
          <label>Sexe</label>
          <select v-model="form.sexe" class="input" required>
            <option value="">-- Choisir --</option>
            <option value="Masculin">Masculin</option>
            <option value="Féminin">Féminin</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>Nom</label>
          <input v-model="form.nom" type="text" class="input" required />
        </div>
        <div>
          <label>Prénom</label>
          <input v-model="form.prenom" type="text" class="input" required />
        </div>
      </div>

      <div>
        <label>Date de naissance</label>
        <input v-model="form.date_naissance" type="date" class="input" />
      </div>

      <!-- Contact -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>Email</label>
          <input v-model="form.email" type="email" class="input" />
        </div>
        <div>
          <label>Téléphone</label>
          <input v-model="form.telephone" type="text" class="input" />
        </div>
      </div>

      <div>
        <label>Adresse</label>
        <input v-model="form.adresse" type="text" class="input" />
      </div>

      <!-- Relations -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>Filière</label>
          <select v-model="form.filiere_id" class="input" >
            <option value="">-- Choisir --</option>
            <option v-for="f in props.filieres" :key="f.id" :value="f.id">
              {{ f.nomFiliere }}
            </option>
          </select>
        </div>
        <div>
          <label>Niveau</label>
          <select v-model="form.niveau_id" class="input" required>
            <option value="">-- Choisir --</option>
            <option v-for="n in props.niveaux" :key="n.id" :value="n.id">
              {{ n.nomNiveau }}
            </option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>Salle</label>
          <select v-model="form.salle_id" class="input" required>
            <option value="">-- Choisir --</option>
            <option v-for="s in props.salles" :key="s.id" :value="s.id">
              {{ s.nomSalle }}
            </option>
          </select>
        </div>
        <div>
          <label>Année scolaire</label>
          <select v-model="form.annee_scolaire_id" class="input" required>
            <option value="">-- Choisir --</option>
            <option v-for="a in props.annees" :key="a.id" :value="a.id">
              {{ a.libelle }}
            </option>
          </select>
        </div>
      </div>

      <!-- Mot de passe -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>Mot de passe</label>
          <input v-model="form.password" type="password" class="input" required />
        </div>
        <div>
          <label>Confirmation</label>
          <input v-model="form.password_confirmation" type="password" class="input" required />
        </div>
      </div>

      <!-- Boutons -->
      <div class="flex justify-between items-center pt-4">
        <Link
          href="/admin/eleves"
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

<style scoped>
.input {
  @apply w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200;
}
</style>
