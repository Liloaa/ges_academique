<script setup>
import EleveLayout from '@/Layouts/EleveLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: EleveLayout });

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
    <Head title="Profil Eleve" />

    <div class="p-8">
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">👤 Profil Eleve</h1>
            <p class="text-gray-600">Gérez les informations de votre compte Eleve</p>
        </div>

        <div class="space-y-6 max-w-4xl">
            <!-- Informations du profil -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Informations du profil
                    </h2>
                </div>
                <div class="p-6">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>
            </div>

            <!-- Mot de passe -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Mise à jour du mot de passe
                    </h2>
                </div>
                <div class="p-6">
                    <UpdatePasswordForm />
                </div>
            </div>

            <!-- Suppression du compte -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Suppression du compte
                    </h2>
                </div>
                <div class="p-6">
                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </div>
</template>