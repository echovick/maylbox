<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import ComposeSheet from '@/components/email/ComposeSheet.vue';
import EmailList from '@/components/email/EmailList.vue';
import EmailViewer from '@/components/email/EmailViewer.vue';
import LabelManager from '@/components/email/LabelManager.vue';
import MailSidebar from '@/components/email/MailSidebar.vue';
import ThreadView from '@/components/email/ThreadView.vue';
import UpdateCredentialsDialog from '@/components/email/UpdateCredentialsDialog.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useCompose } from '@/composables/useCompose';
import { useEmails } from '@/composables/useEmails';
import { categories } from '@/data/dummyEmails';

const props = defineProps<{
    accounts: any[];
    defaultAccountId: number | null;
}>();

const {
    filteredEmails,
    selectedEmail,
    selectedEmailId,
    selectedThread,
    currentFolder,
    searchQuery,
    isSearching,
    labels,
    syncStatus,
    syncError,
    currentPage,
    totalPages,
    totalEmails,
    initializeFromProps,
    selectEmail,
    toggleStar,
    deleteEmail,
    setSearchQuery,
    clearSearch,
    refreshEmails,
    goToPage,
    createLabel,
    updateLabel,
    deleteLabel,
    updateCredentials,
    currentAccountId,
    accounts} = useEmails();

const { openCompose } = useCompose();

// Local state
const activeCategory = ref('primary');
const showEmailViewer = ref(false);
const showSearchBar = ref(false);
const showLabelManager = ref(false);
const showCredentialsDialog = ref(false);

const currentAccount = computed(() =>
    accounts.value.find(acc => String(acc.id) === currentAccountId.value)
);

const isSyncing = computed(() => syncStatus.value === 'pending' || syncStatus.value === 'syncing');
const hasSyncError = computed(() => syncStatus.value === 'failed');
const syncProgress = computed(() => {
    if (syncStatus.value === 'syncing') return 'Syncing your inbox...';
    if (syncStatus.value === 'pending') return 'Loading your inbox...';
    return '';
});

onMounted(() => {
    initializeFromProps(props.accounts, props.defaultAccountId);
});

// Computed
const displayEmails = computed(() => {
    // For now, just return filtered emails
    // Later we can filter by category
    return filteredEmails.value;
});

const currentRange = computed(() => {
    const total = totalEmails.value || currentFolder.value?.totalCount || 0;
    const perPage = 20;
    const start = total > 0 ? (currentPage.value - 1) * perPage + 1 : 0;
    const end = Math.min(currentPage.value * perPage, total);
    return `${start}-${end} out of ${total}`;
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
    refreshEmails();
};

const handleSelectAll = () => {
    // TODO: Implement select all
    console.log('Select all emails');
};

const toggleSearchBar = () => {
    showSearchBar.value = !showSearchBar.value;
    if (!showSearchBar.value) {
        clearSearch();
    }
};

const handleSearch = (event: Event) => {
    const target = event.target as HTMLInputElement;
    setSearchQuery(target.value);
};

const handleOpenLabelManager = () => {
    showLabelManager.value = true;
};

const handleCloseLabelManager = () => {
    showLabelManager.value = false;
};

const handleCreateLabel = (labelData: any) => {
    createLabel(labelData);
};

const handleUpdateLabel = (label: any) => {
    updateLabel(label);
};

const handleDeleteLabel = (labelId: string) => {
    deleteLabel(labelId);
};

const handleUpdateCredentials = async (data: Record<string, any>) => {
    if (!currentAccountId.value) return;
    const success = await updateCredentials(currentAccountId.value, data);
    if (success) {
        showCredentialsDialog.value = false;
    }
};
</script>

<template>
    <Head title="Mail" />

    <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <MailSidebar @open-label-manager="handleOpenLabelManager" />

            <!-- Main Content Area -->
            <div class="flex flex-1 flex-col overflow-hidden max-h-screen">
                <!-- Sync Status Banner (shown when syncing) -->
                <div
                    v-if="isSyncing"
                    class="border-b border-sidebar-border bg-primary/10 px-6 py-3"
                >
                    <div class="flex items-center gap-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 animate-spin text-primary"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                        <span class="text-sm font-medium text-primary">
                            {{ syncProgress }}
                        </span>
                    </div>
                </div>

                <!-- Sync Error Banner -->
                <div
                    v-if="hasSyncError"
                    class="border-b border-sidebar-border bg-destructive/10 px-6 py-3"
                >
                    <div class="flex items-center gap-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 shrink-0 text-destructive"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                        <span class="text-sm font-medium text-destructive">
                            {{ syncError || 'Sync failed. Please try again.' }}
                        </span>
                        <div class="ml-auto flex items-center gap-2">
                            <Button variant="outline" size="sm" class="text-destructive" @click="showCredentialsDialog = true">
                                Update Credentials
                            </Button>
                            <Button variant="ghost" size="sm" class="text-destructive" @click="handleRefresh">
                                Retry
                            </Button>
                        </div>
                    </div>
                </div>

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
                        <h1 class="text-lg font-semibold text-foreground">{{ currentFolder?.name || 'Inbox' }}</h1>
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

                        <Button variant="ghost" size="icon" @click="toggleSearchBar">
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

            <!-- Search Bar (conditionally shown) -->
            <div
                v-if="showSearchBar || isSearching"
                class="border-b border-sidebar-border px-6 py-3"
            >
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <Input
                            :value="searchQuery"
                            @input="handleSearch"
                            placeholder="Search emails..."
                            class="pl-10 pr-10"
                            autofocus
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>
                    <Button variant="ghost" size="sm" @click="toggleSearchBar">
                        Close
                    </Button>
                </div>
                <div v-if="isSearching" class="mt-2 text-sm text-muted-foreground">
                    Found {{ displayEmails.length }} result{{ displayEmails.length !== 1 ? 's' : '' }}
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
                        <Button variant="ghost" size="icon" :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">
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
                        <Button variant="ghost" size="icon" :disabled="currentPage >= totalPages" @click="goToPage(currentPage + 1)">
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
                <!-- Email List - 30% width -->
                <div
                    :class="[
                        'w-full overflow-y-auto border-r border-sidebar-border lg:w-[30%]',
                        selectedEmail && showEmailViewer ? 'hidden lg:block' : '',
                    ]"
                >
                    <EmailList
                        :emails="displayEmails"
                        :selected-email-id="selectedEmailId"
                        @select-email="handleSelectEmail"
                    />
                </div>

                <!-- Email Viewer (Thread or Single) - 70% width -->
                <div
                    v-if="selectedEmail"
                    :class="[
                        'w-full overflow-y-auto lg:w-[70%]',
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

    <!-- Label Manager -->
    <LabelManager
        :open="showLabelManager"
        :labels="labels"
        @close="handleCloseLabelManager"
        @create="handleCreateLabel"
        @update="handleUpdateLabel"
        @delete="handleDeleteLabel"
    />

    <!-- Update Credentials Dialog -->
    <UpdateCredentialsDialog
        :open="showCredentialsDialog"
        :account-id="currentAccountId || ''"
        :account-email="currentAccount?.email || ''"
        :sync-error="syncError"
        @close="showCredentialsDialog = false"
        @submit="handleUpdateCredentials"
    />
</template>
