<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({ anneesscolaire: Object })

const form = useForm({
    libelle: props.anneesscolaire.libelle,
})

function submit() {
    form.put(route('anneesscolaires.update', props.anneesscolaire.id), {
        onSuccess: () => form.reset(),
        onError: (errors) => console.error('Form errors:', errors),
    })
}
</script>

<template>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4 text-center">Modifier l’année scolaire</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-gray-700">Libellé :</label>
                <input
                    v-model="form.libelle"
                    type="text"
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
                    ← Retour à la liste
                </Link>

                <button
                    type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                    :disabled="form.processing"
                >
                    ✅ Mettre à jour
                </button>
            </div>
        </form>
    </div>
</template>
