<template>
  <EleveLayout :user="user" :unreadCount="unreadCount">
    <div class="create-message">
      <div class="page-header">
        <div class="header-left">
          <h1 class="page-title">✏️ Nouveau message</h1>
          <p class="page-subtitle">Envoyez un message à un administrateur ou un enseignant</p>
        </div>
        <div class="header-right">
          <Link :href="route('eleve.messages.inbox')" class="btn btn-secondary">
            ← Retour
          </Link>
        </div>
      </div>

      <form @submit.prevent="submit" class="message-form">
        <!-- Destinataire -->
        <div class="form-group">
          <label for="destinataire_id" class="form-label">
            Destinataire <span class="required">*</span>
          </label>
          <div class="destinataire-select">
            <select 
              id="destinataire_id" 
              v-model="form.destinataire_id"
              class="form-select"
              required
              @change="onDestinataireChange"
            >
              <option value="">Sélectionnez un destinataire</option>
              <optgroup label="Administrateurs">
                <option v-for="dest in adminDestinataires" :key="dest.id" :value="dest.id">
                  {{ dest.name }} - {{ dest.email }}
                </option>
              </optgroup>
              <optgroup label="Enseignants">
                <option v-for="dest in enseignantDestinataires" :key="dest.id" :value="dest.id">
                  {{ dest.name }} - {{ dest.email }}
                </option>
              </optgroup>
            </select>
            <div class="destinataire-info" v-if="selectedDestinataire">
              <div class="info-item">
                <strong>Rôle :</strong> {{ selectedDestinataire.role_label }}
              </div>
            </div>
          </div>
          <div v-if="form.errors.destinataire_id" class="error-message">
            {{ form.errors.destinataire_id }}
          </div>
        </div>

        <!-- Sujet -->
        <div class="form-group">
          <label for="sujet" class="form-label">Sujet (optionnel)</label>
          <input 
            type="text" 
            id="sujet" 
            v-model="form.sujet"
            class="form-input"
            placeholder="Objet du message"
            maxlength="200"
          >
          <div class="char-count">
            {{ form.sujet ? form.sujet.length : 0 }}/200 caractères
          </div>
        </div>

        <!-- Année scolaire -->
        <div class="form-group">
          <label for="annee_scolaire_id" class="form-label">Année scolaire (optionnel)</label>
          <div class="annee-select">
            <select 
              id="annee_scolaire_id" 
              v-model="form.annee_scolaire_id"
              class="form-select"
            >
              <option value="">Sélectionnez une année scolaire</option>
              <option v-for="annee in annees" :key="annee.id" :value="annee.id">
                {{ annee.libelle }}
              </option>
            </select>
            <div v-if="anneeActive" class="annee-info">
              <button type="button" @click="useActiveAnnee" class="btn-link">
                Utiliser l'année active ({{ anneeActive.libelle }})
              </button>
            </div>
          </div>
        </div>

        <!-- Contenu -->
        <div class="form-group">
          <label for="contenu" class="form-label">
            Message <span class="required">*</span>
          </label>
          <div class="editor-toolbar">
            <button type="button" @click="formatText('bold')" class="format-btn" title="Gras">
              <strong>B</strong>
            </button>
            <button type="button" @click="formatText('italic')" class="format-btn" title="Italique">
              <em>I</em>
            </button>
            <button type="button" @click="formatText('underline')" class="format-btn" title="Souligné">
              <u>U</u>
            </button>
            <button type="button" @click="insertTemplate('question')" class="format-btn">
              ❓ Question
            </button>
            <button type="button" @click="insertTemplate('urgence')" class="format-btn">
              ⚠️ Urgence
            </button>
          </div>
          <textarea 
            id="contenu" 
            v-model="form.contenu"
            class="form-textarea"
            rows="12"
            placeholder="Tapez votre message ici..."
            required
            ref="textareaRef"
          ></textarea>
          <div v-if="form.errors.contenu" class="error-message">
            {{ form.errors.contenu }}
          </div>
          <div class="textarea-info">
            <div class="char-count">
              {{ form.contenu ? form.contenu.length : 0 }} caractères
              <span v-if="form.contenu && form.contenu.length < 10" class="char-warning">
                (Le message doit contenir au moins 10 caractères)
              </span>
            </div>
            <div class="tips">
              <span class="tip">💡 Astuce : Soyez poli et précis dans votre demande</span>
            </div>
          </div>
        </div>

        <!-- Prévisualisation -->
        <div class="form-group">
          <label class="form-label">Prévisualisation</label>
          <div class="preview-box">
            <div v-if="form.contenu" class="preview-content" v-html="formatPreview(form.contenu)"></div>
            <div v-else class="preview-empty">
              Votre message s'affichera ici...
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <button type="button" @click="resetForm" class="btn btn-secondary" :disabled="form.processing">
            Annuler
          </button>
          <button type="submit" class="btn btn-primary" :disabled="form.processing || form.contenu?.length < 10">
            <span v-if="form.processing">
              <span class="spinner"></span> Envoi en cours...
            </span>
            <span v-else>📤 Envoyer le message</span>
          </button>
        </div>
      </form>
    </div>
  </EleveLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import EleveLayout from '@/Layouts/EleveLayout.vue'

