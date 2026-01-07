<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const form = useForm({
  matricule: '',
  nom: '',
  prenom: '',
  date_naissance: '',
  email: '',
  sexe: '',
  adresse: '',
  telephone: '',
  password: '',
  password_confirmation: ''
})

const submit = () => {
  form.post('/admin/eleves')
}
</script>

<template>
  <Head title="Ajouter un Élève" />

  <div class="p-8 max-w-2xl mx-auto bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">➕ Ajouter un Élève</h1>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Informations personnelles -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Matricule *
          </label>
          <input 
            v-model="form.matricule" 
            type="text" 
            required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Ex: El001"
          />
          <div v-if="form.errors.matricule" class="text-red-600 text-sm mt-1">
            {{ form.errors.matricule }}
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Sexe
          </label>
          <select 
            v-model="form.sexe" 
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">-- Choisir --</option>
            <option value="Masculin">Masculin</option>
            <option value="Féminin">Féminin</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Nom *
          </label>
          <input 
            v-model="form.nom" 
            type="text" 
            required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Prénom *
          </label>
          <input 
            v-model="form.prenom" 
            type="text" 
            required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Date de naissance
        </label>
        <input 
          v-model="form.date_naissance" 
          type="date" 
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Informations de contact -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Email *
          </label>
          <input 
            v-model="form.email" 
            type="email" 
            required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="exemple@email.com"
          />
          <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
            {{ form.errors.email }}
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Téléphone
          </label>
          <input 
            v-model="form.telephone" 
            type="text" 
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Adresse
        </label>
        <textarea 
          v-model="form.adresse" 
          rows="3"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <!-- Compte utilisateur -->
      <div class="border-t pt-4">
        <h3 class="text-lg font-medium text-gray-800 mb-4">📧 Compte utilisateur</h3>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mot de passe *
            </label>
            <input 
              v-model="form.password" 
              type="password" 
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
              {{ form.errors.password }}
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Confirmation *
            </label>
            <input 
              v-model="form.password_confirmation" 
              type="password" 
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>
      </div>

      <!-- Boutons -->
      <div class="flex justify-between items-center pt-6">
        <Link
          href="/admin/eleves"
          class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Création...' : '💾 Créer l\'élève' }}
        </button>
      </div>
    </form>
  </div>
</template>