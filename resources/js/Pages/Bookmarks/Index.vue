<script setup>
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';
import PostListPaginated from '@/Components/Posts/PostListPaginated.vue';

defineProps({
    authUser: {
        type: Object,
        default: null,
    },
    posts: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <main class="mx-auto max-w-3xl px-6 py-12">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold">Mes favoris</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Les posts que tu as mis de cote pour les relire plus tard.
                </p>
            </div>
            <NotificationBell />
        </div>

        <section class="mt-8">
            <p
                v-if="posts.data.length === 0"
                class="rounded-md border border-gray-200 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500"
            >
                Tu n'as pas encore de favoris. Clique sur l'icone de marque-page sur un post pour l'ajouter ici.
            </p>

            <PostListPaginated v-else :posts="posts" :auth-user="authUser" />
        </section>
    </main>
</template>
