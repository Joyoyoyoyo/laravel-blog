<script setup>
import axios from 'axios';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { NOTIFICATION_TYPES } from '@/Utils/notificationTypes';
import {
    formatNotificationLabel,
    markAllNotificationsAsRead,
    markNotificationAsRead,
} from '@/Utils/notificationUtils';

const open = ref(false);
const loading = ref(false);
const notifications = ref([]);
const unreadCountLocal = ref(null);
const dropdownRef = ref(null);

const page = usePage();

const sharedUnreadCount = computed(() => page.props.auth?.unread_notifications_count ?? 0);
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const unreadCount = computed(() => unreadCountLocal.value ?? sharedUnreadCount.value);

async function fetchNotifications() {
    loading.value = true;
    try {
        const response = await axios.get('/api/notifications');
        notifications.value = response?.data?.data ?? [];
        unreadCountLocal.value = response?.data?.unread_count ?? 0;
    } catch (e) {
        notifications.value = [];
    } finally {
        loading.value = false;
    }
}

async function toggleDropdown() {
    if (!isAuthenticated.value) {
        return;
    }
    open.value = !open.value;
    if (open.value) {
        await fetchNotifications();
    }
}

async function markAsRead(notification) {
    if (notification.read_at) {
        return goTo(notification);
    }

    const newUnreadCount = await markNotificationAsRead(notification);
    if (newUnreadCount !== null) {
        unreadCountLocal.value = newUnreadCount;
    }
    goTo(notification);
}

async function markAllAsRead() {
    const newUnreadCount = await markAllNotificationsAsRead(notifications.value);
    if (newUnreadCount !== null) {
        unreadCountLocal.value = newUnreadCount;
    }
}

function goTo(notification) {
    open.value = false;
    if (notification?.data?.post_id) {
        router.visit('/posts');
    }
}

function handleClickOutside(event) {
    if (!dropdownRef.value) {
        return;
    }
    if (!dropdownRef.value.contains(event.target)) {
        open.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div v-if="isAuthenticated" ref="dropdownRef" class="relative inline-block">
        <button
            type="button"
            class="relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
            @click.stop="toggleDropdown"
            aria-label="Notifications"
        >
            <span class="text-base">🔔</span>
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-semibold text-white"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <div
            v-if="open"
            class="absolute right-0 z-20 mt-2 w-80 rounded-md border border-gray-200 bg-white shadow-lg"
        >
            <div class="flex items-center justify-between border-b border-gray-100 px-3 py-2">
                <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                <button
                    v-if="unreadCount > 0"
                    type="button"
                    class="text-xs text-indigo-600 hover:text-indigo-800"
                    @click="markAllAsRead"
                >
                    Tout marquer comme lu
                </button>
            </div>

            <div class="max-h-80 overflow-y-auto">
                <p v-if="loading" class="px-3 py-4 text-center text-sm text-gray-500">
                    Chargement...
                </p>

                <p
                    v-else-if="notifications.length === 0"
                    class="px-3 py-4 text-center text-sm text-gray-500"
                >
                    Aucune notification.
                </p>

                <ul v-else class="divide-y divide-gray-100">
                    <li
                        v-for="notification in notifications"
                        :key="notification.id"
                        class="cursor-pointer px-3 py-2 text-sm hover:bg-gray-50"
                        :class="{ 'bg-indigo-50': !notification.read_at }"
                        @click="markAsRead(notification)"
                    >
                        <p class="text-gray-800">{{ formatNotificationLabel(notification) }}</p>
                        <p
                            v-if="notification.type === NOTIFICATION_TYPES.POST_COMMENTED && notification.data?.comment_excerpt"
                            class="mt-1 text-xs text-gray-600 italic"
                        >
                            "{{ notification.data.comment_excerpt }}"
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ notification.created_at }}
                        </p>
                    </li>
                </ul>
            </div>

            <div class="border-t border-gray-100 px-3 py-2 text-right">
                <a href="/notifications" class="text-xs text-indigo-600 hover:text-indigo-800">
                    Voir toutes les notifications
                </a>
            </div>
        </div>
    </div>
</template>
