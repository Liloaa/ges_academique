<template>
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex-shrink-0">
      <div class="p-6 font-bold text-lg">👩🏻‍🏫 Espace Élève</div>
      <nav class="mt-6">
        <ul class="space-y-2">
          <li v-for="link in navLinks" :key="link.name">
            <Link :href="link.href" class="block px-4 py-2 rounded hover:bg-gray-700 transition">
              {{ link.icon }} {{ link.name }}
            </Link>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Contenu principal -->
    <main class="flex-1 p-6 overflow-y-auto">
      <!-- En-tête -->
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de bord - Élèves</h1>

      <!-- Cartes de résumé -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <DashboardCard title="Total Élèves" :value="totalEleves" color="text-green-600" />
        <DashboardCard title="Inscriptions Actives" :value="inscriptionsActives" color="text-blue-600" />
        <DashboardCard title="Filières Disponibles" :value="filieresCount" color="text-indigo-600" />
        <DashboardCard title="Année Scolaire" value="2025" color="text-orange-600" />
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { defineProps } from 'vue'

defineProps({
  eleves: Array,
  totalEleves: Number,
  inscriptionsActives: Number,
  filieresCount: Number,
})

// Liens du menu
const navLinks = [
  { name: 'Tableau de bord', href: '/eleve', icon: '📊' },/*
  { name: 'Profil', href: '/eleve/profil', icon: '👤' },
  { name: 'Inscriptions', href: '/eleve/inscriptions', icon: '📇' },
  { name: 'Notes', href: '/eleve/notes', icon: '📝' },
  { name: 'Filières', href: '/eleve/filieres', icon: '🎓' },
  { name: 'Déconnexion', href: '/logout', icon: '🚪' },*/
]

// Fonction de suppression
const supprimer = (id) => {
  if (confirm('Voulez-vous vraiment supprimer cet élève ?')) {
    router.delete(route('eleves.destroy', id))
  }
}
</script>

<!-- Composant de carte -->
<script>
export default {
  components: {
    DashboardCard: {
      props: ['title', 'value', 'color'],
      template: `
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-gray-600 font-semibold">{{ title }}</h2>
          <p :class="['text-2xl font-bold mt-2', color]">{{ value }}</p>
        </div>
      `,
    },
  },
}
</script>

<style scoped>
.btn {
  @apply px-3 py-1 text-white rounded-lg shadow transition text-sm;
}
</style>
