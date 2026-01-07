<template>
  <AdminLayout :user="user">
    <div class="message-show">
      <!-- En-tête avec navigation -->
      <div class="message-header">
        <div class="back-nav">
          <Link :href="isInbox ? route('admin.messages.inbox') : route('admin.messages.sent')" class="back-link">
            ← Retour
          </Link>
          <h1 class="message-title">{{ message.sujet || 'Sans objet' }}</h1>
        </div>
        
        <div class="message-actions">
          <button v-if="isInbox" @click="toggleReply" class="btn btn-primary">
            {{ showReply ? 'Annuler' : 'Répondre' }}
          </button>
          <button @click="deleteMessage" class="btn btn-danger">
            🗑️ Supprimer
          </button>
        </div>
      </div>

      <!-- Métadonnées du message -->
      <div class="message-meta">
        <div class="meta-row">
          <div class="meta-item">
            <span class="meta-label">De :</span>
            <span class="meta-value sender">{{ message.expediteur?.nom || 'Expéditeur inconnu' }}</span>
          </div>
          <div class="meta-item">
            <span class="meta-label">À :</span>
            <span class="meta-value recipient">{{ message.destinataire?.nom || 'Destinataire inconnu' }}</span>
          </div>
        </div>
        <div class="meta-row">
          <div class="meta-item">
            <span class="meta-label">Date :</span>
            <span class="meta-value">{{ formatDateTime(message.date_envoi) }}</span>
          </div>
          <div v-if="message.annee_scolaire" class="meta-item">
            <span class="meta-label">Année scolaire :</span>
            <span class="meta-value badge annee">{{ message.annee_scolaire.libelle }}</span>
          </div>
          <div class="meta-item">
            <span class="meta-label">Statut :</span>
            <span class="meta-value status-badge" :class="{ 'unread': !message.lu, 'read': message.lu }">
              {{ message.lu ? 'Lu' : 'Non lu' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Contenu du message -->
      <div class="message-content-wrapper">
        <div class="message-content">
          <div v-if="message.contenu" class="content-text" v-html="formatContent(message.contenu)"></div>
          <div v-else class="no-content">
            <p class="empty-message">Ce message ne contient pas de texte.</p>
          </div>
        </div>

        <!-- Pièces jointes (si ajoutées plus tard) -->
        <div v-if="false" class="attachments">
          <h4>Pièces jointes</h4>
          <div class="attachments-list">
            <!-- Structure pour les pièces jointes -->
          </div>
        </div>
      </div>

      <!-- Formulaire de réponse (visible seulement si showReply = true) -->
      <div v-if="showReply" class="reply-form-section">
        <div class="reply-header">
          <h3>✏️ Répondre</h3>
          <p class="reply-to">À : {{ message.expediteur?.nom }}</p>
        </div>
        
        <form @submit.prevent="sendReply" class="reply-form">
          <div class="form-group">
            <label for="replySubject" class="form-label">Sujet</label>
            <input 
              type="text" 
              id="replySubject" 
              v-model="replyForm.sujet"
              class="form-input"
              :placeholder="'Re: ' + (message.sujet || 'Sans objet')"
            >
          </div>
          
          <div class="form-group">
            <label for="replyContent" class="form-label">Message <span class="required">*</span></label>
            <textarea 
              id="replyContent" 
              v-model="replyForm.contenu"
              class="form-textarea"
              rows="8"
              placeholder="Tapez votre réponse ici..."
              required
            ></textarea>
            <div v-if="replyForm.errors.contenu" class="error-message">
              {{ replyForm.errors.contenu }}
            </div>
          </div>
          
          <div class="form-group">
            <label for="replyAnnee" class="form-label">Année scolaire (optionnel)</label>
            <select 
              id="replyAnnee" 
              v-model="replyForm.annee_scolaire_id"
              class="form-select"
            >
              <option value="">Conserver l'année d'origine</option>
              <option v-for="annee in annees" :key="annee.id" :value="annee.id">
                {{ annee.libelle }}
              </option>
            </select>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="toggleReply" class="btn btn-secondary">
              Annuler
            </button>
            <button type="submit" class="btn btn-primary" :disabled="replyForm.processing">
              <span v-if="replyForm.processing">Envoi en cours...</span>
              <span v-else>📤 Envoyer la réponse</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Section des messages liés (thread) -->
      <div v-if="relatedMessages.length > 0" class="thread-section">
        <h3 class="thread-title">📜 Conversation</h3>
        <div class="thread-messages">
          <div v-for="msg in relatedMessages" :key="msg.id" 
               class="thread-message" :class="{ 'current': msg.id === message.id }">
            <div class="thread-header">
              <span class="thread-sender">{{ msg.expediteur?.nom }}</span>
              <span class="thread-date">{{ formatDate(msg.date_envoi) }}</span>
              <span v-if="msg.id === message.id" class="thread-current">(Message actuel)</span>
            </div>
            <div class="thread-preview" @click="viewMessage(msg.id)">
              {{ truncateText(msg.contenu, 100) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  message: Object,
  user: Object,
  annees: {
    type: Array,
    default: () => []
  },
  relatedMessages: {
    type: Array,
    default: () => []
  }
})

// Variables réactives
const showReply = ref(false)

// Déterminer si le message est dans la boîte de réception ou les envoyés
const isInbox = computed(() => {
  return props.message.destinataire_id === props.user.id
})

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

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

// Formater le contenu (gestion basique du Markdown)
const formatContent = (text) => {
  if (!text) return ''
  
  // Remplacements basiques pour le formatage
  let formatted = text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/__(.*?)__/g, '<u>$1</u>')
    .replace(/\n/g, '<br>')
  
  return formatted
}

// Tronquer le texte
const truncateText = (text, length) => {
  if (!text) return ''
  if (text.length <= length) return text
  return text.substring(0, length) + '...'
}

// Formulaire de réponse
const replyForm = useForm({
  destinataire_id: props.message.expediteur_id,
  sujet: `Re: ${props.message.sujet || 'Sans objet'}`,
  contenu: '',
  annee_scolaire_id: props.message.annee_scolaire_id || ''
})

// Basculer l'affichage du formulaire de réponse
const toggleReply = () => {
  showReply.value = !showReply.value
  if (!showReply.value) {
    replyForm.reset()
    replyForm.clearErrors()
  }
}

// Envoyer la réponse
const sendReply = () => {
  replyForm.post(route('admin.messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showReply.value = false
      replyForm.reset()
      // Optionnel: recharger le message pour voir la réponse dans le thread
      router.reload({ only: ['relatedMessages'] })
    }
  })
}

