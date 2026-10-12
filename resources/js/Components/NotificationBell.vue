<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()
const notifications = ref([])
const nonLues = ref(0)
const ouvert = ref(false)
const bouton = ref(null)
const position = ref({ top: 0, right: 0 })
let intervalId = null

// ---------- Requêtes ----------
const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content

const xsrfCookie = () => {
  const m = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/)
  return m ? decodeURIComponent(m[1]) : null
}

const post = (url) => {
  const headers = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  if (csrfToken()) headers['X-CSRF-TOKEN'] = csrfToken()
  if (xsrfCookie()) headers['X-XSRF-TOKEN'] = xsrfCookie()
  return fetch(url, { method: 'POST', credentials: 'same-origin', headers })
}

const charger = async () => {
  try {
    const response = await fetch(route('notifications.index'), {
      credentials: 'same-origin',
      headers: { Accept: 'application/json' },
    })
    if (!response.ok) throw new Error(`HTTP ${response.status}`)
    const data = await response.json()
    notifications.value = data.notifications
    nonLues.value = data.non_lues
  } catch (error) {
    console.error('Erreur lors du chargement des notifications :', error)
  }
}

// ---------- Actions ----------
const marquerLue = async (notification) => {
  if (notification.read_at) return
  try {
    const response = await post(route('notifications.read', notification.id))
    if (!response.ok) throw new Error(`HTTP ${response.status}`)
    notification.read_at = new Date().toISOString()
    nonLues.value = Math.max(0, nonLues.value - 1)
  } catch (error) {
    console.error('Impossible de marquer la notification comme lue :', error)
  }
}

const toutMarquerLu = async () => {
  try {
    const response = await post(route('notifications.read-all'))
    if (!response.ok) throw new Error(`HTTP ${response.status}`)
    notifications.value.forEach((n) => (n.read_at = n.read_at || new Date().toISOString()))
    nonLues.value = 0
  } catch (error) {
    console.error('Impossible de tout marquer comme lu :', error)
  }
}

// Route de lecture d'un message selon le rôle de l'utilisateur connecté
const routeMessage = computed(() => {
  const role = page.props.auth?.user?.role
  return ['admin', 'enseignant', 'eleve'].includes(role) ? `${role}.messages.show` : null
})

const ouvrir = async (notification) => {
  await marquerLue(notification)
  ouvert.value = false

  const data = notification.data || {}
  if (data.message_id && routeMessage.value) {
    router.visit(route(routeMessage.value, data.message_id))
  } else if (data.lien) {
    try {
      router.visit(route(data.lien))
    } catch (e) {
      /* route inconnue : on reste sur la page */
    }
  }
}

const formaterDate = (iso) => {
  if (!iso) return ''
  return new Date(iso).toLocaleString('fr-FR', {
    day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit',
  })
}

// ---------- Ouverture / position ----------
const calculerPosition = () => {
  if (!bouton.value) return
  const rect = bouton.value.getBoundingClientRect()
  position.value = {
    top: rect.bottom + 8,
    right: Math.max(8, window.innerWidth - rect.right),
  }
}

const toggle = () => {
  ouvert.value = !ouvert.value
  if (ouvert.value) {
    calculerPosition()
    charger()
  }
}

const fermerAuClicExterieur = (e) => {
  if (!e.target.closest('.notif-bell-wrapper') && !e.target.closest('.notif-dropdown')) {
    ouvert.value = false
  }
}

onMounted(() => {
  charger()
  // Rafraîchit le compteur toutes les 60 s sans rechargement de page
  intervalId = setInterval(charger, 60000)
  document.addEventListener('click', fermerAuClicExterieur)
  window.addEventListener('resize', calculerPosition)
})

onUnmounted(() => {
  clearInterval(intervalId)
  document.removeEventListener('click', fermerAuClicExterieur)
  window.removeEventListener('resize', calculerPosition)
})
</script>

<template>
  <div class="notif-bell-wrapper">
    <button ref="bouton" class="notif-bell-btn" @click.stop="toggle" aria-label="Notifications">
      🔔
      <span v-if="nonLues > 0" class="notif-badge">{{ nonLues > 9 ? '9+' : nonLues }}</span>
    </button>

    <!-- Téléporté dans <body> : sinon le menu passe sous le contenu de la page (backdrop-filter) -->
    <Teleport to="body">
      <div
        v-if="ouvert"
        class="notif-dropdown"
        :style="{ top: position.top + 'px', right: position.right + 'px' }"
      >
        <div class="notif-header">
          <span>Notifications</span>
          <button v-if="nonLues > 0" class="notif-mark-all" @click="toutMarquerLu">
            Tout marquer lu
          </button>
        </div>

        <div v-if="notifications.length === 0" class="notif-empty">
          Aucune notification pour le moment.
        </div>

        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="notif-item"
          :class="{ 'notif-item-unread': !notification.read_at }"
          @click="ouvrir(notification)"
        >
          <span class="notif-icone">{{ notification.data?.icone }}</span>
          <div class="notif-texte">
            <div class="notif-titre">{{ notification.data?.titre }}</div>
            <div class="notif-message">{{ notification.data?.message }}</div>
            <div class="notif-date">{{ formaterDate(notification.created_at) }}</div>
          </div>
        </div>
      </div>
    </Teleport>
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
  position: fixed;
  width: min(340px, calc(100vw - 16px));
  max-height: 420px;
  overflow-y: auto;
  background: white;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  border: 1px solid #e5e7eb;
  z-index: 9999;
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

.notif-date {
  font-size: 10.5px;
  color: #9ca3af;
  margin-top: 3px;
}
</style>