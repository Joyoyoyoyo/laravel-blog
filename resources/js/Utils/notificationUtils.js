import axios from 'axios';
import { NOTIFICATION_TYPES } from '@/Utils/notificationTypes';

export function formatNotificationLabel(notification) {
    const actor = notification?.data?.actor_name ?? "Quelqu'un";
    const title = notification?.data?.post_title ?? 'votre post';

    if (notification?.type === NOTIFICATION_TYPES.POST_LIKED) {
        return `${actor} a like votre post "${title}"`;
    }

    if (notification?.type === NOTIFICATION_TYPES.POST_COMMENTED) {
        return `${actor} a commente votre post "${title}"`;
    }

    return 'Nouvelle notification';
}

/**
 * Marque une notification comme lue cote API et met a jour l'objet local.
 * Retourne le nouveau unread_count si l'API l'a renvoye, sinon null.
 */
export async function markNotificationAsRead(notification) {
    if (!notification || notification.read_at) {
        return null;
    }

    try {
        const response = await axios.post(`/api/notifications/${notification.id}/read`);
        notification.read_at = new Date().toISOString();
        return response?.data?.data?.unread_count ?? null;
    } catch (e) {
        return null;
    }
}

/**
 * Marque toutes les notifications comme lues cote API et met a jour les objets locaux.
 * Retourne le nouveau unread_count (0) si l'API a repondu, sinon null.
 */
export async function markAllNotificationsAsRead(notifications = []) {
    try {
        const response = await axios.post('/api/notifications/read-all');
        const now = new Date().toISOString();
        notifications.forEach((notification) => {
            if (!notification.read_at) {
                notification.read_at = now;
            }
        });
        return response?.data?.data?.unread_count ?? 0;
    } catch (e) {
        return null;
    }
}