// Supprimer le message
const deleteMessage = () => {
  if (confirm('Voulez-vous vraiment supprimer ce message ? Cette action est irréversible.')) {
    router.delete(route('admin.messages.destroy', props.message.id), {
      onSuccess: () => {
        // Rediriger vers la boîte de réception ou les messages envoyés
        if (isInbox.value) {
          router.visit(route('admin.messages.inbox'))
        } else {
          router.visit(route('admin.messages.sent'))
        }
      }
    })
  }
}

// Voir un message dans le thread
const viewMessage = (messageId) => {
  if (messageId !== props.message.id) {
    router.visit(route('admin.messages.show', messageId))
  }
}

// Marquer comme lu si nécessaire
onMounted(() => {
  if (isInbox.value && !props.message.lu) {
    // Vous pouvez envoyer une requête pour marquer comme lu ici
    // Mais cela se fait normalement dans le contrôleur
  }
})
</script>

<style scoped>
.message-show {
  padding: 20px;
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 25px;
  flex-wrap: wrap;
  gap: 15px;
}

.back-nav {
  flex: 1;
}

.back-link {
  display: inline-block;
  margin-bottom: 10px;
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
}

.back-link:hover {
  text-decoration: underline;
}

.message-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0;
  word-break: break-word;
}

.message-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn {
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  font-weight: 500;
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

.btn-danger {
  background: #e53e3e;
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

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Métadonnées */
.message-meta {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 25px;
  border: 1px solid #e2e8f0;
}

.meta-row {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-bottom: 10px;
}

.meta-row:last-child {
  margin-bottom: 0;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.95rem;
}

.meta-label {
  font-weight: 600;
  color: #4a5568;
  min-width: 100px;
}

.meta-value {
  color: #2d3748;
}

.sender {
  font-weight: 600;
  color: #667eea;
}

.recipient {
  font-weight: 600;
  color: #48bb78;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge.annee {
  background: #bee3f8;
  color: #2c5282;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.read {
  background: #c6f6d5;
  color: #22543d;
}

.status-badge.unread {
  background: #fed7d7;
  color: #742a2a;
}

/* Contenu du message */
.message-content-wrapper {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.message-content {
  min-height: 200px;
}

.content-text {
  line-height: 1.7;
  color: #2d3748;
  font-size: 1.05rem;
  white-space: pre-line;
}

.content-text >>> strong {
  font-weight: 700;
  color: #2d3748;
}

.content-text >>> em {
  font-style: italic;
}

.content-text >>> u {
  text-decoration: underline;
}

.no-content {
  padding: 40px;
  text-align: center;
}

.empty-message {
  color: #a0aec0;
  font-style: italic;
  margin: 0;
}

/* Pièces jointes */
.attachments {
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.attachments h4 {
  font-size: 1.1rem;
  margin-bottom: 15px;
  color: #2d3748;
}

.attachments-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

/* Formulaire de réponse */
.reply-form-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.reply-header {
  margin-bottom: 25px;
}

.reply-header h3 {
  font-size: 1.5rem;
  color: #2d3748;
  margin: 0 0 5px 0;
}

.reply-to {
  color: #718096;
  margin: 0;
}

.reply-form {
  margin-top: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #2d3748;
}

.required {
  color: #e53e3e;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e0;
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  transition: border-color 0.2s ease;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 150px;
  line-height: 1.5;
}

.error-message {
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 5px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

/* Thread de conversation */
.thread-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.thread-title {
  font-size: 1.5rem;
  color: #2d3748;
  margin: 0 0 20px 0;
}

.thread-messages {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.thread-message {
  padding: 15px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.thread-message:hover {
  background: #f7fafc;
  border-color: #cbd5e0;
}

.thread-message.current {
  background: #ebf4ff;
  border-color: #667eea;
  border-left: 4px solid #667eea;
}

.thread-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 8px;
  flex-wrap: wrap;
}

.thread-sender {
  font-weight: 600;
  color: #2d3748;
}

.thread-date {
  font-size: 0.85rem;
  color: #718096;
}

.thread-current {
  font-size: 0.85rem;
  color: #667eea;
  font-weight: 500;
}

.thread-preview {
  color: #4a5568;
  font-size: 0.95rem;
  line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
  .message-header {
    flex-direction: column;
  }
  
  .message-actions {
    width: 100%;
    justify-content: stretch;
  }
  
  .btn {
    flex: 1;
    justify-content: center;
  }
  
  .meta-row {
    flex-direction: column;
    gap: 10px;
  }
  
  .meta-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }
  
  .meta-label {
    min-width: auto;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .form-actions .btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .message-show {
    padding: 10px;
  }
  
  .message-content-wrapper,
  .reply-form-section,
  .thread-section {
    padding: 20px;
  }
  
  .message-title {
    font-size: 1.5rem;
  }
}
</style>