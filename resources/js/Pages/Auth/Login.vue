<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion" />
    
    <div class="login-container">
        <!-- Colonne de gauche avec l'image de fond -->
        <div class="login-image-section">
            <div class="image-overlay">
                <h2>Bienvenue sur GES-ÉTUDIANT</h2>
                <p>Système de Gestion des Élèves</p>
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
                    <h2>Bonjour!</h2>
                    <h3>Content de te revoir</h3>
                </div>

                <div v-if="status" class="status-message">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="login-form">
                    <div class="input-group">
                        <label for="email" class="input-label">Nom d'utilisateur ou email</label>
                        <input
                            id="email"
                            type="email"
                            class="text-input"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Entrez votre nom d'utilisateur ou email"
                        />
                        <InputError class="input-error" :message="form.errors.email" />
                    </div>

                    <div class="input-group">
                        <label for="password" class="input-label">Mot de passe</label>
                        <input
                            id="password"
                            type="password"
                            class="text-input"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="Entrez votre mot de passe"
                        />
                        <InputError class="input-error" :message="form.errors.password" />
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="remember-text">Se souvenir de moi</span>
                        </label>
                        
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="forgot-password"
                        >
                            Mot de passe oublié?
                        </Link>
                    </div>

                    <button
                        type="submit"
                        class="login-button"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Se connecter
                    </button>
                </form>

                <div class="register-link">
                    <p>Vous n'avez pas de compte? <Link :href="route('register')" class="register-link-text">Cliquez ici</Link></p>
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
    max-width: 500px;
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
    margin-bottom: 2rem;
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

.status-message {
    background-color: #E6EED6;
    color: #2c3e50;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    text-align: center;
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

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.remember-text {
    color: #5a6c7d;
    font-size: 0.9rem;
}

.forgot-password {
    color: #b4dcb1;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
}

.forgot-password:hover {
    text-decoration: underline;
}

.login-button {
    width: 100%;
    padding: 0.75rem;
    background-color: #80d379;
    color: #2c3e50;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.login-button:hover:not(:disabled) {
    background-color: #5dae58;
}

.login-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.register-link {
    text-align: center;
    margin-bottom: 2rem;
}

.register-link p {
    color: #5a6c7d;
}

.register-link-text {
    color: #b4dcb1;
    font-weight: 600;
    text-decoration: none;
}

.register-link-text:hover {
    text-decoration: underline;
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
</style>