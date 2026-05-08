<script setup>
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';
import PostListPaginated from '@/Components/Posts/PostListPaginated.vue';

import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    authUser: {
        type: Object,
        default: null,
    },
    posts: {
        type: Object,
        required: true,
    },
});

const isOwnProfile = computed(() => props.authUser?.id === props.user.id);
</script>

<template>
    <main class="mx-auto max-w-3xl px-6 py-12">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold">{{ user.name }}</h1>
                <p v-if="user.member_since" class="mt-1 text-xs text-gray-500">
                    Membre depuis {{ user.member_since }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a
                    v-if="isOwnProfile"
                    href="/profile"
                    class="text-sm text-indigo-600 hover:text-indigo-800"
                >
                    Modifier mon profil
                </a>
                <NotificationBell />
            </div>
        </div>

        <section class="mt-6 rounded-lg border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-800">A propos</h2>
            <p v-if="user.bio" class="mt-2 whitespace-pre-line text-sm text-gray-700">
                {{ user.bio }}
            </p>
            <p v-else class="mt-2 text-sm italic text-gray-500">
                Cet utilisateur n'a pas encore de bio.
            </p>

            <dl class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Posts</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ user.posts_count ?? 0 }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Likes recus</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ user.likes_received_count ?? 0 }}
                    </dd>
                </div>
            </dl>
        </section>

        <section class="mt-8">
            <h2 class="text-xl font-semibold">Posts de {{ user.name }}</h2>
            <div class="mt-4">
                <PostListPaginated :posts="posts" :auth-user="authUser" />
            </div>
        </section>
    </main>
</template>
