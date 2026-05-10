<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    links: {
        type: Array,
        default: () => [],
    },
    /**
     * When true, hide the nav if there is only one page (Laravel emits few link slots).
     */
    compact: {
        type: Boolean,
        default: true,
    },
    navClass: {
        type: String,
        default: 'mt-6 flex flex-wrap gap-2',
    },
    linkSize: {
        type: String,
        default: 'xs',
        validator: (v) => ['xs', 'sm'].includes(v),
    },
});

const visible = computed(() => {
    const list = props.links;
    if (!Array.isArray(list) || list.length === 0) {
        return false;
    }
    return props.compact ? list.length > 3 : true;
});

const sizeClass = computed(() => (props.linkSize === 'sm' ? 'text-sm' : 'text-xs'));
</script>

<template>
    <nav v-if="visible" :class="navClass">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                class="rounded border px-3 py-1"
                :class="[
                    sizeClass,
                    link.active
                        ? 'border-indigo-600 bg-indigo-600 text-white'
                        : 'border-gray-300 text-gray-700 hover:bg-gray-100',
                ]"
                v-html="link.label"
            />
            <span
                v-else
                class="rounded border border-gray-200 px-3 py-1 text-gray-400"
                :class="sizeClass"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
