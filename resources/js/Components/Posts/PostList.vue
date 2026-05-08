<script setup>
import Post from './Post.vue';
import axios from 'axios';
import { reactive, watch } from 'vue';

const props = defineProps({
    posts: {
        type: Array,
        required: true,
    },
    authUser: {
        type: Object,
        default: null,
    },
});

const commentForms = reactive({});
const editingComments = reactive({});
const errors = reactive({});
const processing = reactive({});
const likesState = reactive({});
const likeProcessing = reactive({});
const likeErrors = reactive({});
const bookmarksState = reactive({});
const bookmarkProcessing = reactive({});
const bookmarkErrors = reactive({});

function ensurePostState(postId) {
    if (!commentForms[postId]) {
        commentForms[postId] = { body: '' };
    }

    if (!(postId in editingComments)) {
        editingComments[postId] = null;
    }

    if (!errors[postId]) {
        errors[postId] = '';
    }

    if (!(postId in processing)) {
        processing[postId] = false;
    }

    if (!(postId in likesState)) {
        const post = (props.posts ?? []).find((entry) => entry.id === postId);
        likesState[postId] = {
            count: post?.likes_count ?? 0,
            likedByAuthUser: post?.liked_by_auth_user ?? false,
        };
    }

    if (!(postId in likeProcessing)) {
        likeProcessing[postId] = false;
    }

    if (!(postId in likeErrors)) {
        likeErrors[postId] = '';
    }

    if (!(postId in bookmarksState)) {
        const post = (props.posts ?? []).find((entry) => entry.id === postId);
        bookmarksState[postId] = post?.bookmarked_by_auth_user ?? false;
    }

    if (!(postId in bookmarkProcessing)) {
        bookmarkProcessing[postId] = false;
    }

    if (!(postId in bookmarkErrors)) {
        bookmarkErrors[postId] = '';
    }
}

watch(
    () => props.posts,
    (posts) => {
        (posts ?? []).forEach((post) => ensurePostState(post.id));
    },
    { immediate: true }
);

function startEdit(postId, comment) {
    editingComments[postId] = {
        id: comment.id,
        body: comment.body,
    };
    errors[postId] = '';
}

function cancelEdit(postId) {
    editingComments[postId] = null;
    errors[postId] = '';
}

function setEditingBody(postId, body) {
    if (!editingComments[postId]) {
        return;
    }
    editingComments[postId].body = body;
}

function setCommentBody(postId, body) {
    ensurePostState(postId);
    commentForms[postId].body = body;
}

async function submitComment(postId) {
    ensurePostState(postId);
    processing[postId] = true;
    errors[postId] = '';

    try {
        await axios.post('/api/comments/save', {
            post_id: postId,
            body: commentForms[postId].body,
        });
        window.location.reload();
    } catch (e) {
        errors[postId] =
            e.response?.data?.message ??
            e.response?.data?.errors?.body?.[0] ??
            'Erreur lors de la creation du commentaire.';
    } finally {
        processing[postId] = false;
    }
}

async function updateComment(postId) {
    const editing = editingComments[postId];
    if (!editing) {
        return;
    }

    processing[postId] = true;
    errors[postId] = '';

    try {
        await axios.post('/api/comments/save', {
            id: editing.id,
            post_id: postId,
            body: editing.body,
        });
        window.location.reload();
    } catch (e) {
        errors[postId] =
            e.response?.data?.message ??
            e.response?.data?.errors?.body?.[0] ??
            'Erreur lors de la mise a jour du commentaire.';
    } finally {
        processing[postId] = false;
    }
}

async function toggleLike(postId) {
    if (!props.authUser) {
        return;
    }

    ensurePostState(postId);
    likeProcessing[postId] = true;
    likeErrors[postId] = '';

    try {
        const response = await axios.post(`/api/posts/${postId}/like`);
        const data = response?.data?.data ?? {};
        likesState[postId] = {
            count: data.likes_count ?? likesState[postId].count,
            likedByAuthUser: data.liked_by_auth_user ?? likesState[postId].likedByAuthUser,
        };
    } catch (e) {
        likeErrors[postId] = e.response?.data?.message ?? 'Erreur lors du like.';
    } finally {
        likeProcessing[postId] = false;
    }
}

async function toggleBookmark(postId) {
    if (!props.authUser) {
        return;
    }

    ensurePostState(postId);
    bookmarkProcessing[postId] = true;
    bookmarkErrors[postId] = '';

    try {
        const response = await axios.post(`/api/posts/${postId}/bookmark`);
        const data = response?.data?.data ?? {};
        bookmarksState[postId] = data.bookmarked_by_auth_user ?? bookmarksState[postId];
    } catch (e) {
        bookmarkErrors[postId] = e.response?.data?.message ?? 'Erreur lors de la sauvegarde.';
    } finally {
        bookmarkProcessing[postId] = false;
    }
}

function getCommentBody(postId) {
    ensurePostState(postId);
    return commentForms[postId].body ?? '';
}

function getEditingCommentId(postId) {
    return editingComments[postId]?.id ?? null;
}

function getEditingBody(postId) {
    return editingComments[postId]?.body ?? '';
}

function getError(postId) {
    return errors[postId] ?? '';
}

function getProcessing(postId) {
    return processing[postId] ?? false;
}

function getLikesCount(postId) {
    ensurePostState(postId);
    return likesState[postId].count ?? 0;
}

function getLikedByAuthUser(postId) {
    ensurePostState(postId);
    return likesState[postId].likedByAuthUser ?? false;
}

function getLikeProcessing(postId) {
    return likeProcessing[postId] ?? false;
}

function getLikeError(postId) {
    return likeErrors[postId] ?? '';
}

function getBookmarkedByAuthUser(postId) {
    ensurePostState(postId);
    return bookmarksState[postId] ?? false;
}

function getBookmarkProcessing(postId) {
    return bookmarkProcessing[postId] ?? false;
}

function getBookmarkError(postId) {
    return bookmarkErrors[postId] ?? '';
}
</script>

<template>
    <p v-if="posts.length === 0" class="text-gray-600">Aucun post pour le moment.</p>

    <ul v-else class="space-y-4">
        <Post
            v-for="post in posts"
            :key="post.id"
            :post="post"
            :auth-user="authUser"
            :comment-body="getCommentBody(post.id)"
            :editing-comment-id="getEditingCommentId(post.id)"
            :editing-body="getEditingBody(post.id)"
            :error="getError(post.id)"
            :processing="getProcessing(post.id)"
            :likes-count="getLikesCount(post.id)"
            :liked-by-auth-user="getLikedByAuthUser(post.id)"
            :like-processing="getLikeProcessing(post.id)"
            :like-error="getLikeError(post.id)"
            :bookmarked-by-auth-user="getBookmarkedByAuthUser(post.id)"
            :bookmark-processing="getBookmarkProcessing(post.id)"
            :bookmark-error="getBookmarkError(post.id)"
            @start-edit="startEdit"
            @cancel-edit="cancelEdit"
            @submit-comment="submitComment"
            @update-comment="updateComment"
            @edit-body="setEditingBody"
            @comment-body="setCommentBody"
            @toggle-like="toggleLike"
            @toggle-bookmark="toggleBookmark"
        />
    </ul>
</template>
