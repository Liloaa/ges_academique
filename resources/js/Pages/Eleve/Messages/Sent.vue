<template>
  <EleveLayout :user="user" :unreadCount="unreadCount">
    <div class="messages-container">
      <!-- En-tête -->
      <div class="messages-header">
        <div class="header-left">
          <h1 class="page-title">📤 Messages envoyés</h1>
          <p class="page-subtitle">Messages que vous avez envoyés</p>
        </div>
        <div class="header-right">
          <div class="header-actions">
            <Link :href="route('eleve.messages.inbox')" class="btn btn-secondary">
              📨 Boîte de réception
            </Link>
            <Link :href="route('eleve.messages.create')" class="btn btn-primary">
              ✏️ Nouveau message
            </Link>
          </div>
        </div>
      </div>

      <!-- Liste des messages envoyés -->
      <div class="messages-section">
        <div v-if="messages.length === 0" class="empty-state">
          <div class="empty-icon">📤</div>
          <h3>Aucun message envoyé</h3>
          <p>Vous n'avez pas encore envoyé de message.</p>
          <Link :href="route('eleve.messages.create')" class="btn btn-primary">
            Écrire votre premier message
          </Link>
        </div>

        <div v-else class="messages-list">
          <!-- Filtres -->
          <div class="filters">
            <div class="filter-group">
              <select v-model="filterReceiver" class="filter-select">
                <option value="all">Tous les destinataires</option>
                <option v-for="receiver in uniqueReceivers" :key="receiver.id" :value="receiver.id">
                  {{ receiver.name }} ({{ receiver.role === 'admin' ? 'Admin' : 'Enseignant' }})
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

          <!-- Messages -->
          <div class="sent-messages">
            <div v-for="message in filteredMessages" :key="message.id" class="sent-message">
              <div class="message-header">
                <div class="recipient-info">
                  <div class="recipient-avatar">
                    {{ getInitials(message.destinataire?.name) }}
                  </div>
                  <div class="recipient-details">
                    <div class="recipient-name">
                      À : {{ message.destinataire?.name || 'Inconnu' }}
                    </div>
                    <div class="recipient-role">
                      {{ message.destinataire?.role === 'admin' ? 'Administrateur' : 'Enseignant' }}
                    </div>
                  </div>
                </div>
                <div class="message-meta">
                  <div class="message-date">{{ formatDateTime(message.date_envoi) }}</div>
                  <div v-if="message.lu" class="message-status read">
                    <span class="status-icon">✓</span>
                    <span class="status-text">Lu</span>
                  </div>
                  <div v-else class="message-status unread">
                    <span class="status-icon">✗</span>
                    <span class="status-text">Non lu</span>
                  </div>
                </div>
              </div>
              
              <div class="message-body">
                <h4 class="message-subject">{{ message.sujet || 'Sans objet' }}</h4>
                <p class="message-content">{{ truncateText(message.contenu, 200) }}</p>
              </div>
              
              <div class="message-footer">
                <div class="message-actions">
                  <Link :href="route('eleve.messages.show', message.id)" class="btn-action view">
                    👁️ Voir le message
                  </Link>
                  <button @click="deleteMessage(message.id)" class="btn-action delete">
                    🗑️ Supprimer
                  </button>
                </div>
                <div v-if="message.annee_scolaire" class="message-tag">
                  {{ message.annee_scolaire.libelle }}
                </div>
              </div>
            </div>
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
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import EleveLayout from '@/Layouts/EleveLayout.vue'

const props = defineProps({
  messages: Array,
  user: Object,
  unreadCount: Number
})

// Variables réactives
const filterReceiver = ref('all')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

// Formater la date et l'heure
const formatDateTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
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

// Extraire les destinataires uniques
const uniqueReceivers = computed(() => {
  const unique = []
  const seen = new Set()
  
  props.messages.forEach(message => {
    if (message.destinataire && !seen.has(message.destinataire.id)) {
      seen.add(message.destinataire.id)
      unique.push(message.destinataire)
    }
  })
  
  return unique.sort((a, b) => a.name.localeCompare(b.name))
})

// Filtrer les messages
const filteredMessages = computed(() => {
  let filtered = [...props.messages]
  
  // Filtrer par destinataire
  if (filterReceiver.value !== 'all') {
    filtered = filtered.filter(m => m.destinataire_id == filterReceiver.value)
  }
  
  // Filtrer par recherche
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(m => 
      (m.sujet && m.sujet.toLowerCase().includes(query)) ||
      (m.contenu && m.contenu.toLowerCase().includes(query)) ||
      (m.destinataire?.name && m.destinataire.name.toLowerCase().includes(query))
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

.btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

/* Messages envoyés */
.sent-messages {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.sent-message {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.sent-message:hover {
  border-color: #cbd5e0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 15px;
}

.recipient-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.recipient-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #48bb78, #38a169);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1rem;
  flex-shrink: 0;
}

.recipient-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.recipient-name {
  font-weight: 600;
  color: #2d3748;
  font-size: 1.1rem;
}

.recipient-role {
  font-size: 0.8rem;
  color: #718096;
  background: #e2e8f0;
  padding: 4px 8px;
  border-radius: 10px;
  display: inline-block;
  width: fit-content;
}

.message-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
}

.message-date {
  font-size: 0.85rem;
  color: #718096;
  white-space: nowrap;
}

.message-status {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.message-status.read {
  background: #c6f6d5;
  color: #22543d;
}

.message-status.unread {
  background: #fed7d7;
  color: #742a2a;
}

.status-icon {
  font-size: 0.9rem;
}

.message-body {
  margin-bottom: 15px;
}

.message-subject {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 10px 0;
}

.message-content {
  color: #4a5568;
  line-height: 1.6;
  margin: 0;
}

.message-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 1px solid #e2e8f0;
  flex-wrap: wrap;
  gap: 10px;
}

.message-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-action {
  padding: 8px 16px;
  border: 1px solid #cbd5e0;
  border-radius: 6px;
  background: white;
  cursor: pointer;
  font-size: 0.9rem;
  color: #4a5568;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
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

.message-tag {
  background: #bee3f8;
  color: #2c5282;
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
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
  
  .filters {
    flex-direction: column;
  }
  
  .filter-group {
    min-width: 100%;
  }
  
  .message-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .message-meta {
    align-items: flex-start;
  }
  
  .message-footer {
    flex-direction: column;
    align-items: stretch;
  }
  
  .message-actions {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .messages-container {
    padding: 10px;
  }
  
  .messages-section {
    padding: 15px;
  }
  
  .btn-action {
    width: 100%;
    justify-content: center;
  }
}
</style>