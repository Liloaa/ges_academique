<template>
  <AdminLayout :user="user">
    <div class="messages-container">
      <div class="messages-header">
        <h1 class="page-title">📤 Messages envoyés</h1>
        <div class="header-actions">
          <Link :href="route('admin.messages.inbox')" class="btn btn-secondary">
            <span>📨 Boîte de réception</span>
          </Link>
          <Link :href="route('admin.messages.create')" class="btn btn-primary">
            <span>✏️ Nouveau message</span>
          </Link>
        </div>
      </div>

      <!-- Liste des messages envoyés -->
      <div class="messages-list">
        <div v-if="messages.length === 0" class="empty-state">
          <div class="empty-icon">📤</div>
          <h3>Aucun message envoyé</h3>
          <p>Vous n'avez pas encore envoyé de message.</p>
          <Link :href="route('admin.messages.create')" class="btn btn-primary">
            Écrire votre premier message
          </Link>
        </div>

        <div v-else class="messages-grid">
          <div v-for="message in messages" :key="message.id" class="message-card">
            <div class="message-content">
              <div class="message-header">
                <div class="recipient-info">
                  <div class="recipient-avatar">
                    <span>{{ getInitials(message.destinataire?.nom) }}</span>
                  </div>
                  <div>
                    <h4 class="recipient-name">À : {{ message.destinataire?.nom }}</h4>
                    <span class="message-date">{{ formatDate(message.date_envoi) }}</span>
                  </div>
                </div>
                <div class="message-status">
                  <span v-if="message.lu" class="badge read-badge">Lu</span>
                  <span v-else class="badge unread-badge">Non lu</span>
                  <span v-if="message.annee_scolaire" class="badge annee-badge">
                    {{ message.annee_scolaire.libelle }}
                  </span>
                </div>
              </div>
              
              <div class="message-body">
                <h5 class="message-subject">{{ message.sujet || 'Sans objet' }}</h5>
                <p class="message-preview">{{ truncateText(message.contenu, 150) }}</p>
              </div>
              
              <div class="message-footer">
                <div class="message-actions">
                  <Link :href="route('admin.messages.show', message.id)" class="btn-action">
                    👁️ Voir
                  </Link>
                  <button @click="deleteMessage(message.id)" class="btn-action delete">
                    🗑️ Supprimer
                  </button>
                </div>
                <span class="message-time">{{ formatTime(message.date_envoi) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  messages: Array,
  user: Object
})

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
  if (text.length <= length) return text
  return text.substring(0, length) + '...'
}

// Obtenir les initiales
const getInitials = (nom) => {
  if (!nom) return '?'
  return nom.split(' ').map(n => n[0]).join('').toUpperCase()
}

// Supprimer un message
const deleteMessage = (id) => {
  if (confirm('Voulez-vous vraiment supprimer ce message ?')) {
    router.delete(route('admin.messages.destroy', id))
  }
}
</script>

<style scoped>
/* Styles similaires à Inbox.vue, avec quelques adaptations */
.messages-container {
  padding: 20px;
}

.messages-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 15px;
}

.page-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.btn {
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  border: none;
  cursor: pointer;
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

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 20px;
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

.messages-grid {
  display: grid;
  gap: 15px;
}

.message-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.message-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
}

.recipient-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.recipient-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #48bb78, #38a169);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
}

.recipient-name {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 4px 0;
}

.message-date {
  font-size: 0.85rem;
  color: #718096;
}

.message-status {
  display: flex;
  gap: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.read-badge {
  background: #c6f6d5;
  color: #22543d;
}

.unread-badge {
  background: #fed7d7;
  color: #742a2a;
}

.annee-badge {
  background: #bee3f8;
  color: #2c5282;
}

.message-body {
  margin-bottom: 15px;
}

.message-subject {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 8px 0;
}

.message-preview {
  color: #4a5568;
  font-size: 0.95rem;
  line-height: 1.5;
  margin: 0;
}

.message-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 1px solid #e2e8f0;
}

.message-actions {
  display: flex;
  gap: 10px;
}

.btn-action {
  padding: 6px 12px;
  background: #e2e8f0;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.85rem;
  color: #4a5568;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.btn-action:hover {
  background: #cbd5e0;
}

.btn-action.delete {
  background: #fed7d7;
  color: #742a2a;
}

.btn-action.delete:hover {
  background: #feb2b2;
}

.message-time {
  font-size: 0.85rem;
  color: #718096;
}

@media (max-width: 768px) {
  .messages-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .header-actions {
    flex-direction: column;
  }
  
  .message-footer {
    flex-direction: column;
    gap: 10px;
    align-items: stretch;
  }
  
  .message-actions {
    justify-content: center;
  }
}
</style>