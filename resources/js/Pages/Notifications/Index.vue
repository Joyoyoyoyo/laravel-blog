<script setup>
import { Link } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { NOTIFICATION_TYPES } from '@/Utils/notificationTypes';
import {
    formatNotificationLabel,
    markAllNotificationsAsRead,
    markNotificationAsRead,
} from '@/Utils/notificationUtils';

const props = defineProps({
    notifications: {
        type: Object,
        required: true,
    },
    authUser: {
        type: Object,
        default: null,
    },
});

const items = reactive(props.notifications.data.map((n) => ({ ...n })));
</script>

<template>
    <main class="mx-auto max-w-3xl px-6 py-12">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Notifications</h1>
            <button
                type="button"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                @click="markAllNotificationsAsRead(items)"
            >
                Tout marquer comme lu
            </button>
        </div>

        <p v-if="authUser" class="mt-2 text-sm text-gray-500">
            Connecte en tant que {{ authUser.name }}.
        </p>

        <section class="mt-8">
            <p v-if="items.length === 0" class="text-gray-600">Aucune notification.</p>

            <ul v-else class="space-y-3">
                <li
                    v-for="notification in items"
                    :key="notification.id"
                    class="rounded-lg border p-4"
                    :class="notification.read_at ? 'border-gray-200 bg-white' : 'border-indigo-200 bg-indigo-50'"
                    @click="markNotificationAsRead(notification)"
                >
                    <p class="text-sm text-gray-800">{{ formatNotificationLabel(notification) }}</p>
                    <p
                        v-if="notification.type === NOTIFICATION_TYPES.POST_COMMENTED && notification.data?.comment_excerpt"
                        class="mt-1 text-xs italic text-gray-600"
                    >
                        "{{ notification.data.comment_excerpt }}"
                    </p>
                    <p class="mt-1 text-xs text-gray-500">{{ notification.created_at }}</p>
                </li>
            </ul>

            <nav v-if="notifications.links?.length > 3" class="mt-6 flex flex-wrap gap-2">
                <template v-for="link in notifications.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded border px-3 py-1 text-xs"
                        :class="link.active ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-100'"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="rounded border border-gray-200 px-3 py-1 text-xs text-gray-400"
                        v-html="link.label"
                    />
                </template>
            </nav>
        </section>
    </main>
</template>
