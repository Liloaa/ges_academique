<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

// ✅ Un seul defineProps()
const props = defineProps({
  filiere: Object,
})

const form = useForm({
  nomFiliere: props.filiere.nomFiliere,
  description: props.filiere.description || '',
})

const submit = () => {
  form.put(`/admin/filieres/${props.filiere.id}`)
}
</script>

<template>
  <Head title="Modifier la Filière" />

  <div class="p-8 max-w-2xl mx-auto bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">✏️ Modifier la Filière</h1>

    <form @submit.prevent="submit" class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Nom de la Filière
        </label>
        <input
          type="text"
          v-model="form.nomFiliere"
          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
        />
        <div v-if="form.errors.nomFiliere" class="text-red-600 text-sm mt-1">
          {{ form.errors.nomFiliere }}
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
        <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">
          {{ form.errors.description }}
        </div>
      </div>

      <div class="flex justify-between items-center pt-4">
        <Link
          href="/admin/filieres/index"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
        >
          ← Retour
        </Link>

        <button
          type="submit"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Mise à jour...' : '✅ Mettre à jour' }}
        </button>
      </div>
    </form>
  </div>
</template>
