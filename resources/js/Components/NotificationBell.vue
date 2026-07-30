<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const notifications = ref([])
const nonLues = ref(0)
const ouvert = ref(false)
let intervalId = null

const charger = async () => {
  try {
    const response = await fetch(route('notifications.index'))
    const data = await response.json()
    notifications.value = data.notifications
    nonLues.value = data.non_lues
  } catch (error) {
    console.error('Erreur lors du chargement des notifications :', error)
  }
}

const toggle = () => {
  ouvert.value = !ouvert.value
  if (ouvert.value) charger()
}

const marquerLue = async (notification) => {
  if (!notification.read_at) {
    await fetch(route('notifications.read', notification.id), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
    })
    notification.read_at = new Date().toISOString()
    nonLues.value = Math.max(0, nonLues.value - 1)
  }
}

const toutMarquerLu = async () => {
  await fetch(route('notifications.read-all'), {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
  })
  notifications.value.forEach(n => (n.read_at = new Date().toISOString()))
  nonLues.value = 0
}

const fermerAuClicExterieur = (e) => {
  if (!e.target.closest('.notif-bell-wrapper')) {
    ouvert.value = false
  }
}

onMounted(() => {
  charger()
  // Rafraîchit le compteur toutes les 60s sans navigation ni rechargement de page
  intervalId = setInterval(charger, 60000)
  document.addEventListener('click', fermerAuClicExterieur)
})

onUnmounted(() => {
  clearInterval(intervalId)
  document.removeEventListener('click', fermerAuClicExterieur)
})
</script>

<template>
  <div class="notif-bell-wrapper">
    <button class="notif-bell-btn" @click.stop="toggle" aria-label="Notifications">
      🔔
      <span v-if="nonLues > 0" class="notif-badge">{{ nonLues > 9 ? '9+' : nonLues }}</span>
    </button>

    <div v-if="ouvert" class="notif-dropdown">
      <div class="notif-header">
        <span>Notifications</span>
        <button v-if="nonLues > 0" class="notif-mark-all" @click="toutMarquerLu">Tout marquer lu</button>
      </div>

      <div v-if="notifications.length === 0" class="notif-empty">
        Aucune notification pour le moment.
      </div>

      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="notif-item"
        :class="{ 'notif-item-unread': !notification.read_at }"
        @click="marquerLue(notification)"
      >
        <span class="notif-icone">{{ notification.data.icone }}</span>
        <div class="notif-texte">
          <div class="notif-titre">{{ notification.data.titre }}</div>
          <div class="notif-message">{{ notification.data.message }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.notif-bell-wrapper {
  position: relative;
}

.notif-bell-btn {
  position: relative;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: background 0.15s;
}

.notif-bell-btn:hover {
  background: rgba(0, 0, 0, 0.05);
}

.notif-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  background: #dc2626;
  color: white;
  font-size: 10px;
  font-weight: bold;
  border-radius: 999px;
  min-width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 3px;
}

.notif-dropdown {
  position: absolute;
  right: 0;
  top: calc(100% + 8px);
  width: 340px;
  max-height: 420px;
  overflow-y: auto;
  background: white;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  border: 1px solid #e5e7eb;
  z-index: 50;
}

.notif-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 14px;
  border-bottom: 1px solid #e5e7eb;
  font-weight: bold;
  font-size: 13px;
  color: #1f2937;
}

.notif-mark-all {
  background: none;
  border: none;
  color: #2563eb;
  font-size: 11px;
  font-weight: normal;
  cursor: pointer;
}

.notif-empty {
  padding: 24px 14px;
  text-align: center;
  color: #9ca3af;
  font-size: 13px;
}

.notif-item {
  display: flex;
  gap: 10px;
  padding: 10px 14px;
  border-bottom: 1px solid #f3f4f6;
  cursor: pointer;
  transition: background 0.15s;
}

.notif-item:hover {
  background: #f9fafb;
}

.notif-item-unread {
  background: #eff6ff;
}

.notif-icone {
  font-size: 18px;
}

.notif-titre {
  font-weight: 600;
  font-size: 12.5px;
  color: #1f2937;
}

.notif-message {
  font-size: 12px;
  color: #6b7280;
  margin-top: 2px;
}
</style>