const props = defineProps({
  destinataires: Array,
  annees: Array,
  anneeActive: Object,
  user: Object,
  unreadCount: Number
})

// Référence textarea
const textareaRef = ref(null)

// Séparer les destinataires par rôle
const adminDestinataires = computed(() => {
  return props.destinataires.filter(d => d.role === 'admin')
})

const enseignantDestinataires = computed(() => {
  return props.destinataires.filter(d => d.role === 'enseignant')
})

// Destinataire sélectionné
const selectedDestinataire = computed(() => {
  return props.destinataires.find(d => d.id == form.destinataire_id)
})

// Formulaire
const form = useForm({
  destinataire_id: '',
  sujet: '',
  contenu: '',
  annee_scolaire_id: ''
})

// Soumettre le formulaire
const submit = () => {
  form.post(route('eleve.messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    }
  })
}

// Réinitialiser le formulaire
const resetForm = () => {
  if (form.contenu || form.sujet || form.destinataire_id) {
    if (confirm('Voulez-vous annuler ce message ? Les modifications seront perdues.')) {
      form.reset()
      form.clearErrors()
    }
  }
}

// Quand le destinataire change
const onDestinataireChange = () => {
  // Optionnel: suggestions de sujet basées sur le destinataire
  if (!form.sujet && selectedDestinataire.value) {
    const role = selectedDestinataire.value.role
    if (role === 'enseignant') {
      form.sujet = 'Question sur le cours'
    } else if (role === 'admin') {
      form.sujet = 'Demande administrative'
    }
  }
}

// Utiliser l'année active
const useActiveAnnee = () => {
  if (props.anneeActive) {
    form.annee_scolaire_id = props.anneeActive.id
  }
}

// Formater le texte
const formatText = (type) => {
  if (!textareaRef.value) return
  
  const textarea = textareaRef.value
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = form.contenu.substring(start, end)
  
  let formattedText = ''
  switch(type) {
    case 'bold':
      formattedText = `**${selectedText}**`
      break
    case 'italic':
      formattedText = `*${selectedText}*`
      break
    case 'underline':
      formattedText = `__${selectedText}__`
      break
  }
  
  form.contenu = form.contenu.substring(0, start) + formattedText + form.contenu.substring(end)
  
  // Remettre le focus
  setTimeout(() => {
    textarea.focus()
    textarea.setSelectionRange(start + formattedText.length, start + formattedText.length)
  }, 0)
}

