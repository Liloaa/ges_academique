<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Mot de passe oublié" />
    
    <div class="login-container">
        <!-- Colonne de gauche avec l'image de fond -->
        <div class="login-image-section">
            <div class="image-overlay">
                <h2>Réinitialisation du mot de passe</h2>
                <p>Nous vous enverrons un lien pour réinitialiser votre mot de passe</p>
            </div>
        </div>

        <!-- Colonne de droite avec le formulaire -->
        <div class="login-form-section">
            <div class="form-container">
                <div class="logo-section">
                    <div class="logo">
                        <img src="/images/men-logo.jpg" alt="Logo Ministère de l'Éducation Nationale" class="logo-image" />
                    </div>
                    <div class="app-info">
                        <h1>GES-ÉTUDIANT</h1>
                        <p>Ministère de l'Éducation Nationale</p>
                    </div>
                </div>

                <div class="welcome-message">
                    <h2>Mot de passe oublié?</h2>
                    <h3>Nous sommes là pour vous aider</h3>
                </div>

                <div class="instruction-text">
                    <p>
                        Indiquez-nous simplement votre adresse email 
                        et nous vous enverrons un lien de réinitialisation de mot de passe qui vous permettra d'en choisir un nouveau.
                    </p>
                </div>

                <div
                    v-if="status"
                    class="status-message"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="login-form">
                    <div class="input-group">
                        <label for="email" class="input-label">Email</label>
                        <input
                            id="email"
                            type="email"
                            class="text-input"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Entrez votre adresse email"
                        />
                        <InputError class="input-error" :message="form.errors.email" />
                    </div>

                    <button
                        type="submit"
                        class="login-button"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Envoyer le lien de réinitialisation
                    </button>
                </form>

                <div class="back-link">
                    <p><a :href="route('login')" class="back-link-text">← Retour à la page de connexion</a></p>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Reset des styles Tailwind */
.login-container * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.login-container {
    display: flex;
    min-height: 100vh;
    background-color: #FDFDFB;
    width: 100%;
}

/* Section image de fond */
.login-image-section {
    flex: 1;
    background: linear-gradient(rgba(234, 240, 233, 0.7), rgba(235, 244, 218, 0.7)), 
                url('/images/background-login.jpg') center/cover no-repeat;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-overlay {
    background: linear-gradient(rgba(255, 255, 255, 0.7), rgba(226, 238, 195, 0.7));
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    color: #2c3e50;
    max-width: 80%;
    border: 2px solid #C7DDC5;
}

.image-overlay h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.image-overlay p {
    font-size: 1.2rem;
    color: #5a6c7d;
}

/* Section formulaire */
.login-form-section {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background-color: #FDFDFB;
}

.form-container {
    max-width: 400px;
    width: 100%;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.logo {
    width: 60px;
    height: 60px;
    background-color: #FDFDFB;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 5px;
    border: 1px solid #E6EED6;
}

.logo-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 5px;
}

.app-info h1 {
    font-size: 1.5rem;
    color: #2c3e50;
    margin-bottom: 0.25rem;
    font-weight: 700;
}

.app-info p {
    font-size: 0.9rem;
    color: #5a6c7d;
}

.welcome-message {
    margin-bottom: 1.5rem;
}

.welcome-message h2 {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.welcome-message h3 {
    font-size: 1.5rem;
    color: #5a6c7d;
    font-weight: 500;
}

.instruction-text {
    background-color: #F1F1D3;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border-left: 4px solid #C7DDC5;
}

.instruction-text p {
    color: #5a6c7d;
    line-height: 1.5;
    font-size: 0.95rem;
}

.status-message {
    background-color: #E6EED6;
    color: #2c3e50;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    text-align: center;
    border: 1px solid #C7DDC5;
}

.login-form {
    margin-bottom: 2rem;
}

.input-group {
    margin-bottom: 1.5rem;
}

.input-label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-weight: 600;
    font-size: 0.9rem;
}

.text-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #E6EED6;
    border-radius: 8px;
    background-color: #FDFDFB;
    color: #2c3e50;
    transition: border-color 0.3s ease;
    font-size: 1rem;
}

.text-input:focus {
    outline: none;
    border-color: #C7DDC5;
    box-shadow: 0 0 0 3px rgba(199, 221, 197, 0.3);
}

.input-error {
    color: #e74c3c;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.login-button {
    width: 100%;
    padding: 0.75rem;
    background-color: #C7DDC5;
    color: #2c3e50;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.login-button:hover:not(:disabled) {
    background-color: #b5d0b3;
}

.login-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.back-link {
    text-align: center;
    margin-bottom: 2rem;
}

.back-link p {
    color: #5a6c7d;
}

.back-link-text {
    color: #C7DDC5;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.back-link-text:hover {
    text-decoration: underline;
}

.download-section {
    text-align: center;
    border-top: 1px solid #E6EED6;
    padding-top: 2rem;
}

.download-section p {
    color: #5a6c7d;
    margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .login-container {
        flex-direction: column;
    }
    
    .login-image-section {
        min-height: 200px;
    }
    
    .image-overlay {
        max-width: 90%;
        padding: 1.5rem;
    }
    
    .image-overlay h2 {
        font-size: 1.5rem;
    }
    
    .image-overlay p {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .welcome-message h2 {
        font-size: 1.5rem;
    }
    
    .welcome-message h3 {
        font-size: 1.2rem;
    }
    
    .instruction-text {
        padding: 1rem;
    }
    
    .form-container {
        padding: 0 1rem;
    }
}
</style>