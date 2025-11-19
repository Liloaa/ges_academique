<script setup>
import { Head, router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const form = ref({
  matricule: '',
  nom: '',
  prenom: '',
  grade: '',
  specialite: '',
  email: '',
  telephone: '',
  sexe: '',
  adresse: '',
  password: '',
  password_confirmation: '',
})

const submit = async () => {
  try {
    await router.post(route('enseignants.store'), form.value)
  } catch (error) {
    console.error('Erreur lors de la création :', error)
    alert('❌ Erreur : ' + (error.message || 'Création échouée'))
  }
}
</script>

<template>
  <Head title="Ajouter un Enseignant" />

  <div class="p-8 max-w-3xl mx-auto bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">➕ Ajouter un Enseignant</h1>

    <form @submit.prevent="submit" class="grid grid-cols-2 gap-4">
      <input v-model="form.matricule" type="text" placeholder="Matricule" class="input" required />
      <input v-model="form.nom" type="text" placeholder="Nom" class="input" required />
      <input v-model="form.prenom" type="text" placeholder="Prénom" class="input" required />
      <input v-model="form.grade" type="text" placeholder="Grade" class="input" />
      <input v-model="form.specialite" type="text" placeholder="Spécialité" class="input" />
      <input v-model="form.email" type="email" placeholder="Email" class="input" required />
      <input v-model="form.telephone" type="text" placeholder="Téléphone" class="input" />
      <select v-model="form.sexe" class="input">
        <option value="">Sexe</option>
        <option value="Homme">Homme</option>
        <option value="Femme">Femme</option>
      </select>
      <textarea v-model="form.adresse" placeholder="Adresse" class="col-span-2 input"></textarea>

      <input
        v-model="form.password"
        type="password"
        placeholder="Mot de passe"
        class="input col-span-1"
        required
      />
      <input
        v-model="form.password_confirmation"
        type="password"
        placeholder="Confirmer le mot de passe"
        class="input col-span-1"
        required
      />

      <!-- 🔹 Boutons -->
      <div class="col-span-2 flex justify-between items-center mt-4">
        <!-- Bouton Retour -->
        <Link
          :href="route('enseignants.index')"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>

        <!-- Bouton Enregistrer -->
        <button
          type="submit"
          class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition"
        >
          💾 Enregistrer
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.input {
  @apply border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400;
}
</style>
