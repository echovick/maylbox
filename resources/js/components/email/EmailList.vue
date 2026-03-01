<script setup lang="ts">
import type { Email } from '@/types/email';
import EmailListItem from './EmailListItem.vue';
import { Skeleton } from '@/components/ui/skeleton';

defineProps<{
    emails: Email[];
    selectedEmailId?: string | null;
    loading?: boolean;
}>();

const emit = defineEmits<{
    selectEmail: [emailId: string];
}>();

const handleSelect = (emailId: string) => {
    emit('selectEmail', emailId);
};
</script>

<template>
    <div class="flex h-full flex-col overflow-hidden">
        <!-- Loading State -->
        <div v-if="loading" class="space-y-2 p-4">
            <div v-for="i in 5" :key="i" class="flex gap-3">
                <Skeleton class="h-10 w-10 rounded-full" />
                <div class="flex-1 space-y-2">
                    <Skeleton class="h-4 w-3/4" />
                    <Skeleton class="h-3 w-full" />
                    <Skeleton class="h-3 w-2/3" />
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else-if="emails.length === 0"
            class="flex flex-1 flex-col items-center justify-center p-8 text-center"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mb-4 h-16 w-16 text-muted-foreground/50"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
            >
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="m2 7 10 7 10-7" />
            </svg>
            <h3 class="mb-2 text-lg font-medium text-foreground">
                No emails found
            </h3>
            <p class="text-sm text-muted-foreground">
                Your inbox is empty or no emails match your current filter.
            </p>
        </div>

        <!-- Email List -->
        <div
            v-else
            class="flex-1 overflow-y-auto"
        >
            <EmailListItem
                v-for="email in emails"
                :key="email.id"
                :email="email"
                :is-selected="email.id === selectedEmailId"
                @select="handleSelect"
            />
        </div>
    </div>
</template>
