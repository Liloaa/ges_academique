<template>
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex-shrink-0">
      <div class="p-6 font-bold text-lg border-b border-gray-700">
        👩🏻‍🎓 Espace Élève
      </div>

      <nav class="mt-6">
        <ul class="space-y-2">
          <li v-for="link in navLinks" :key="link.name">
            <Link
              :href="link.href"
              class="block px-4 py-2 rounded hover:bg-gray-700 transition"
              :class="{ 'bg-gray-800': $page.url.startsWith(link.href) }"
            >
              {{ link.icon }} {{ link.name }}
            </Link>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Contenu principal -->
    <main class="flex-1 p-8 overflow-y-auto">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Liens du menu élève
const navLinks = [
  { name: 'Tableau de bord', href: '/eleve', icon: '📊' },
  { name: 'Mes Notes', href: '/eleve/notes', icon: '📝' },
  { name: 'Profil', href: '/eleve/profil', icon: '👤' },
  { name: 'Déconnexion', href: '/logout', icon: '🚪' },
]
</script>

<style scoped>
aside {
  @apply h-screen;
}
</style>
