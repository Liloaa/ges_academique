<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })
const form = useForm({
  nomNiveau: '',
  description: '',
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
          Nom du Niveau
        </label>
        <input
          type="text"
          v-model="form.nomNiveau"
          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
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
        ></textarea>
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
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</template>
