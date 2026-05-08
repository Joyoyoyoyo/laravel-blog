<script setup>
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';
import PostListPaginated from '@/Components/Posts/PostListPaginated.vue';

defineProps({
    user: {
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
            <h1 class="text-3xl font-bold">Home</h1>
            <div class="flex items-center gap-3">
                <a
                    href="/bookmarks"
                    class="text-sm text-indigo-600 hover:text-indigo-800"
                >
                    Mes favoris
                </a>
                <a
                    href="/profile"
                    class="text-sm text-indigo-600 hover:text-indigo-800"
                >
                    Mon profil
                </a>
                <NotificationBell />
            </div>
        </div>
        <p class="mt-4 text-gray-600">Voici tous les posts que tu as ecrits.</p>
        <p v-if="user" class="mt-2 text-sm text-gray-500">
            Connecte en tant que {{ user.name }} ({{ user.email }}).
        </p>

        <section class="mt-8">
            <div class="mb-4">
                <a
                    href="/posts/editor"
                    class="inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    Ecrire un nouveau post
                </a>
                <a
                    href="/posts"
                    class="ml-2 inline-block rounded-md border border-indigo-600 px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50"
                >
                    Voir tous les posts
                </a>
            </div>

            <PostListPaginated :posts="posts" :auth-user="user" />
        </section>
    </main>
</template>
