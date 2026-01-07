<template>
  <EnseignantLayout>
    <div class="main-content">
      <!-- En-tête de l'enseignant -->
      <section class="enseignant-header" v-if="enseignant">
        <div class="enseignant-info">
          <div class="avatar">
            {{ enseignantInitiales }}
          </div>
          <div class="info">
            <h1>Bienvenue, {{ enseignant.prenom }} {{ enseignant.nom }}</h1>
            <p class="specialite">{{ enseignant.specialite || 'Enseignant' }}</p>
          </div>
        </div>
      </section>

      <!-- Statistiques de l'enseignant -->
      <section class="stats-section">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-header">
              <h3>Matieres Enseignées</h3>
              <span class="stat-icon">📚</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques?.totalMatieres || 0 }}</div>
              <p class="stat-desc">Matieres différentes</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <h3>Élèves</h3>
              <span class="stat-icon">👨‍🎓</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques?.totalEleves || 0 }}</div>
              <p class="stat-desc">Élèves au total</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <h3>Niveaux</h3>
              <span class="stat-icon">🏫</span>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ statistiques?.totalNiveaux || 0 }}</div>
              <p class="stat-desc">Niveaux différents</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Liste des classes attribuées -->
      <section class="classes-section">
        <div class="section-header">
          <h2>Mes Classes Attribuées</h2>
        </div>
        
        <div v-if="!classes || classes.length === 0" class="empty-state">
          <p>Aucune classe attribuée pour le moment.</p>
          <p>Contactez l'administration pour vous assigner des classes.</p>
        </div>

        <div v-else class="classes-grid">
          <div v-for="classe in classes" :key="classe.niveau?.id" class="class-card">
            <div class="class-header">
              <div class="class-title">
                <h3>{{ classe.niveau?.nom || 'N/A' }}</h3>
                <span class="cycle-badge">{{ classe.niveau?.cycle || 'N/A' }}</span>
              </div>
              <div class="class-info">
                <div class="info-item">
                  <span class="info-icon">🏫</span>
                  <span>Salle: {{ classe.salle?.nom || 'N/A' }}</span>
                </div>
                <div class="info-item">
                  <span class="info-icon">👥</span>
                  <span>Effectif: {{ classe.effectif || 0 }}/{{ classe.salle?.capacite || 0 }}</span>
                </div>
                <div v-if="classe.niveau?.filiere" class="info-item">
                  <span class="info-icon">📊</span>
                  <span>Filière: {{ classe.niveau.filiere }}</span>
                </div>
              </div>
            </div>

            <div class="class-body">
              <h4>Matieres enseignées :</h4>
              <div class="matieres-list">
                <div v-for="matiere in classe.matieres" :key="matiere.id" class="matiere-item">
                  <div class="matiere-name">{{ matiere.nom }}</div>
                  <div class="matiere-coeff">Coeff. {{ matiere.coefficient }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </EnseignantLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import EnseignantLayout from '@/Layouts/EnseignantLayout.vue'

const props = defineProps({
  classes: {
    type: Array,
    default: () => []
  },
  statistiques: {
    type: Object,
    default: () => ({})
  },
  prochainesEvaluations: {
    type: Array,
    default: () => []
  },
  enseignant: {
    type: Object,
    default: () => ({})
  }
})

// Calculer les initiales de l'enseignant
const enseignantInitiales = computed(() => {
  if (!props.enseignant || !props.enseignant.prenom) return 'EN'
  return (props.enseignant.prenom?.charAt(0) || '') + (props.enseignant.nom?.charAt(0) || '')
})

// Formater la date
const formatDate = (dateString, format) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  if (format === 'DD') {
    return date.getDate().toString().padStart(2, '0')
  }
  if (format === 'MMM') {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc']
    return months[date.getMonth()]
  }
  return date.toLocaleDateString('fr-FR')
}
</script>

<style scoped>
.main-content {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* En-tête enseignant */
.enseignant-header {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 12px;
  padding: 25px;
  color: white;
}

.enseignant-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.avatar {
  width: 70px;
  height: 70px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  font-weight: 600;
}

.info h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 600;
}

.specialite {
  margin: 5px 0 0;
  opacity: 0.9;
  font-size: 1rem;
}

/* Statistiques */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  border: 1px solid #e2e8f0;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.stat-header h3 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #718096;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-icon {
  font-size: 1.5rem;
}

.stat-content {
  text-align: center;
}

.stat-number {
  font-size: 2.2rem;
  font-weight: 700;
  color: #2d3748;
  margin: 10px 0;
}

.stat-desc {
  color: #718096;
  font-size: 0.9rem;
}

/* Section des classes */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h2 {
  color: #2d3748;
  font-size: 1.5rem;
  margin: 0;
}

.view-all {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.classes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 20px;
}

.class-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.class-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
}

.class-header {
  padding: 20px;
  background: linear-gradient(135deg, #f6f8ff, #edf2f7);
  border-bottom: 1px solid #e2e8f0;
}

.class-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.class-title h3 {
  margin: 0;
  color: #2d3748;
  font-size: 1.3rem;
}

.cycle-badge {
  background: #667eea;
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: uppercase;
}

.class-info {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #4a5568;
  font-size: 0.9rem;
}

.info-icon {
  font-size: 1rem;
  opacity: 0.7;
}

.class-body {
  padding: 20px;
}

.class-body h4 {
  margin: 0 0 15px;
  color: #4a5568;
  font-size: 1rem;
}

.matieres-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.matiere-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 15px;
  background: #f7fafc;
  border-radius: 8px;
  border-left: 4px solid #667eea;
}

.matiere-name {
  font-weight: 500;
  color: #2d3748;
}

.matiere-coeff {
  background: #e2e8f0;
  color: #4a5568;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: 500;
}


/* États vides */
.empty-state {
  text-align: center;
  padding: 40px;
  color: #a0aec0;
}

.empty-state p {
  margin: 10px 0;
}
</style>