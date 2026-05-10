<script setup>
import InertiaPaginationLinks from '@/Components/InertiaPaginationLinks.vue';
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

        <InertiaPaginationLinks
            :links="posts.links"
            :compact="false"
            link-size="sm"
            nav-class="mt-8 flex flex-wrap gap-2"
        />
    </div>
</template>
