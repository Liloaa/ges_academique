<template>
  <EleveLayout :user="user" :unreadCount="unreadCount">
    <div class="messages-container">
      <!-- En-tête -->
      <div class="messages-header">
        <div class="header-left">
          <h1 class="page-title">📨 Boîte de réception</h1>
          <p class="page-subtitle">Messages reçus</p>
        </div>
        <div class="header-right">
          <div class="header-actions">
            <Link :href="route('eleve.messages.create')" class="btn btn-primary">
              ✏️ Nouveau message
            </Link>
            <Link :href="route('eleve.messages.sent')" class="btn btn-secondary">
              📤 Messages envoyés
            </Link>
            <button v-if="unreadCount > 0" @click="markAllAsRead" class="btn btn-success">
              📭 Tout marquer comme lu
            </button>
          </div>
        </div>
      </div>

      <!-- Statistiques rapides -->
      <div class="quick-stats">
        <div class="stat-item">
          <div class="stat-icon">📨</div>
          <div class="stat-content">
            <div class="stat-value">{{ messages.length }}</div>
            <div class="stat-label">Messages reçus</div>
          </div>
        </div>
        <div class="stat-item unread">
          <div class="stat-icon">📬</div>
          <div class="stat-content">
            <div class="stat-value">{{ unreadCount }}</div>
            <div class="stat-label">Non lus</div>
          </div>
        </div>
      </div>

      <!-- Liste des messages -->
      <div class="messages-section">
        <div v-if="messages.length === 0" class="empty-state">
          <div class="empty-icon">📭</div>
          <h3>Votre boîte de réception est vide</h3>
          <p>Vous n'avez pas encore reçu de messages.</p>
          <Link :href="route('eleve.messages.create')" class="btn btn-primary">
            Écrire votre premier message
          </Link>
        </div>

        <div v-else class="messages-list">
          <!-- Filtres -->
          <div class="filters">
            <div class="filter-group">
              <select v-model="filterStatus" class="filter-select">
                <option value="all">Tous les messages</option>
                <option value="unread">Non lus seulement</option>
                <option value="read">Lus seulement</option>
              </select>
            </div>
            <div class="filter-group">
              <select v-model="filterSender" class="filter-select">
                <option value="all">Tous les expéditeurs</option>
                <option v-for="sender in uniqueSenders" :key="sender.id" :value="sender.id">
                  {{ sender.name }} ({{ sender.role === 'admin' ? 'Admin' : 'Enseignant' }})
                </option>
              </select>
            </div>
            <div class="filter-group">
              <div class="search-box">
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Rechercher un message..."
                  class="search-input"
                >
                <span class="search-icon">🔍</span>
              </div>
            </div>
          </div>

          <!-- Tableau des messages -->
          <div class="table-container">
            <table class="messages-table">
              <thead>
                <tr>
                  <th class="sender-col">Expéditeur</th>
                  <th class="subject-col">Sujet</th>
                  <th class="date-col">Date</th>
                  <th class="status-col">Statut</th>
                  <th class="actions-col">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="message in filteredMessages" :key="message.id" 
                    :class="{ 'unread-row': !message.lu }" class="message-row">
                  <td class="sender-cell">
                    <div class="sender-info">
                      <div class="sender-avatar">
                        {{ getInitials(message.expediteur?.name) }}
                      </div>
                      <div class="sender-details">
                        <div class="sender-name">{{ message.expediteur?.name || 'Inconnu' }}</div>
                        <div class="sender-role">
                          {{ message.expediteur?.role === 'admin' ? 'Administrateur' : 'Enseignant' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="subject-cell">
                    <div class="subject-line">
                      <span class="subject-text">{{ message.sujet || 'Sans objet' }}</span>
                      <span class="preview">{{ truncateText(message.contenu, 60) }}</span>
                    </div>
                  </td>
                  <td class="date-cell">
                    <div class="date">{{ formatDate(message.date_envoi) }}</div>
                    <div class="time">{{ formatTime(message.date_envoi) }}</div>
                  </td>
                  <td class="status-cell">
                    <span class="status-badge" :class="{ 'unread': !message.lu }">
                      {{ message.lu ? 'Lu' : 'Non lu' }}
                    </span>
                  </td>
                  <td class="actions-cell">
                    <div class="action-buttons">
                      <Link :href="route('eleve.messages.show', message.id)" class="btn-action view">
                        👁️ Voir
                      </Link>
                      <button @click="deleteMessage(message.id)" class="btn-action delete">
                        🗑️
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="filteredMessages.length > 10" class="pagination">
            <button @click="prevPage" :disabled="currentPage === 1" class="page-btn">
              ← Précédent
            </button>
            <span class="page-info">
              Page {{ currentPage }} sur {{ totalPages }}
            </span>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="page-btn">
              Suivant →
            </button>
          </div>
        </div>
      </div>
    </div>
  </EleveLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import EleveLayout from '@/Layouts/EleveLayout.vue'

const props = defineProps({
  messages: Array,
  user: Object,
  unreadCount: Number
})

// Variables réactives
const filterStatus = ref('all')
const filterSender = ref('all')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

// Formater la date
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Tronquer le texte
const truncateText = (text, length) => {
  if (!text) return ''
  if (text.length <= length) return text
  return text.substring(0, length) + '...'
}

// Obtenir les initiales
const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
}

// Extraire les expéditeurs uniques
const uniqueSenders = computed(() => {
  const unique = []
  const seen = new Set()
  
  props.messages.forEach(message => {
    if (message.expediteur && !seen.has(message.expediteur.id)) {
      seen.add(message.expediteur.id)
      unique.push(message.expediteur)
    }
  })
  
  return unique.sort((a, b) => a.name.localeCompare(b.name))
})

// Filtrer les messages
const filteredMessages = computed(() => {
  let filtered = [...props.messages]
  
  // Filtrer par statut
  if (filterStatus.value === 'unread') {
    filtered = filtered.filter(m => !m.lu)
  } else if (filterStatus.value === 'read') {
    filtered = filtered.filter(m => m.lu)
  }
  
  // Filtrer par expéditeur
  if (filterSender.value !== 'all') {
    filtered = filtered.filter(m => m.expediteur_id == filterSender.value)
  }
  
  // Filtrer par recherche
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(m => 
      (m.sujet && m.sujet.toLowerCase().includes(query)) ||
      (m.contenu && m.contenu.toLowerCase().includes(query)) ||
      (m.expediteur?.name && m.expediteur.name.toLowerCase().includes(query))
    )
  }
  
  // Pagination
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filtered.slice(start, end)
})

