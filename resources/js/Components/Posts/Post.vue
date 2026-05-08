<script setup>
const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    authUser: {
        type: Object,
        default: null,
    },
    commentBody: {
        type: String,
        default: '',
    },
    editingCommentId: {
        type: [Number, String],
        default: null,
    },
    editingBody: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    processing: {
        type: Boolean,
        default: false,
    },
    likesCount: {
        type: Number,
        default: 0,
    },
    likedByAuthUser: {
        type: Boolean,
        default: false,
    },
    likeProcessing: {
        type: Boolean,
        default: false,
    },
    likeError: {
        type: String,
        default: '',
    },
    bookmarkedByAuthUser: {
        type: Boolean,
        default: false,
    },
    bookmarkProcessing: {
        type: Boolean,
        default: false,
    },
    bookmarkError: {
        type: String,
        default: '',
    },
});

const emit = defineEmits([
    'start-edit',
    'cancel-edit',
    'submit-comment',
    'update-comment',
    'edit-body',
    'comment-body',
    'toggle-like',
    'toggle-bookmark',
]);
</script>

<template>
    <li class="rounded-lg border border-gray-200 p-4">
        <h2 class="text-lg font-semibold">{{ post.title }}</h2>
        <p class="mt-1 text-xs text-gray-500">
            Par
            <a
                v-if="post.author_id"
                :href="`/users/${post.author_id}`"
                class="font-medium text-indigo-600 hover:text-indigo-800"
            >{{ post.author ?? 'Inconnu' }}</a>
            <span v-else>{{ post.author ?? 'Inconnu' }}</span>
            <span v-if="post.category">- {{ post.category }}</span>
            - {{ post.created_at }}
        </p>
        <p class="mt-2 text-sm text-gray-700">
            {{ post.body }}
        </p>

        <div class="mt-3 flex items-center gap-3">
            <button
                v-if="authUser"
                type="button"
                class="rounded border border-indigo-300 px-2.5 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50 disabled:opacity-50"
                :disabled="likeProcessing"
                @click="emit('toggle-like', post.id)"
            >
                {{ likedByAuthUser ? 'Unlike' : 'Like' }}
            </button>
            <p v-else class="text-xs text-gray-500">
                Connecte-toi pour liker.
            </p>
            <span class="text-xs text-gray-600">
                {{ likesCount }} like{{ likesCount > 1 ? 's' : '' }}
            </span>

            <button
                v-if="authUser"
                type="button"
                class="ml-2 inline-flex items-center gap-1 rounded border border-amber-300 px-2.5 py-1 text-xs font-medium text-amber-700 hover:bg-amber-50 disabled:opacity-50"
                :class="{ 'bg-amber-100': bookmarkedByAuthUser }"
                :disabled="bookmarkProcessing"
                :title="bookmarkedByAuthUser ? 'Retirer des favoris' : 'Ajouter aux favoris'"
                @click="emit('toggle-bookmark', post.id)"
            >
                <span aria-hidden="true">{{ bookmarkedByAuthUser ? '★' : '☆' }}</span>
                {{ bookmarkedByAuthUser ? 'Sauvegarde' : 'Sauvegarder' }}
            </button>
        </div>
        <p v-if="likeError" class="mt-1 text-xs text-red-600">
            {{ likeError }}
        </p>
        <p v-if="bookmarkError" class="mt-1 text-xs text-red-600">
            {{ bookmarkError }}
        </p>

        <div v-if="authUser?.id && post.author_id && authUser.id === post.author_id" class="mt-3">
            <a
                :href="`/posts/editor/${post.id}`"
                class="inline-block text-sm font-medium text-indigo-600 hover:text-indigo-800"
            >
                Modifier le post
            </a>
        </div>

        <div class="mt-4 border-t pt-4">
            <h3 class="text-sm font-semibold text-gray-800">Commentaires</h3>

            <p v-if="(post.comments ?? []).length === 0" class="mt-2 text-sm text-gray-500">Aucun commentaire.</p>

            <ul v-else class="mt-3 space-y-3">
                <li
                    v-for="comment in (post.comments ?? [])"
                    :key="comment.id"
                    class="rounded-md border border-gray-100 bg-gray-50 p-3"
                >
                    <p class="text-sm text-gray-800">{{ comment.body }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ comment.author ?? 'Inconnu' }} - {{ comment.created_at }}
                    </p>

                    <div v-if="comment.can_edit" class="mt-2">
                        <button
                            v-if="editingCommentId !== comment.id"
                            type="button"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-800"
                            @click="emit('start-edit', post.id, comment)"
                        >
                            Modifier (15 min)
                        </button>

                        <div v-else class="space-y-2">
                            <textarea
                                :value="editingBody"
                                rows="3"
                                class="block w-full rounded-md border border-gray-300 px-2 py-1 text-sm"
                                @input="emit('edit-body', post.id, $event.target.value)"
                            />
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    class="rounded bg-indigo-600 px-2 py-1 text-xs text-white hover:bg-indigo-700 disabled:opacity-50"
                                    :disabled="processing"
                                    @click="emit('update-comment', post.id)"
                                >
                                    Enregistrer
                                </button>
                                <button
                                    type="button"
                                    class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-100"
                                    @click="emit('cancel-edit', post.id)"
                                >
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="mt-4">
                <p v-if="!authUser" class="text-sm text-gray-500">Connecte-toi pour commenter.</p>

                <form v-else class="space-y-2" @submit.prevent="emit('submit-comment', post.id)">
                    <textarea
                        :value="commentBody"
                        rows="3"
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                        placeholder="Ecris ton commentaire..."
                        required
                        @input="emit('comment-body', post.id, $event.target.value)"
                    />
                    <p v-if="error" class="text-xs text-red-600">
                        {{ error }}
                    </p>
                    <button
                        type="submit"
                        class="rounded bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                        :disabled="processing"
                    >
                        Commenter
                    </button>
                </form>
            </div>
        </div>
    </li>
</template>
