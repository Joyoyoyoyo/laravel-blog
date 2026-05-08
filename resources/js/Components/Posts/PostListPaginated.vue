<script setup>
import { Link } from '@inertiajs/vue3';
import PostList from './PostList.vue';

defineProps({
    posts: {
        type: Object,
        required: true,
    },
    authUser: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <div>
        <PostList :posts="posts.data" :auth-user="authUser" />

        <nav v-if="posts.links?.length" class="mt-8 flex flex-wrap gap-2">
            <component
                :is="link.url ? Link : 'span'"
                v-for="(link, i) in posts.links"
                :key="i"
                :href="link.url ?? ''"
                class="rounded border px-3 py-1 text-sm"
                :class="[
                    link.active ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 text-gray-700',
                    !link.url ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-100',
                ]"
                v-html="link.label"
            />
        </nav>
    </div>
</template>