// Calculer le nombre total de pages
const totalPages = computed(() => {
  return Math.ceil(props.messages.length / itemsPerPage)
})

// Navigation entre pages
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

// Marquer tous comme lus
const markAllAsRead = () => {
  if (confirm('Marquer tous les messages comme lus ?')) {
    router.post(route('eleve.messages.mark-all-read'), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload()
      }
    })
  }
}

// Supprimer un message
const deleteMessage = (id) => {
  if (confirm('Voulez-vous vraiment supprimer ce message ?')) {
    router.delete(route('eleve.messages.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {
        router.reload()
      }
    })
  }
}

// Réinitialiser les filtres
onMounted(() => {
  filterStatus.value = 'all'
  filterSender.value = 'all'
  searchQuery.value = ''
  currentPage.value = 1
})
</script>

<style scoped>
.messages-container {
  padding: 20px;
}

.messages-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 20px;
}

.header-left {
  flex: 1;
}

.page-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 5px 0;
}

.page-subtitle {
  color: #718096;
  margin: 0;
}

.header-right {
  display: flex;
  align-items: center;
}

.header-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn {
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-secondary {
  background: #e2e8f0;
  color: #4a5568;
}

.btn-success {
  background: #48bb78;
  color: white;
}

.btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Statistiques rapides */
.quick-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
  margin-bottom: 30px;
}

.stat-item {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  border-left: 4px solid #667eea;
}

.stat-item.unread {
  border-left-color: #e53e3e;
}

.stat-icon {
  font-size: 2rem;
}

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  line-height: 1;
}

.stat-label {
  font-size: 0.9rem;
  color: #718096;
  margin-top: 5px;
}

/* Filtres */
.filters {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
  padding: 15px;
  background: #f8fafc;
  border-radius: 10px;
  flex-wrap: wrap;
}

.filter-group {
  flex: 1;
  min-width: 200px;
}

