<script setup lang="ts">
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import type { Appearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const modes: Appearance[] = ['light', 'dark', 'system'];

const currentIcon = computed(() => {
    switch (appearance.value) {
        case 'light':
            return Sun;
        case 'dark':
            return Moon;
        default:
            return Monitor;
    }
});

const currentLabel = computed(() => {
    switch (appearance.value) {
        case 'light':
            return 'Light';
        case 'dark':
            return 'Dark';
        default:
            return 'System';
    }
});

function cycle() {
    const idx = modes.indexOf(appearance.value);
    const next = modes[(idx + 1) % modes.length];
    updateAppearance(next);
}
</script>

<template>
    <button
        @click="cycle"
        class="inline-flex h-9 w-9 items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
        :title="`Theme: ${currentLabel}`"
    >
        <component :is="currentIcon" class="h-5 w-5" />
        <span class="sr-only">Toggle theme ({{ currentLabel }})</span>
    </button>
</template>
