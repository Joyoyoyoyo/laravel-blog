<script setup>
import AppHeader from '@/Components/Layout/AppHeader.vue';
import PostListPaginated from '@/Components/Posts/PostListPaginated.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

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

const subscribersCount = ref(props.user.subscribers_count ?? 0);
const subscribedByAuthUser = ref(!!props.user.subscribed_by_auth_user);
const subscribeProcessing = ref(false);
const subscribeError = ref('');

watch(
    () => props.user,
    (u) => {
        subscribersCount.value = u.subscribers_count ?? 0;
        subscribedByAuthUser.value = !!u.subscribed_by_auth_user;
        subscribeError.value = '';
    },
    { deep: true },
);

async function toggleSubscribe() {
    subscribeProcessing.value = true;
    subscribeError.value = '';
    try {
        const { data } = await window.axios.post(`/api/users/${props.user.id}/subscribe`);
        subscribedByAuthUser.value = !!data?.data?.subscribed_by_auth_user;
        if (typeof data?.data?.subscribers_count === 'number') {
            subscribersCount.value = data.data.subscribers_count;
        }
    } catch (e) {
        subscribeError.value =
            e.response?.data?.message ?? "Impossible de modifier l'abonnement.";
    } finally {
        subscribeProcessing.value = false;
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

        <main class="mx-auto max-w-3xl px-6 py-12">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ user.name }}</h1>
                    <p v-if="user.member_since" class="mt-1 text-xs text-gray-500">
                        Membre depuis {{ user.member_since }}
                    </p>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-2">
                    <a
                        v-if="isOwnProfile"
                        href="/profile"
                        class="text-sm text-indigo-600 hover:text-indigo-800"
                    >
                        Modifier mon profil
                    </a>
                    <button
                        v-else-if="authUser"
                        type="button"
                        class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                        :disabled="subscribeProcessing"
                        @click="toggleSubscribe"
                    >
                        {{ subscribedByAuthUser ? 'Se désabonner' : "S'abonner" }}
                    </button>
                    <p v-if="subscribeError" class="max-w-xs text-right text-xs text-red-600">
                        {{ subscribeError }}
                    </p>
                </div>
            </div>

            <section class="mt-6 rounded-lg border border-gray-200 bg-white p-4">
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
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Abonnés</dt>
                        <dd class="mt-1 text-lg font-semibold">
                            <Link
                                :href="`/users/${user.id}/subscribers`"
                                class="text-indigo-600 hover:text-indigo-800 hover:underline"
                            >
                                {{ subscribersCount }}
                            </Link>
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
    </div>
</template>
