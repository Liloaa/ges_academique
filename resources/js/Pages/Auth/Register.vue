<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    prenom: '',
    nom: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'eleve',
    code_secret: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Inscription" />
    
    <div class="login-container">
        <!-- Colonne de gauche avec l'image de fond -->
        <div class="login-image-section">
            <div class="image-overlay">
                <h2>Rejoignez GES-ÉTUDIANT</h2>
                <p>Créez votre compte pour accéder au système de gestion</p>
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
                    <h2>Créer un compte</h2>
                    <h3>Rejoignez notre plateforme</h3>
                </div>

                <form @submit.prevent="submit" class="login-form">
                    <!-- Prénom et Nom sur la même ligne -->
                    <div class="name-fields">
                        <div class="input-group half">
                            <label for="prenom" class="input-label">Prénom(s)</label>
                            <input
                                id="prenom"
                                type="text"
                                class="text-input"
                                v-model="form.prenom"
                                required
                                autofocus
                                autocomplete="given-name"
                                placeholder="Votre prénom"
                            />
                            <InputError class="input-error" :message="form.errors.prenom" />
                        </div>

                        <div class="input-group half">
                            <label for="nom" class="input-label">Nom</label>
                            <input
                                id="nom"
                                type="text"
                                class="text-input"
                                v-model="form.nom"
                                required
                                autocomplete="family-name"
                                placeholder="Votre nom"
                            />
                            <InputError class="input-error" :message="form.errors.nom" />
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="input-group">
                        <label for="email" class="input-label">Email</label>
                        <input
                            id="email"
                            type="email"
                            class="text-input"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="votre@email.com"
                        />
                        <InputError class="input-error" :message="form.errors.email" />
                    </div>

                    <!-- Mot de passe et confirmation -->
                    <div class="password-fields">
                        <div class="input-group half">
                            <label for="password" class="input-label">Mot de passe</label>
                            <input
                                id="password"
                                type="password"
                                class="text-input"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Votre mot de passe"
                            />
                            <InputError class="input-error" :message="form.errors.password" />
                        </div>

                        <div class="input-group half">
                            <label for="password_confirmation" class="input-label">Confirmation</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                class="text-input"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Confirmez le mot de passe"
                            />
                            <InputError class="input-error" :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <!-- Rôle -->
                    <div class="input-group">
                        <label for="role" class="input-label">Rôle</label>
                        <select 
                            id="role" 
                            v-model="form.role"
                            class="text-input"
                            required
                        >
                            <option value="eleve">Élève</option>
                            <option value="enseignant">Enseignant</option>
                            <option value="admin">Administrateur</option>
                        </select>
                        <InputError class="input-error" :message="form.errors.role" />
                    </div>

                    <!-- Code secret (conditionnel) -->
                    <div v-if="form.role === 'admin' || form.role === 'enseignant'" class="input-group">
                        <label for="code_secret" class="input-label">Code secret</label>
                        <input
                            id="code_secret"
                            type="password"
                            class="text-input"
                            v-model="form.code_secret"
                            required
                            placeholder="Code secret fourni par l'administration"
                        />
                        <InputError class="input-error" :message="form.errors.code_secret" />
                    </div>

                    <button
                        type="submit"
                        class="login-button"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        S'inscrire
                    </button>
                </form>

                <div class="register-link">
                    <p>Vous avez déjà un compte? <Link :href="route('login')" class="register-link-text">Connectez-vous ici</Link></p>
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

.login-form {
    margin-bottom: 2rem;
}

/* Groupes de champs côte à côte */
.name-fields,
.password-fields {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.input-group {
    margin-bottom: 1.5rem;
}

.input-group.half {
    flex: 1;
    margin-bottom: 0;
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

/* Style spécifique pour le select */
select.text-input {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235a6c7d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
}

.input-error {
    color: #e74c3c;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.login-button {
    width: 100%;
    padding: 0.75rem;
    background-color: #7bde72;
    color: #2c3e50;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 1rem;
}

.login-button:hover:not(:disabled) {
    background-color: #8ecc89;
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
    
    .name-fields,
    .password-fields {
        flex-direction: column;
        gap: 0;
    }
}

@media (max-width: 480px) {
    .welcome-message h2 {
        font-size: 1.5rem;
    }
    
    .welcome-message h3 {
        font-size: 1.2rem;
    }
    
    .form-container {
        padding: 0 1rem;
    }
}
</style>