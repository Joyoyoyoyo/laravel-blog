<script setup>
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    profile: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const flashStatus = computed(() => page.props.flash?.status ?? null);

const profileForm = useForm({
    name: props.profile.name ?? '',
    email: props.profile.email ?? '',
    bio: props.profile.bio ?? '',
});

function submitProfile() {
    profileForm.put('/api/profile', {
        preserveScroll: true,
    });
}

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function submitPassword() {
    passwordForm.put('/api/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
}

const deleteForm = useForm({
    password: '',
});

function submitDelete() {
    if (!window.confirm('Cette action est definitive. Confirmer la suppression de ton compte ?')) {
        return;
    }

    deleteForm.delete('/api/profile', {
        preserveScroll: true,
        onSuccess: () => deleteForm.reset(),
    });
}

function logout() {
    router.post('/api/logout');
}
</script>

<template>
    <main class="mx-auto max-w-2xl px-6 py-12">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold">Mon profil</h1>
                <p class="mt-1 text-sm text-gray-500">Modifie tes informations, ton mot de passe ou supprime ton compte.</p>
            </div>
            <NotificationBell />
        </div>

        <section class="mt-8 rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900">Informations</h2>
            <p class="mt-1 text-sm text-gray-500">Mets a jour ton nom, ton email et ta bio publique.</p>

            <p
                v-if="flashStatus === 'profile-updated'"
                class="mt-4 rounded-md bg-green-50 px-3 py-2 text-sm text-green-700"
            >
                Profil mis a jour.
            </p>

            <form class="mt-6 space-y-4" @submit.prevent="submitProfile">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input
                        id="name"
                        v-model="profileForm.name"
                        type="text"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    />
                    <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">
                        {{ profileForm.errors.name }}
                    </p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        id="email"
                        v-model="profileForm.email"
                        type="email"
                        autocomplete="email"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    />
                    <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">
                        {{ profileForm.errors.email }}
                    </p>
                </div>

                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                    <textarea
                        id="bio"
                        v-model="profileForm.bio"
                        rows="4"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    />
                    <p v-if="profileForm.errors.bio" class="mt-1 text-sm text-red-600">
                        {{ profileForm.errors.bio }}
                    </p>
                </div>

                <button
                    type="submit"
                    class="rounded-md bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    :disabled="profileForm.processing"
                >
                    {{ profileForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                </button>
            </form>
        </section>

        <section class="mt-8 rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900">Mot de passe</h2>
            <p class="mt-1 text-sm text-gray-500">Choisis un mot de passe d'au moins 8 caracteres.</p>

            <p
                v-if="flashStatus === 'password-updated'"
                class="mt-4 rounded-md bg-green-50 px-3 py-2 text-sm text-green-700"
            >
                Mot de passe mis a jour.
            </p>

            <form class="mt-6 space-y-4" @submit.prevent="submitPassword">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                    <input
                        id="current_password"
                        v-model="passwordForm.current_password"
                        type="password"
                        autocomplete="current-password"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    />
                    <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                        {{ passwordForm.errors.current_password }}
                    </p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                    <input
                        id="password"
                        v-model="passwordForm.password"
                        type="password"
                        autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    />
                    <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                        {{ passwordForm.errors.password }}
                    </p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                    <input
                        id="password_confirmation"
                        v-model="passwordForm.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    />
                </div>

                <button
                    type="submit"
                    class="rounded-md bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    :disabled="passwordForm.processing"
                >
                    {{ passwordForm.processing ? 'Enregistrement...' : 'Mettre a jour le mot de passe' }}
                </button>
            </form>
        </section>

        <section class="mt-8 rounded-lg border border-red-200 bg-red-50 p-6">
            <h2 class="text-lg font-semibold text-red-800">Zone dangereuse</h2>
            <p class="mt-1 text-sm text-red-700">
                La suppression de ton compte est definitive. Tes posts, commentaires et likes seront perdus.
            </p>

            <form class="mt-6 space-y-4" @submit.prevent="submitDelete">
                <div>
                    <label for="delete_password" class="block text-sm font-medium text-gray-700">Confirme avec ton mot de passe</label>
                    <input
                        id="delete_password"
                        v-model="deleteForm.password"
                        type="password"
                        autocomplete="current-password"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                        required
                    />
                    <p v-if="deleteForm.errors.password" class="mt-1 text-sm text-red-600">
                        {{ deleteForm.errors.password }}
                    </p>
                </div>

                <button
                    type="submit"
                    class="rounded-md bg-red-600 px-4 py-2 font-medium text-white hover:bg-red-700 disabled:opacity-50"
                    :disabled="deleteForm.processing"
                >
                    {{ deleteForm.processing ? 'Suppression...' : 'Supprimer mon compte' }}
                </button>
            </form>
        </section>

        <div class="mt-8 flex justify-end">
            <button
                type="button"
                class="text-sm text-gray-600 underline hover:text-gray-900"
                @click="logout"
            >
                Se deconnecter
            </button>
        </div>
    </main>
</template>
