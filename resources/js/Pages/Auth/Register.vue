<script setup>
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const errors = ref({});
const successMessage = ref('');
const processing = ref(false);

async function submit() {
    errors.value = {};
    successMessage.value = '';
    processing.value = true;

    try {
        const { data } = await axios.post('/api/register', form.value, {
            headers: {
                Accept: 'application/json',
            },
        });
        successMessage.value = data.message ?? 'Inscription reussie.';
        form.value = {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        };
    } catch (e) {
        if (e.response?.status === 422 && e.response.data?.errors) {
            errors.value = e.response.data.errors;
        } else {
            errors.value = {
                general: [
                    e.response?.data?.message ??
                        'Une erreur est survenue. Reessaie plus tard.',
                ],
            };
        }
    } finally {
        processing.value = false;
    }
}
</script>

<template>
    <main class="mx-auto max-w-md px-6 py-12">
        <h1 class="text-2xl font-bold">Inscription</h1>
        <p class="mt-2 text-gray-600">
            Cree un compte. Tu seras enregistre avec le role utilisateur
            simple.
        </p>

        <form class="mt-8 space-y-4" @submit.prevent="submit">
            <div v-if="errors.general?.length" class="rounded-md bg-red-50 p-3 text-sm text-red-700">
                <p v-for="(msg, i) in errors.general" :key="i">{{ msg }}</p>
            </div>

            <div v-if="successMessage" class="rounded-md bg-green-50 p-3 text-sm text-green-800">
                {{ successMessage }}
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                />
                <p v-if="errors.name?.length" class="mt-1 text-sm text-red-600">
                    {{ errors.name[0] }}
                </p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                />
                <p v-if="errors.email?.length" class="mt-1 text-sm text-red-600">
                    {{ errors.email[0] }}
                </p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                />
                <p v-if="errors.password?.length" class="mt-1 text-sm text-red-600">
                    {{ errors.password[0] }}
                </p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700"
                    >Confirmation du mot de passe</label
                >
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                />
            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                :disabled="processing"
            >
                {{ processing ? 'En cours...' : "S'inscrire" }}
            </button>
        </form>
    </main>
</template>