.filter-select {
  width: 100%;
  padding: 10px;
  border: 1px solid #cbd5e0;
  border-radius: 8px;
  background: white;
  font-size: 0.95rem;
}

.search-box {
  position: relative;
}

.search-input {
  width: 100%;
  padding: 10px 10px 10px 40px;
  border: 1px solid #cbd5e0;
  border-radius: 8px;
  font-size: 0.95rem;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #a0aec0;
}

/* Section messages */
.messages-section {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  border: 2px dashed #e2e8f0;
  border-radius: 12px;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 20px;
  opacity: 0.5;
}

.empty-state h3 {
  font-size: 1.5rem;
  color: #2d3748;
  margin-bottom: 10px;
}

.empty-state p {
  color: #718096;
  margin-bottom: 20px;
}

/* Table */
.table-container {
  overflow-x: auto;
}

.messages-table {
  width: 100%;
  border-collapse: collapse;
}

.messages-table thead {
  background: #f7fafc;
  border-bottom: 2px solid #e2e8f0;
}

.messages-table th {
  padding: 15px;
  text-align: left;
  font-weight: 600;
  color: #2d3748;
  white-space: nowrap;
}

.messages-table td {
  padding: 15px;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: middle;
}

.message-row:hover {
  background: #f8fafc;
}

.unread-row {
  background: #f0f9ff;
}

.unread-row:hover {
  background: #e6f7ff;
}

/* Cellules spécifiques */
.sender-cell {
  min-width: 200px;
}

.sender-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sender-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
  flex-shrink: 0;
}

.sender-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.sender-name {
  font-weight: 600;
  color: #2d3748;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sender-role {
  font-size: 0.75rem;
  color: #718096;
  background: #edf2f7;
  padding: 2px 8px;
  border-radius: 10px;
  display: inline-block;
  width: fit-content;
}

.subject-cell {
  min-width: 300px;
  max-width: 400px;
}

.subject-line {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.subject-text {
  font-weight: 600;
  color: #2d3748;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.preview {
  font-size: 0.85rem;
  color: #718096;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.date-cell {
  white-space: nowrap;
}

.date {
  font-weight: 500;
  color: #2d3748;
  margin-bottom: 4px;
}

.time {
  font-size: 0.85rem;
  color: #718096;
}

.status-cell {
  white-space: nowrap;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-block;
}

.status-badge.unread {
  background: #fed7d7;
  color: #742a2a;
}

.status-badge:not(.unread) {
  background: #c6f6d5;
  color: #22543d;
}

.actions-cell {
  white-space: nowrap;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-action {
  padding: 6px 12px;
  border: 1px solid #cbd5e0;
  border-radius: 6px;
  background: white;
  cursor: pointer;
  font-size: 0.85rem;
  color: #4a5568;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s ease;
}

.btn-action:hover {
  background: #e2e8f0;
}

.btn-action.delete:hover {
  background: #fed7d7;
  color: #e53e3e;
  border-color: #fed7d7;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.page-btn {
  padding: 8px 16px;
  background: #e2e8f0;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  color: #4a5568;
  transition: background-color 0.2s ease;
}

.page-btn:hover:not(:disabled) {
  background: #cbd5e0;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-weight: 500;
  color: #4a5568;
}

/* Responsive */
@media (max-width: 1024px) {
  .filters {
    flex-direction: column;
  }
  
  .filter-group {
    min-width: 100%;
  }
}

@media (max-width: 768px) {
  .messages-header {
    flex-direction: column;
  }
  
  .header-actions {
    width: 100%;
    justify-content: stretch;
  }
  
  .btn {
    flex: 1;
    justify-content: center;
  }
  
  .quick-stats {
    grid-template-columns: 1fr;
  }
  
  .messages-table {
    font-size: 0.9rem;
  }
  
  .messages-table th,
  .messages-table td {
    padding: 10px 8px;
  }
  
  .sender-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
  
  .sender-avatar {
    width: 30px;
    height: 30px;
    font-size: 0.7rem;
  }
}

@media (max-width: 480px) {
  .messages-container {
    padding: 10px;
  }
  
  .messages-section {
    padding: 15px;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 5px;
  }
  
  .btn-action {
    width: 100%;
    justify-content: center;
  }
}
</style>