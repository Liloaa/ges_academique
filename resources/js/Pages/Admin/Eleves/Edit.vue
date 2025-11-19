<script setup>
import { reactive } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  eleve: Object,
  filieres: Array,
  niveaux: Array,
  salles: Array,
  annees: Array
})

const form = reactive({
  matricule: props.eleve.matricule,
  nom: props.eleve.nom,
  prenom: props.eleve.prenom,
  date_naissance: props.eleve.date_naissance,
  email: props.eleve.email,
  sexe: props.eleve.sexe,
  adresse: props.eleve.adresse,
  telephone: props.eleve.telephone,
  filiere_id: props.eleve.filiere_id,
  niveau_id: props.eleve.niveau_id,
  salle_id: props.eleve.salle_id,
  annee_scolaire_id: props.eleve.annee_scolaire_id,
  password: '',
  password_confirmation: ''
})

const submit = () => {
  router.put(route('eleves.update', props.eleve.id), form, {
    onSuccess: () => alert('Élève mis à jour avec succès'),
    onError: (err) => {
      console.error(err)
      alert('Erreur: ' + JSON.stringify(err))
    }
  })
}
</script>

<template>
  <div class="p-6 max-w-4xl mx-auto bg-white shadow rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">✏️ Modifier un Élève</h1>

    <form @submit.prevent="submit" class="space-y-5">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>Matricule</label>
          <input v-model="form.matricule" type="text" class="input" required />
        </div>
        <div>
          <label>Sexe</label>
          <select v-model="form.sexe" class="input">
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

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>Filière</label>
          <select v-model="form.filiere_id" class="input">
            <option value="">-- Choisir --</option>
            <option v-for="f in props.filieres" :key="f.id" :value="f.id">
              {{ f.nomFiliere }}
            </option>
          </select>
        </div>
        <div>
          <label>Niveau</label>
          <select v-model="form.niveau_id" class="input">
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
          <select v-model="form.salle_id" class="input">
            <option value="">-- Choisir --</option>
            <option v-for="s in props.salles" :key="s.id" :value="s.id">
              {{ s.nomSalle }}
            </option>
          </select>
        </div>
        <div>
          <label>Année scolaire</label>
          <select v-model="form.annee_scolaire_id" class="input">
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
          <input
            v-model="form.password_confirmation"
            type="password"
            class="input"
            required
          />
        </div>
      </div>

      <div class="flex justify-between items-center pt-4">
        <!-- 🔙 Bouton Retour -->
        <Link
          href="/admin/eleves/index"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>

        <!-- ✅ Bouton de mise à jour -->
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

<style scoped>
.input {
  @apply w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200;
}
</style>
