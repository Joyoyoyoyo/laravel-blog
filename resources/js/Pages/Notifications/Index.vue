<script setup>
import AppHeader from '@/Components/Layout/AppHeader.vue';
import InertiaPaginationLinks from '@/Components/InertiaPaginationLinks.vue';
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
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

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

                <InertiaPaginationLinks :links="notifications.links" />
            </section>
        </main>
    </div>
</template>
