<template>
  <EnseignantLayout :user="user" :unreadCount="unreadCount">
    <div class="message-detail">
      <!-- Navigation et en-tête -->
      <div class="message-header">
        <div class="breadcrumb">
          <Link :href="isInbox ? route('enseignant.messages.inbox') : route('enseignant.messages.sent')" class="back-link">
            ← Retour à {{ isInbox ? 'la boîte de réception' : 'les messages envoyés' }}
          </Link>
        </div>
        
        <div class="header-main">
          <h1 class="message-title">{{ message.sujet || 'Sans objet' }}</h1>
          <div class="header-actions">
            <button v-if="isInbox && message.expediteur" @click="toggleReply" class="btn btn-primary">
              {{ showReply ? 'Annuler' : 'Répondre' }}
            </button>
            <button @click="deleteMessage" class="btn btn-danger">
              🗑️ Supprimer
            </button>
          </div>
        </div>
      </div>

      <!-- Informations du message -->
      <div class="message-info">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Expéditeur :</span>
            <span class="info-value sender">
              <div class="user-badge">
                <span class="user-avatar small">
                  {{ getInitials(message.expediteur?.name) }}
                </span>
                <div class="user-details">
                  <span class="user-name">{{ message.expediteur?.name || 'Inconnu' }}</span>
                  <span class="user-role">{{ getRoleLabel(message.expediteur?.role) }}</span>
                </div>
              </div>
            </span>
          </div>
          
          <div class="info-item">
            <span class="info-label">Destinataire :</span>
            <span class="info-value recipient">
              <div class="user-badge">
                <span class="user-avatar small">
                  {{ getInitials(message.destinataire?.name) }}
                </span>
                <div class="user-details">
                  <span class="user-name">{{ message.destinataire?.name || 'Inconnu' }}</span>
                  <span class="user-role">{{ getRoleLabel(message.destinataire?.role) }}</span>
                </div>
              </div>
            </span>
          </div>
          
          <div class="info-item">
            <span class="info-label">Date :</span>
            <span class="info-value">{{ formatDateTime(message.date_envoi) }}</span>
          </div>
          
          <div v-if="message.annee_scolaire" class="info-item">
            <span class="info-label">Année scolaire :</span>
            <span class="info-value annee-badge">
              {{ message.annee_scolaire.libelle }}
            </span>
          </div>
          
          <div class="info-item">
            <span class="info-label">Statut :</span>
            <span class="info-value status-badge" :class="{ 'unread': !message.lu, 'read': message.lu }">
              {{ message.lu ? 'Lu' : 'Non lu' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Contenu du message -->
      <div class="message-content-card">
        <div class="content-header">
          <h3 class="content-title">Message</h3>
          <span class="content-time">{{ formatTime(message.date_envoi) }}</span>
        </div>
        
        <div class="message-body">
          <div v-if="message.contenu" class="message-text" v-html="formatContent(message.contenu)"></div>
          <div v-else class="empty-content">
            <p class="empty-text">Ce message ne contient pas de texte.</p>
          </div>
        </div>
        
        <!-- Option pour répondre en bas du message -->
        <div v-if="isInbox && message.expediteur" class="quick-reply">
          <button @click="toggleReply" class="btn-reply">
            <span class="reply-icon">↩️</span>
            <span class="reply-text">Répondre</span>
          </button>
        </div>
      </div>

      <!-- Formulaire de réponse (visible quand showReply est true) -->
      <div v-if="showReply" class="reply-section">
        <div class="reply-header">
          <h3 class="reply-title">
            <span class="reply-icon">✏️</span>
            Répondre à {{ message.expediteur?.name }}
          </h3>
          <p class="reply-subtitle">Votre réponse sera envoyée à {{ message.expediteur?.name }}</p>
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
            <div class="char-count">
              {{ replyForm.sujet ? replyForm.sujet.length : 0 }}/200 caractères
            </div>
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
            <div class="textarea-tools">
              <button type="button" @click="insertSignature" class="btn-tool">
                👤 Ajouter ma signature
              </button>
            </div>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="toggleReply" class="btn btn-secondary">
              Annuler
            </button>
            <button type="submit" class="btn btn-primary" :disabled="replyForm.processing">
              <span v-if="replyForm.processing">
                <span class="spinner"></span> Envoi en cours...
              </span>
              <span v-else>📤 Envoyer la réponse</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Fil de conversation (messages liés) -->
      <div v-if="relatedMessages.length > 0" class="conversation-section">
        <h3 class="conversation-title">
          <span class="conversation-icon">💬</span>
          Conversation
        </h3>
        
        <div class="conversation-timeline">
          <div v-for="msg in relatedMessages" :key="msg.id" 
               class="conversation-item" :class="{ 'current': msg.id === message.id }">
            <div class="timeline-marker" :class="{ 'current': msg.id === message.id }"></div>
            
            <div class="conversation-content" @click="viewMessage(msg.id)">
              <div class="conversation-header">
                <div class="sender-info">
                  <span class="sender-avatar mini">
                    {{ getInitials(msg.expediteur?.name) }}
                  </span>
                  <span class="sender-name">{{ msg.expediteur?.name || 'Inconnu' }}</span>
                  <span class="sender-badge" :class="getRoleClass(msg.expediteur?.role)">
                    {{ getRoleLabel(msg.expediteur?.role) }}
                  </span>
                </div>
                <div class="conversation-date">
                  {{ formatDate(msg.date_envoi) }} à {{ formatTime(msg.date_envoi) }}
                  <span v-if="msg.id === message.id" class="current-indicator">(Message actuel)</span>
                </div>
              </div>
              
              <div class="conversation-body">
                <h5 class="conversation-subject">{{ msg.sujet || 'Sans objet' }}</h5>
                <p class="conversation-preview">{{ truncateText(msg.contenu, 120) }}</p>
              </div>
              
              <div class="conversation-footer">
                <span class="conversation-status">
                  <span v-if="msg.lu" class="status-read">✓ Lu</span>
                  <span v-else class="status-unread">✗ Non lu</span>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Messages d'information -->
      <div v-if="flash.success" class="alert alert-success">
        {{ flash.success }}
      </div>
      
      <div v-if="flash.error" class="alert alert-error">
        {{ flash.error }}
      </div>
    </div>
  </EnseignantLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'
import EnseignantLayout from '@/Layouts/EnseignantLayout.vue'

const props = defineProps({
  message: Object,
  user: Object,
  unreadCount: Number,
  relatedMessages: {
    type: Array,
    default: () => []
  }
})

// Récupérer les messages flash
const flash = computed(() => usePage().props.flash || {})

// Variables réactives
const showReply = ref(false)

// Déterminer si le message est dans la boîte de réception
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

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Formater le contenu (gestion basique du Markdown)
const formatContent = (text) => {
  if (!text) return ''
  
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

// Obtenir les initiales
const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
}

// Obtenir le label du rôle
const getRoleLabel = (role) => {
  switch(role) {
    case 'admin': return 'Administrateur'
    case 'enseignant': return 'Enseignant'
    case 'eleve': return 'Élève'
    default: return 'Utilisateur'
  }
}

// Obtenir la classe CSS pour le rôle
const getRoleClass = (role) => {
  switch(role) {
    case 'admin': return 'role-admin'
    case 'enseignant': return 'role-enseignant'
    case 'eleve': return 'role-eleve'
    default: return ''
  }
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

// Insérer la signature
const insertSignature = () => {
  const signature = `\n\nCordialement,\n${props.user.name}`
  replyForm.contenu += signature
}

// Envoyer la réponse
const sendReply = () => {
  replyForm.post(route('enseignant.messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showReply.value = false
      replyForm.reset()
      // Recharger la page pour voir la réponse dans le fil de conversation
      router.reload({ only: ['relatedMessages'] })
    }
  })
}

// Supprimer le message
const deleteMessage = () => {
  if (confirm('Voulez-vous vraiment supprimer ce message ? Cette action est irréversible.')) {
    router.delete(route('enseignant.messages.destroy', props.message.id), {
      onSuccess: () => {
        // Rediriger vers la boîte de réception ou les messages envoyés
        if (isInbox.value) {
          router.visit(route('enseignant.messages.inbox'))
        } else {
          router.visit(route('enseignant.messages.sent'))
        }
      }
    })
  }
}

// Voir un message dans le fil de conversation
const viewMessage = (messageId) => {
  if (messageId !== props.message.id) {
    router.visit(route('enseignant.messages.show', messageId))
  }
}
</script>

<style scoped>
.message-detail {
  padding: 20px;
}

/* En-tête */
.message-header {
  margin-bottom: 25px;
}

.breadcrumb {
  margin-bottom: 15px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
}

.back-link:hover {
  text-decoration: underline;
}

.header-main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 15px;
}

.message-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0;
  flex: 1;
  word-break: break-word;
}

