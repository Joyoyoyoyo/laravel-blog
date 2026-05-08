<script setup>
import AppHeader from '@/Components/Layout/AppHeader.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const props = defineProps({
    post: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = ref({
    title: props.post?.title ?? '',
    body: props.post?.body ?? '',
    category_id: props.post?.category_id ?? '',
});

const errors = ref({});
const processing = ref(false);
const successMessage = ref('');
const isEdit = computed(() => props.post !== null);

async function submit() {
    processing.value = true;
    errors.value = {};
    successMessage.value = '';

    try {
        const { data } = isEdit.value
            ? await axios.put(`/api/posts/${props.post.id}`, form.value)
            : await axios.post('/api/posts', form.value);
        successMessage.value = data.message ?? 'Enregistre avec succes.';
        router.visit('/home');
    } catch (e) {
        if (e.response?.status === 422 && e.response.data?.errors) {
            errors.value = e.response.data.errors;
        } else {
            errors.value = {
                general: [
                    e.response?.data?.message ??
                        'Une erreur est survenue pendant la sauvegarde.',
                ],
            };
        }
    } finally {
        processing.value = false;
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

        <main class="mx-auto max-w-3xl px-6 py-12">
            <h1 class="text-3xl font-bold">
                {{ isEdit ? 'Modifier le post' : 'Ecrire un post' }}
            </h1>

            <form class="mt-8 space-y-4" @submit.prevent="submit">
                <div v-if="errors.general?.length" class="rounded-md bg-red-50 p-3 text-sm text-red-700">
                    <p v-for="(msg, i) in errors.general" :key="i">{{ msg }}</p>
                </div>

                <div v-if="successMessage" class="rounded-md bg-green-50 p-3 text-sm text-green-700">
                    {{ successMessage }}
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                        required
                    />
                    <p v-if="errors.title?.length" class="mt-1 text-sm text-red-600">
                        {{ errors.title[0] }}
                    </p>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Categorie</label>
                    <select
                        id="category"
                        v-model="form.category_id"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                        required
                    >
                        <option disabled value="">Selectionne une categorie</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                    <p v-if="errors.category_id?.length" class="mt-1 text-sm text-red-600">
                        {{ errors.category_id[0] }}
                    </p>
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700">Contenu</label>
                    <textarea
                        id="body"
                        v-model="form.body"
                        rows="10"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                        required
                    />
                    <p v-if="errors.body?.length" class="mt-1 text-sm text-red-600">
                        {{ errors.body[0] }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="processing"
                    class="rounded-md bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                    {{ processing ? 'Sauvegarde...' : isEdit ? 'Mettre a jour' : 'Publier' }}
                </button>
            </form>
        </main>
    </div>
</template>
