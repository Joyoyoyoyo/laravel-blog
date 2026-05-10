<script setup>
import AppHeader from '@/Components/Layout/AppHeader.vue';
import InertiaPaginationLinks from '@/Components/InertiaPaginationLinks.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    profileUser: {
        type: Object,
        required: true,
    },
    subscribers: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

        <main class="mx-auto max-w-3xl px-6 py-12">
            <p class="text-sm text-indigo-600 hover:text-indigo-800">
                <Link :href="`/users/${profileUser.id}`">← Profil de {{ profileUser.name }}</Link>
            </p>

            <h1 class="mt-4 text-3xl font-bold">
                Abonnés de {{ profileUser.name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Les membres qui suivent ce profil.
            </p>

            <section class="mt-8">
                <p
                    v-if="subscribers.data.length === 0"
                    class="rounded-md border border-gray-200 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500"
                >
                    Aucun abonné pour le moment.
                </p>

                <ul v-else class="divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white">
                    <li v-for="subscriber in subscribers.data" :key="subscriber.id">
                        <Link
                            :href="`/users/${subscriber.id}`"
                            class="block px-4 py-3 text-sm font-medium text-gray-900 hover:bg-gray-50"
                        >
                            {{ subscriber.name }}
                        </Link>
                    </li>
                </ul>

                <InertiaPaginationLinks :links="subscribers.links" />
            </section>
        </main>
    </div>
</template>
