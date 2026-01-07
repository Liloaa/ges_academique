<template>
  <AdminLayout :user="user">
    <div class="create-message">
      <div class="page-header">
        <div class="header-left">
          <h1 class="page-title">✏️ Nouveau message</h1>
          <p class="page-subtitle">Envoyez un message à un utilisateur</p>
        </div>
        <div class="header-right">
          <Link :href="route('admin.messages.inbox')" class="btn btn-secondary">
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
          <select 
            id="destinataire_id" 
            v-model="form.destinataire_id"
            class="form-select"
            required
          >
            <option value="">Sélectionnez un destinataire</option>
            <option v-for="user in utilisateurs" :key="user.id" :value="user.id">
              {{ user.nom }} - {{ user.email }} 
              <span v-if="user.role">({{ user.role }})</span>
            </option>
          </select>
          <div v-if="errors.destinataire_id" class="error-message">
            {{ errors.destinataire_id }}
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
        </div>

        <!-- Contenu -->
        <div class="form-group">
          <label for="contenu" class="form-label">
            Message <span class="required">*</span>
          </label>
          <textarea 
            id="contenu" 
            v-model="form.contenu"
            class="form-textarea"
            rows="10"
            placeholder="Tapez votre message ici..."
            required
          ></textarea>
          <div v-if="errors.contenu" class="error-message">
            {{ errors.contenu }}
          </div>
          <div class="textarea-tools">
            <div class="char-count">
              {{ form.contenu ? form.contenu.length : 0 }} caractères
            </div>
            <button type="button" @click="formatText('bold')" class="format-btn" title="Gras">
              <strong>B</strong>
            </button>
            <button type="button" @click="formatText('italic')" class="format-btn" title="Italique">
              <em>I</em>
            </button>
            <button type="button" @click="formatText('underline')" class="format-btn" title="Souligné">
              <u>U</u>
            </button>
          </div>
        </div>

        <!-- Prévisualisation -->
        <div class="form-group">
          <label class="form-label">Prévisualisation</label>
          <div class="preview-box">
            <div v-if="form.contenu" class="preview-content" v-html="formatPreview(form.contenu)"></div>
            <div v-else class="preview-empty">
              Le message s'affichera ici...
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <button type="button" @click="resetForm" class="btn btn-secondary" :disabled="form.processing">
            Annuler
          </button>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <span v-if="form.processing">Envoi en cours...</span>
            <span v-else>📤 Envoyer le message</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  utilisateurs: Array,
  annees: Array,
  user: Object,
  errors: Object
})

// Initialiser le formulaire
const form = useForm({
  destinataire_id: '',
  sujet: '',
  contenu: '',
  annee_scolaire_id: ''
})

// Soumettre le formulaire
const submit = () => {
  form.post(route('admin.messages.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}

// Réinitialiser le formulaire
const resetForm = () => {
  if (confirm('Voulez-vous annuler ce message ? Les modifications seront perdues.')) {
    form.reset()
    form.clearErrors()
  }
}

// Formater le texte
const formatText = (type) => {
  const textarea = document.getElementById('contenu')
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
  
  // Remettre le focus sur le textarea
  setTimeout(() => {
    textarea.focus()
    textarea.setSelectionRange(start + formattedText.length, start + formattedText.length)
  }, 0)
}

// Formater la prévisualisation
const formatPreview = (text) => {
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/__(.*?)__/g, '<u>$1</u>')
    .replace(/\n/g, '<br>')
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
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
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

.char-count {
  font-size: 0.85rem;
  color: #718096;
  margin-top: 5px;
  text-align: right;
}

.textarea-tools {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
}

.format-btn {
  background: #e2e8f0;
  border: 1px solid #cbd5e0;
  border-radius: 4px;
  padding: 4px 8px;
  cursor: pointer;
  font-size: 0.9rem;
  margin-left: 5px;
}

.format-btn:hover {
  background: #cbd5e0;
}

.preview-box {
  background: #f7fafc;
  border: 1px solid #cbd5e0;
  border-radius: 8px;
  padding: 15px;
  min-height: 100px;
  max-height: 300px;
  overflow-y: auto;
}

.preview-content {
  line-height: 1.5;
  color: #2d3748;
}

.preview-empty {
  color: #a0aec0;
  font-style: italic;
  text-align: center;
  padding: 20px;
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
}
</style>