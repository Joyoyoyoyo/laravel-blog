<script setup>
defineProps({
    appName: {
        type: String,
        default: 'Laravel',
    },
});
</script>

<template>
    <main class="mx-auto max-w-3xl px-6 py-12">
        <h1 class="text-3xl font-bold">Inertia + Vue est configure</h1>
        <p class="mt-4 text-gray-600">
            Tu peux maintenant creer tes composants dans
            <code>resources/js/Pages</code> et les rendre avec
            <code>Inertia::render()</code>.
        </p>
    </main>
</template>
