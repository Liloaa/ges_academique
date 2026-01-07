<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const form = useForm({
    libelle: '',
})

const submit = () => {
  form.post('/admin/anneesscolaires')
}
</script>

<template>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4 text-center">Ajouter une année scolaire</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-gray-700">Libellé :</label>
                <input
                    v-model="form.libelle"
                    type="text"
                    placeholder="Ex : 2025–2026"
                    class="border rounded px-3 py-2 w-full"
                />
                <span
                    v-if="form.errors.libelle"
                    class="text-red-500 text-sm"
                >
                    {{ form.errors.libelle }}
                </span>
            </div>

            <div class="flex justify-between items-center pt-2">
                <Link
                    :href="route('anneesscolaires.index')"
                    class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 transition"
                >
                    ← Retour
                </Link>

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Enregistrement...' : '💾 Enregistrer l\'année scolaire' }}
                </button>
            </div>
        </form>
    </div>
</template>
