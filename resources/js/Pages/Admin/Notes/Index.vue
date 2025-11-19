<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  notes: Array,
})

const search = ref('')
const selectedMatiere = ref('')

// Filtrage des notes
const filteredNotes = computed(() => {
  return props.notes.filter(note => {
    const matchesSearch =
      note.inscription?.eleve?.nom.toLowerCase().includes(search.value.toLowerCase()) ||
      note.inscription?.eleve?.prenom.toLowerCase().includes(search.value.toLowerCase())

    const matchesMatiere =
      !selectedMatiere.value || note.matiere?.nom === selectedMatiere.value

    return matchesSearch && matchesMatiere
  })
})

const effectif = computed(() => filteredNotes.value.length)

// Confirmation de suppression
const confirmDelete = async (id) => {
  if (confirm('Voulez-vous vraiment supprimer cette note ?')) {
    try {
      await router.delete(`/admin/notes/${id}`)
    } catch (error) {
      console.error('Erreur lors de la suppression :', error)
      alert('Erreur : ' + error.message)
    }
  }
}
</script>

<template>
  <Head title="Gestion des Notes" />

  <div class="p-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestion des Notes</h1>
      <Link
        href="/admin/notes/create"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        ➕ Nouvelle note
      </Link>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
          <tr>
            <th class="px-6 py-3">#</th>
            <th class="px-6 py-3">Élève</th>
            <th class="px-6 py-3">Matière</th>
            <th class="px-6 py-3">Trimestre</th>
            <th class="px-6 py-3">Note</th>
            <th class="px-6 py-3">Enseignant</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(note, index) in filteredNotes"
            :key="note.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-6 py-3">{{ index + 1 }}</td>
            <td class="px-6 py-3">{{ note.inscription?.eleve?.nom }} {{ note.inscription?.eleve?.prenom }}</td>
            <td class="px-6 py-3">{{ note.matiere?.nomMatiere || '—' }}</td>
            <td class="px-6 py-3">{{ note.trimestre }}</td>
            <td class="px-6 py-3">{{ note.note }}</td>
            <td class="px-6 py-3">{{ note.enseignant?.nom || '—' }}</td>
            <td class="px-6 py-3 text-center">
              <div class="flex justify-center gap-3">
                <!-- Modifier -->
                <Link
                  :href="`/admin/notes/${note.id}/edit`"
                  class="px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition"
                >
                  ✏️ Modifier
                </Link>

                <!-- Supprimer -->
                <button
                  @click="confirmDelete(note.id)"
                  class="px-2 py-1 bg-red-100 text-red-600 rounded hover:bg-red-200 transition"
                >
                  🗑️ Supprimer
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="filteredNotes.length === 0">
            <td colspan="7" class="text-center text-gray-500 py-6">
              Aucune note trouvée.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
