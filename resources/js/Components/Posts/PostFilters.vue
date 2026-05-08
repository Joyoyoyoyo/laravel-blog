<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({
            category_id: '',
            date_from: '',
            date_to: '',
        }),
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['apply', 'reset']);

const localFilters = reactive({
    category_id: '',
    date_from: '',
    date_to: '',
});

watch(
    () => props.filters,
    (next) => {
        localFilters.category_id = next?.category_id ?? '';
        localFilters.date_from = next?.date_from ?? '';
        localFilters.date_to = next?.date_to ?? '';
    },
    { immediate: true, deep: true }
);

function submitFilters() {
    emit('apply', {
        category_id: localFilters.category_id || '',
        date_from: localFilters.date_from || '',
        date_to: localFilters.date_to || '',
    });
}

function resetFilters() {
    localFilters.category_id = '';
    localFilters.date_from = '';
    localFilters.date_to = '';
    emit('reset');
}
</script>

<template>
    <form class="mb-6 grid gap-3 rounded-lg border border-gray-200 p-4 md:grid-cols-4" @submit.prevent="submitFilters">
        <select
            v-model="localFilters.category_id"
            class="rounded-md border border-gray-300 px-3 py-2 text-sm"
        >
            <option value="">Toutes les categories</option>
            <option
                v-for="category in categories"
                :key="category.id"
                :value="String(category.id)"
            >
                {{ category.name }}
            </option>
        </select>

        <input
            v-model="localFilters.date_from"
            type="date"
            class="rounded-md border border-gray-300 px-3 py-2 text-sm"
        />

        <input
            v-model="localFilters.date_to"
            type="date"
            class="rounded-md border border-gray-300 px-3 py-2 text-sm"
        />

        <div class="flex gap-2">
            <button
                type="submit"
                class="rounded bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                Filtrer
            </button>
            <button
                type="button"
                class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="resetFilters"
            >
                Reinitialiser
            </button>
        </div>
    </form>
</template>
