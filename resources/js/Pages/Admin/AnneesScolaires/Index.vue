<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })


const props = defineProps({
    annees: Array,
})

// --- Fonctions ---
function supprimer(id) {
    if (confirm('Voulez-vous vraiment supprimer cette année scolaire ?')) {
        router.delete(route('anneesscolaires.destroy', id))
    }
}

function activer(id) {
    router.put(route('anneesscolaires.active', id))
}
</script>

<template>
    <div class="max-w-6xl mx-auto mt-10 px-6">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">
            Gestion des années scolaires
        </h1>

        <!-- Boutons d’action -->
        <div class="flex justify-between mb-4">
            <!-- Bouton retour
            <Link
                href="/admin"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition"
            >
                ⬅️ Retour à l'acceuil
            </Link>-->

            <!-- Bouton ajout -->
            <Link
                :href="route('anneesscolaires.create')"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition"
            >
                ➕ Ajouter une année
            </Link>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-semibold">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-semibold">Année scolaire</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">Statut</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(anneesscolaire, index) in annees"
                        :key="anneesscolaire.id"
                        class="border-t hover:bg-gray-50 transition"
                    >
                        <td class="px-4 py-2">{{ index + 1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ anneesscolaire.libelle }}</td>
                        <td class="text-center">
                            <span
                                :class="anneesscolaire.active
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-200 text-gray-600'"
                                class="px-2 py-1 rounded text-sm font-medium"
                            >
                                {{ anneesscolaire.active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center space-x-2">
                            <Link
                                :href="route('anneesscolaires.edit', anneesscolaire.id)"
                                class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                🖋️ Modifier
                            </Link>
                            <button
                                @click="supprimer(anneesscolaire.id)"
                                class="text-red-600 hover:text-red-800 font-medium"
                            >
                                ❌ Supprimer
                            </button>
                            <button
                                v-if="!anneesscolaire.active"
                                @click="activer(anneesscolaire.id)"
                                class="text-green-600 hover:text-green-800 font-medium"
                            >
                                ✅ Activer
                            </button>
                        </td>
                    </tr>

                    <tr v-if="annees.length === 0">
                        <td colspan="4" class="text-center py-4 text-gray-500">
                            Aucune année scolaire enregistrée.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
