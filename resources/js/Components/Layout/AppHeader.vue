<script setup>
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const authUser = computed(() => page.props.auth?.user ?? null);
const isAuthenticated = computed(() => Boolean(authUser.value));
const currentPath = computed(() => {
    const url = page.url ?? '/';
    return url.split('?')[0];
});

function isActive(path, { exact = false } = {}) {
    if (exact) {
        return currentPath.value === path;
    }
    return currentPath.value === path || currentPath.value.startsWith(`${path}/`);
}

function logout() {
    router.post('/api/logout');
}

const navLinks = computed(() => {
    const links = [
        { label: 'Accueil', href: isAuthenticated.value ? '/home' : '/', exact: true },
        { label: 'Tous les posts', href: '/posts' },
    ];

    if (isAuthenticated.value) {
        links.push({ label: 'Ecrire un post', href: '/posts/editor', exact: true });
        links.push({ label: 'Mes favoris', href: '/bookmarks' });
    }

    return links;
});
</script>

<template>
    <header class="border-b border-gray-200 bg-white">
        <div class="bg-slate-900 text-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-3">
                <a href="/" class="flex items-baseline gap-1 font-bold tracking-tight">
                    <span class="text-2xl text-white">Mon</span>
                    <span class="text-2xl text-indigo-300">Blog</span>
                </a>

                <div class="flex items-center gap-2 sm:gap-6">
                    <a
                        href="/posts"
                        class="group flex flex-col items-center text-white/90 hover:text-white"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.5-3.5" />
                        </svg>
                        <span class="mt-1 hidden text-[10px] font-semibold uppercase tracking-wide sm:block">Rechercher</span>
                    </a>

                    <a
                        :href="isAuthenticated ? '/profile' : '/login'"
                        class="group flex flex-col items-center text-white/90 hover:text-white"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 21a8 8 0 0 1 16 0" />
                        </svg>
                        <span class="mt-1 hidden text-[10px] font-semibold uppercase tracking-wide sm:block">
                            {{ isAuthenticated ? 'Mon compte' : 'Connexion' }}
                        </span>
                    </a>

                    <a
                        v-if="isAuthenticated"
                        href="/bookmarks"
                        class="group flex flex-col items-center text-white/90 hover:text-white"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                        </svg>
                        <span class="mt-1 hidden text-[10px] font-semibold uppercase tracking-wide sm:block">Favoris</span>
                    </a>

                    <div v-if="isAuthenticated" class="flex flex-col items-center text-white/90">
                        <NotificationBell />
                        <span class="mt-1 hidden text-[10px] font-semibold uppercase tracking-wide sm:block">Notifs</span>
                    </div>

                    <button
                        v-if="isAuthenticated"
                        type="button"
                        class="group flex flex-col items-center text-white/90 hover:text-white"
                        @click="logout"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 17l5-5-5-5" />
                            <path d="M21 12H9" />
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        </svg>
                        <span class="mt-1 hidden text-[10px] font-semibold uppercase tracking-wide sm:block">Deconnexion</span>
                    </button>
                </div>
            </div>
        </div>

        <nav class="border-t border-gray-100 bg-white">
            <ul class="mx-auto flex max-w-6xl items-center gap-2 overflow-x-auto px-6 py-3 sm:gap-6">
                <li v-for="link in navLinks" :key="link.href" class="shrink-0">
                    <a
                        :href="link.href"
                        class="text-xs font-semibold uppercase tracking-wide transition-colors sm:text-sm"
                        :class="isActive(link.href, { exact: link.exact ?? false })
                            ? 'text-indigo-700 underline underline-offset-8 decoration-2'
                            : 'text-slate-700 hover:text-indigo-600'"
                    >
                        {{ link.label }}
                    </a>
                </li>

                <li v-if="authUser" class="ml-auto shrink-0 text-xs text-slate-500 sm:text-sm">
                    Connecte en tant que <span class="font-semibold text-slate-700">{{ authUser.name }}</span>
                </li>
            </ul>
        </nav>
    </header>
</template>