// Insérer un template
const insertTemplate = (type) => {
  if (!textareaRef.value) return
  
  const textarea = textareaRef.value
  const start = textarea.selectionStart
  
  let template = ''
  switch(type) {
    case 'question':
      template = '\n\nJe souhaiterais obtenir des informations concernant :\n\n• [Précisez votre demande]\n\nMerci d\'avance pour votre réponse.\n\nCordialement,\n' + props.user.name
      break
    case 'urgence':
      template = '\n\nCe message concerne une situation urgente :\n\n• [Décrivez la situation urgente]\n\n• [Indiquez les éventuelles conséquences]\n\nMerci de traiter cette demande en priorité.\n\nCordialement,\n' + props.user.name
      break
  }
  
  form.contenu = form.contenu.substring(0, start) + template + form.contenu.substring(start)
  
  // Remettre le focus
  setTimeout(() => {
    textarea.focus()
    textarea.setSelectionRange(start, start)
  }, 0)
}

// Formater la prévisualisation
const formatPreview = (text) => {
  if (!text) return ''
  
  let formatted = text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/__(.*?)__/g, '<u>$1</u>')
    .replace(/\n/g, '<br>')
  
  return formatted
}
</script>

<style scoped>
.create-message {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 15px;
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

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-link {
  background: none;
  border: none;
  color: #667eea;
  cursor: pointer;
  font-size: 0.9rem;
  padding: 0;
  text-decoration: underline;
}

.btn-link:hover {
  color: #764ba2;
}

.message-form {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.form-group {
  margin-bottom: 25px;
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

.destinataire-select,
.annee-select {
  position: relative;
}

.form-select,
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

.form-select:focus,
.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 200px;
  line-height: 1.5;
}

.destinataire-info {
  margin-top: 10px;
  padding: 10px;
  background: #f7fafc;
  border-radius: 8px;
  border-left: 4px solid #667eea;
}

.info-item {
  font-size: 0.9rem;
  color: #4a5568;
}

.annee-info {
  margin-top: 10px;
}

.char-count {
  font-size: 0.85rem;
  color: #718096;
  margin-top: 5px;
  text-align: right;
}

.char-warning {
  color: #e53e3e;
}

.error-message {
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 5px;
}

/* Barre d'outils de l'éditeur */
.editor-toolbar {
  display: flex;
  gap: 5px;
  margin-bottom: 10px;
  padding: 10px;
  background: #f7fafc;
  border-radius: 8px;
  flex-wrap: wrap;
}

.format-btn {
  padding: 8px 12px;
  background: white;
  border: 1px solid #cbd5e0;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  color: #4a5568;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 5px;
}

.format-btn:hover {
  background: #e2e8f0;
}

.textarea-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
  flex-wrap: wrap;
  gap: 10px;
}

.tips {
  font-size: 0.85rem;
  color: #718096;
}

.tip {
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Prévisualisation */
.preview-box {
  background: #f7fafc;
  border: 1px solid #cbd5e0;
  border-radius: 8px;
  padding: 15px;
  min-height: 100px;
  max-height: 200px;
  overflow-y: auto;
}

.preview-content {
  line-height: 1.6;
  color: #2d3748;
}

.preview-content >>> strong {
  font-weight: 700;
}

.preview-content >>> em {
  font-style: italic;
}

.preview-content >>> u {
  text-decoration: underline;
}

.preview-empty {
  color: #a0aec0;
  font-style: italic;
  text-align: center;
  padding: 30px;
}

/* Actions du formulaire */
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

/* Responsive */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
  
  .textarea-info {
    flex-direction: column;
    align-items: stretch;
  }
  
  .tips {
    text-align: center;
  }
}

@media (max-width: 480px) {
  .create-message {
    padding: 10px;
  }
  
  .message-form {
    padding: 20px;
  }
  
  .editor-toolbar {
    justify-content: center;
  }
  
  .format-btn {
    flex: 1;
    justify-content: center;
  }
}
</style>