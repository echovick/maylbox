<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import EmailList from '@/components/email/EmailList.vue';
import EmailViewer from '@/components/email/EmailViewer.vue';
import ThreadView from '@/components/email/ThreadView.vue';
import ComposeSheet from '@/components/email/ComposeSheet.vue';
import { useEmails } from '@/composables/useEmails';
import { useCompose } from '@/composables/useCompose';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { categories } from '@/data/dummyEmails';

const {
    filteredEmails,
    selectedEmail,
    selectedEmailId,
    selectedThread,
    currentFolder,
    unreadCount,
    selectEmail,
    toggleStar,
    deleteEmail,
    markAsRead,
} = useEmails();

const { openCompose } = useCompose();

// Local state
const activeCategory = ref('primary');
const searchQuery = ref('');
const showEmailViewer = ref(false);

// Computed
const displayEmails = computed(() => {
    // For now, just return filtered emails
    // Later we can filter by category
    return filteredEmails.value;
});

const currentRange = computed(() => {
    const total = currentFolder.value?.totalCount || 0;
    const displayed = Math.min(20, displayEmails.value.length);
    return `1-${displayed} out of ${total}`;
});

// Actions
const handleSelectEmail = (emailId: string) => {
    selectEmail(emailId);
    showEmailViewer.value = true;
};

const handleCloseViewer = () => {
    showEmailViewer.value = false;
};

const handleDeleteEmail = (emailId: string) => {
    deleteEmail(emailId);
    showEmailViewer.value = false;
};

const handleToggleStar = (emailId: string) => {
    toggleStar(emailId);
};

const handleCompose = () => {
    openCompose();
};

const handleRefresh = () => {
    // TODO: Implement refresh
    console.log('Refreshing emails...');
};

const handleSelectAll = () => {
    // TODO: Implement select all
    console.log('Select all emails');
};
</script>

<template>
    <Head title="Mail" />

    <AppLayout :show-breadcrumbs="false">
        <div class="flex h-full flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="flex items-center justify-between border-b border-sidebar-border px-6 py-3">
                <!-- Left: Inbox Title and Actions -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-foreground"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m2 7 10 7 10-7" />
                        </svg>
                        <h1 class="text-lg font-semibold text-foreground">Inbox</h1>
                    </div>
                </div>

                <!-- Right: Count and Buttons -->
                <div class="flex items-center gap-3">
                    <span class="text-sm text-muted-foreground">
                        {{ currentRange }}
                    </span>

                    <div class="flex items-center gap-1">
                        <Button variant="ghost" size="icon" @click="handleRefresh">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16" />
                            </svg>
                        </Button>

                        <Button variant="ghost" size="icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </Button>

                        <Button variant="ghost" size="icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <line x1="4" x2="20" y1="12" y2="12" />
                                <line x1="4" x2="20" y1="6" y2="6" />
                                <line x1="4" x2="20" y1="18" y2="18" />
                            </svg>
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="border-b border-sidebar-border px-6">
                <div class="flex items-center gap-6">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        :class="[
                            'relative border-b-2 px-1 py-3 text-sm font-medium transition-colors',
                            activeCategory === category.id
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:text-foreground',
                        ]"
                        @click="activeCategory = category.id"
                    >
                        {{ category.label }}
                        <Badge
                            v-if="category.count > 0"
                            variant="secondary"
                            class="ml-2"
                        >
                            {{ category.count }}
                        </Badge>
                    </button>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center gap-2 border-b border-sidebar-border px-6 py-2">
                <Button variant="ghost" size="sm" @click="handleSelectAll">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mr-2 h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    Select
                </Button>

                <Button variant="ghost" size="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                </Button>

                <Button variant="ghost" size="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <path d="M9 22V12h6v10" />
                    </svg>
                </Button>

                <Button variant="ghost" size="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                    </svg>
                </Button>

                <Button variant="ghost" size="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                    </svg>
                </Button>

                <Button variant="ghost" size="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="12" cy="12" r="1" />
                        <circle cx="19" cy="12" r="1" />
                        <circle cx="5" cy="12" r="1" />
                    </svg>
                </Button>

                <div class="ml-auto flex items-center gap-2">
                    <span class="text-xs text-muted-foreground">Filter</span>
                    <Button variant="ghost" size="icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                        </svg>
                    </Button>

                    <!-- Pagination -->
                    <div class="flex items-center gap-1">
                        <Button variant="ghost" size="icon" disabled>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                        </Button>
                        <Button variant="ghost" size="icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M9 18l6-6-6-6" />
                            </svg>
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex flex-1 overflow-hidden">
                <!-- Email List -->
                <div
                    :class="[
                        'flex-1 overflow-hidden border-r border-sidebar-border',
                        selectedEmail && showEmailViewer ? 'hidden lg:block lg:w-96' : '',
                    ]"
                >
                    <EmailList
                        :emails="displayEmails"
                        :selected-email-id="selectedEmailId"
                        @select-email="handleSelectEmail"
                    />
                </div>

                <!-- Email Viewer (Thread or Single) -->
                <div
                    v-if="selectedEmail"
                    :class="[
                        'flex-1 overflow-hidden',
                        !showEmailViewer ? 'hidden lg:block' : '',
                    ]"
                >
                    <!-- Show ThreadView if email is part of a thread -->
                    <ThreadView
                        v-if="selectedThread"
                        :thread="selectedThread"
                        @close="handleCloseViewer"
                        @delete="handleDeleteEmail"
                        @toggle-star="handleToggleStar"
                    />

                    <!-- Otherwise show regular EmailViewer -->
                    <EmailViewer
                        v-else
                        :email="selectedEmail"
                        @close="handleCloseViewer"
                        @delete="handleDeleteEmail"
                        @toggle-star="handleToggleStar"
                    />
                </div>

                <!-- Empty State (when no email selected) -->
                <div
                    v-else
                    class="hidden flex-1 items-center justify-center lg:flex"
                >
                    <div class="text-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mx-auto mb-4 h-16 w-16 text-muted-foreground/30"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m2 7 10 7 10-7" />
                        </svg>
                        <p class="text-muted-foreground">
                            Select an email to read
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Compose Button (Mobile) -->
        <Button
            class="fixed bottom-6 right-6 h-14 w-14 rounded-full shadow-lg lg:hidden"
            @click="handleCompose"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M12 5v14M5 12h14" />
            </svg>
        </Button>

        <!-- Compose Sheet -->
        <ComposeSheet />
    </AppLayout>
</template>
