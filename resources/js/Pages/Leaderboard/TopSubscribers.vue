<script setup>
import AppHeader from '@/Components/Layout/AppHeader.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    ranking: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

        <main class="mx-auto max-w-3xl px-6 py-12">
            <h1 class="text-3xl font-bold">Top 10 — plus d&apos;abonnés</h1>
            <p class="mt-1 text-sm text-gray-500">
                Les profils avec le plus d&apos;abonnés sur la plateforme.
            </p>

            <section class="mt-8">
                <p
                    v-if="ranking.length === 0"
                    class="rounded-md border border-gray-200 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500"
                >
                    Aucun membre pour le moment.
                </p>

                <ol
                    v-else
                    class="divide-y divide-gray-200 overflow-hidden rounded-lg border border-gray-200 bg-white"
                >
                    <li
                        v-for="(entry, index) in ranking"
                        :key="entry.id"
                        class="flex items-center gap-4 px-4 py-3"
                    >
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold tabular-nums"
                            :class="
                                index === 0
                                    ? 'bg-amber-100 text-amber-900'
                                    : index === 1
                                      ? 'bg-slate-200 text-slate-800'
                                      : index === 2
                                        ? 'bg-orange-100 text-orange-900'
                                        : 'bg-gray-100 text-gray-600'
                            "
                        >
                            {{ index + 1 }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <Link
                                :href="`/users/${entry.id}`"
                                class="truncate font-medium text-indigo-600 hover:text-indigo-800 hover:underline"
                            >
                                {{ entry.name }}
                            </Link>
                        </div>
                        <span class="shrink-0 text-sm tabular-nums text-gray-600">
                            {{ entry.subscribers_count }}
                            {{
                                entry.subscribers_count === 1 ? ' abonné' : ' abonnés'
                            }}
                        </span>
                    </li>
                </ol>
            </section>
        </main>
    </div>
</template>