.header-actions {
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

/* Informations du message */
.message-info {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 25px;
  border: 1px solid #e2e8f0;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 15px;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.info-label {
  font-weight: 600;
  color: #4a5568;
  min-width: 120px;
  font-size: 0.9rem;
}

.info-value {
  color: #2d3748;
  flex: 1;
}

/* Badge utilisateur */
.user-badge {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: white;
  flex-shrink: 0;
}

.user-avatar.small {
  width: 36px;
  height: 36px;
  font-size: 0.9rem;
}

.user-avatar.mini {
  width: 28px;
  height: 28px;
  font-size: 0.75rem;
}

.sender .user-avatar {
  background: linear-gradient(135deg, #667eea, #764ba2);
}

.recipient .user-avatar {
  background: linear-gradient(135deg, #48bb78, #38a169);
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.user-name {
  font-weight: 600;
  color: #2d3748;
}

.user-role {
  font-size: 0.75rem;
  color: #718096;
  background: #edf2f7;
  padding: 2px 8px;
  border-radius: 10px;
  display: inline-block;
  width: fit-content;
}

/* Badges divers */
.annee-badge {
  background: #bee3f8;
  color: #2c5282;
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
  display: inline-block;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
  display: inline-block;
}

.status-badge.read {
  background: #c6f6d5;
  color: #22543d;
}

.status-badge.unread {
  background: #fed7d7;
  color: #742a2a;
}

/* Carte de contenu */
.message-content-card {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  position: relative;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid #e2e8f0;
}

.content-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0;
}

.content-time {
  font-size: 0.9rem;
  color: #718096;
}

.message-body {
  min-height: 100px;
}

.message-text {
  line-height: 1.7;
  color: #2d3748;
  font-size: 1.05rem;
  white-space: pre-line;
}

.message-text >>> strong {
  font-weight: 700;
  color: #2d3748;
}

.message-text >>> em {
  font-style: italic;
}

.message-text >>> u {
  text-decoration: underline;
}

.empty-content {
  padding: 40px;
  text-align: center;
}

.empty-text {
  color: #a0aec0;
  font-style: italic;
  margin: 0;
}

/* Réponse rapide */
.quick-reply {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
  text-align: right;
}

.btn-reply {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-reply:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.reply-icon {
  font-size: 1.2rem;
}

/* Section réponse */
.reply-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.reply-header {
  margin-bottom: 25px;
}

.reply-title {
  font-size: 1.5rem;
  color: #2d3748;
  margin: 0 0 8px 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.reply-subtitle {
  color: #718096;
  margin: 0;
  font-size: 0.95rem;
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

.char-count {
  font-size: 0.85rem;
  color: #718096;
  margin-top: 5px;
  text-align: right;
}

.error-message {
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 5px;
}

.textarea-tools {
  margin-top: 10px;
}

.btn-tool {
  background: none;
  border: 1px solid #cbd5e0;
  border-radius: 6px;
  padding: 6px 12px;
  cursor: pointer;
  font-size: 0.85rem;
  color: #4a5568;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s ease;
}

.btn-tool:hover {
  background: #e2e8f0;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.spinner {
  display: inline-block;
  width: 12px;
  height: 12px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s ease-in-out infinite;
  margin-right: 8px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Fil de conversation */
.conversation-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.conversation-title {
  font-size: 1.5rem;
  color: #2d3748;
  margin: 0 0 20px 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.conversation-timeline {
  position: relative;
  padding-left: 30px;
}

.conversation-timeline::before {
  content: '';
  position: absolute;
  left: 10px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e2e8f0;
}

.conversation-item {
  position: relative;
  margin-bottom: 20px;
}

.conversation-item.current {
  margin-bottom: 30px;
}

.timeline-marker {
  position: absolute;
  left: -30px;
  top: 10px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #cbd5e0;
  border: 3px solid white;
}

.timeline-marker.current {
  background: #667eea;
  width: 24px;
  height: 24px;
  left: -32px;
}

.conversation-content {
  background: #f8fafc;
  border-radius: 10px;
  padding: 20px;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: all 0.3s ease;
}

.conversation-item.current .conversation-content {
  background: white;
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.conversation-content:hover {
  background: white;
  border-color: #cbd5e0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  flex-wrap: wrap;
  gap: 10px;
}

.sender-info {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.sender-name {
  font-weight: 600;
  color: #2d3748;
}

.sender-badge {
  font-size: 0.75rem;
  padding: 2px 8px;
  border-radius: 10px;
  font-weight: 600;
}

.role-admin {
  background: #fed7d7;
  color: #742a2a;
}

.role-enseignant {
  background: #bee3f8;
  color: #2c5282;
}

.role-enseignant {
  background: #c6f6d5;
  color: #22543d;
}

.conversation-date {
  font-size: 0.85rem;
  color: #718096;
}

.current-indicator {
  color: #667eea;
  font-weight: 500;
  margin-left: 5px;
}

.conversation-body {
  margin-bottom: 10px;
}

.conversation-subject {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 8px 0;
}

.conversation-preview {
  color: #4a5568;
  font-size: 0.95rem;
  line-height: 1.5;
  margin: 0;
}

.conversation-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 10px;
  border-top: 1px solid #e2e8f0;
}

.conversation-status {
  font-size: 0.85rem;
}

.status-read {
  color: #48bb78;
  display: flex;
  align-items: center;
  gap: 5px;
}

.status-unread {
  color: #e53e3e;
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Alertes */
.alert {
  padding: 15px 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-weight: 500;
}

.alert-success {
  background: #c6f6d5;
  color: #22543d;
  border: 1px solid #9ae6b4;
}

.alert-error {
  background: #fed7d7;
  color: #742a2a;
  border: 1px solid #feb2b2;
}

/* Responsive */
@media (max-width: 1024px) {
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .info-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
  
  .info-label {
    min-width: auto;
  }
}

@media (max-width: 768px) {
  .message-detail {
    padding: 15px;
  }
  
  .header-main {
    flex-direction: column;
    align-items: stretch;
  }
  
  .header-actions {
    width: 100%;
    justify-content: stretch;
  }
  
  .btn {
    flex: 1;
    justify-content: center;
  }
  
  .message-content-card,
  .reply-section,
  .conversation-section {
    padding: 20px;
  }
  
  .conversation-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .conversation-date {
    align-self: flex-start;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .form-actions .btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .message-detail {
    padding: 10px;
  }
  
  .message-title {
    font-size: 1.5rem;
  }
  
  .conversation-timeline {
    padding-left: 20px;
  }
  
  .timeline-marker {
    left: -20px;
  }
  
  .timeline-marker.current {
    left: -22px;
  }
  
  .conversation-content {
    padding: 15px;
  }
  
  .info-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
  
  .info-label {
    min-width: auto;
  }
}
</style>