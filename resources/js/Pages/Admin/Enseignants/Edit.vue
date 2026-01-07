<script setup>
import { Head, router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  enseignant: Object,
})

const form = ref({
  matricule: props.enseignant.matriculeEnseig,
  nom: props.enseignant.nom,
  prenom: props.enseignant.prenom,
  grade: props.enseignant.grade,
  specialite: props.enseignant.specialite,
  email: props.enseignant.email,
  telephone: props.enseignant.telephone,
  sexe: props.enseignant.sexe,
  adresse: props.enseignant.adresse,
})

const submit = async () => {
  try {
    await router.put(route('enseignants.update', props.enseignant.id), form.value)
  } catch (error) {
    console.error('Erreur lors de la mise à jour :', error)
    alert('❌ Erreur : ' + (error.message || 'Mise à jour échouée'))
  }
}
</script>

<template>
  <Head title="Modifier Enseignant" />

  <div class="p-8 max-w-3xl mx-auto bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Modifier l’Enseignant</h1>

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

      <div class="col-span-2 flex justify-between mt-4">
        <!-- 🔙 Bouton Retour -->
        <Link
          :href="route('enseignants.index')"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>

        <!-- ✅ Bouton Mise à jour -->
        <button
          type="submit"
          class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Mise à jour...' : '✅ Mettre à jour' }}
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.input {
  @apply border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-400;
}
</style>
