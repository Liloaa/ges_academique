<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil Utilisateur" />

    <AdminLayout>
        <div class="profile-container">
            <!-- En-tête de la page -->
            <div class="page-header">
                <h1 class="page-title">Profil Utilisateur</h1>
                <p class="page-subtitle">Gérez vos informations personnelles et votre compte</p>
            </div>

            <!-- Grille des formulaires -->
            <div class="profile-grid">
                <!-- Informations du profil -->
                <div class="profile-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <span class="card-icon">👤</span>
                            Informations du Profil
                        </h2>
                        <p class="card-description">
                            Mettez à jour vos informations de profil et votre adresse email.
                        </p>
                    </div>
                    <div class="card-content">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>
                </div>

                <!-- Mise à jour du mot de passe -->
                <div class="profile-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <span class="card-icon">🔒</span>
                            Mise à jour du Mot de Passe
                        </h2>
                        <p class="card-description">
                            Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester en sécurité.
                        </p>
                    </div>
                    <div class="card-content">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <!-- Suppression du compte -->
                <div class="profile-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <span class="card-icon">🗑️</span>
                            Suppression du Compte
                        </h2>
                        <p class="card-description">
                            Supprimez définitivement votre compte.
                        </p>
                    </div>
                    <div class="card-content">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
    text-align: center;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #718096;
    font-size: 1.1rem;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.profile-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.card-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 1.5rem;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-icon {
    font-size: 1.5rem;
}

.card-description {
    font-size: 0.9rem;
    opacity: 0.9;
    line-height: 1.4;
}

.card-content {
    padding: 1.5rem;
}

/* Styles pour les formulaires enfants */
:deep(.form-section) {
    margin-bottom: 1.5rem;
}

:deep(.form-label) {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

:deep(.form-input) {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    background-color: #f7fafc;
    color: #2d3748;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    font-size: 1rem;
}

:deep(.form-input:focus) {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background-color: white;
}

:deep(.form-input.error) {
    border-color: #e53e3e;
}

:deep(.form-error) {
    color: #e53e3e;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

:deep(.form-button) {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

:deep(.form-button:hover) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

:deep(.form-button:disabled) {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

:deep(.form-button.danger) {
    background: linear-gradient(135deg, #e53e3e, #c53030);
}

:deep(.form-button.danger:hover) {
    box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
}

:deep(.success-message) {
    background: #c6f6d5;
    color: #22543d;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border-left: 4px solid #38a169;
}

:deep(.warning-message) {
    background: #fed7d7;
    color: #742a2a;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border-left: 4px solid #e53e3e;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .page-subtitle {
        font-size: 1rem;
    }
    
    .card-header {
        padding: 1rem;
    }
    
    .card-content {
        padding: 1rem;
    }
}

@media (max-width: 480px) {
    .profile-container {
        padding: 0;
    }
    
    .profile-card {
        border-radius: 8px;
    }
    
    .card-title {
        font-size: 1.1rem;
    }
}
</style>