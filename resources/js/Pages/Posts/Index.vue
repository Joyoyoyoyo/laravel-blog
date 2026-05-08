<script setup>
import AppHeader from '@/Components/Layout/AppHeader.vue';
import PostFilters from '@/Components/Posts/PostFilters.vue';
import PostListPaginated from '@/Components/Posts/PostListPaginated.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    posts: {
        type: Object,
        required: true,
    },
    authUser: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            category_id: '',
            date_from: '',
            date_to: '',
        }),
    },
});

function applyFilters(payload) {
    const query = {};

    if (payload.category_id) {
        query.category_id = payload.category_id;
    }

    if (payload.date_from) {
        query.date_from = payload.date_from;
    }

    if (payload.date_to) {
        query.date_to = payload.date_to;
    }

    router.get('/posts', query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    router.get('/posts', {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

        <main class="mx-auto max-w-4xl px-6 py-12">
            <h1 class="text-3xl font-bold">Tous les posts</h1>
            <p class="mt-2 text-gray-600">
                Liste de tous les posts, du plus recent au plus ancien.
            </p>

            <PostFilters
                :filters="filters"
                :categories="categories"
                @apply="applyFilters"
                @reset="resetFilters"
            />

            <div class="mt-6">
                <PostListPaginated :posts="posts" :auth-user="authUser" />
            </div>
        </main>
    </div>
</template>
