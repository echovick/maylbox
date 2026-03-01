<script setup lang="ts">
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';
import { useEmails } from '@/composables/useEmails';
import { useCompose } from '@/composables/useCompose';
import { useInitials } from '@/composables/useInitials';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Separator } from '@/components/ui/separator';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';

const page = usePage();
const user = page.props.auth.user as { name: string; email: string; avatar?: string };
const { getInitials } = useInitials();

const handleLogout = () => {
    router.flushAll();
};

const {
    folders,
    accounts,
    labels,
    currentFolderId,
    currentAccountId,
    currentLabelId,
    setCurrentFolder,
    setCurrentAccount,
    setCurrentLabel,
} = useEmails();

const { openCompose } = useCompose();

// Get current account
const currentAccount = computed(() => {
    return accounts.value.find(acc => String(acc.id) === currentAccountId.value);
});

// Get folders for current account
const accountFolders = computed(() => {
    return folders.value.filter(f => f.accountId === currentAccountId.value);
});

// Folder icon mapping
const folderIcons: Record<string, string> = {
    inbox: 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z',
    sent: 'M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z',
    drafts: 'M12 19l7-7 3 3-7 7-3-3z M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z',
    trash: 'M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2',
    spam: 'M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z M12 9v4 M12 17h.01',
    archive: 'M21 8v13H3V8 M1 3h22v5H1z M10 12h4',
};

const emit = defineEmits<{
    openLabelManager: [];
}>();

const handleFolderClick = (folderId: string) => {
    setCurrentFolder(folderId);
    setCurrentLabel(null); // Clear label filter when switching folders
};

const handleLabelClick = (labelId: string) => {
    setCurrentLabel(labelId);
};

const handleAccountSwitch = (accountId: string | number) => {
    const id = String(accountId);
    setCurrentAccount(id);
};

const handleAddAccount = () => {
    router.visit('/account-setup');
};
</script>

<template>
    <div class="flex h-full w-64 flex-col border-r border-sidebar-border bg-background">
        <!-- Account Switcher -->
        <div class="border-b border-sidebar-border px-6 py-5">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button
                        variant="outline"
                        class="h-auto w-full justify-between py-3"
                    >
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-medium text-primary-foreground"
                            >
                                {{ currentAccount?.email.substring(0, 2).toUpperCase() }}
                            </div>
                            <div class="min-w-0 flex-1 text-left">
                                <div class="truncate text-sm font-semibold">
                                    {{ currentAccount?.name }}
                                </div>
                                <div class="truncate text-xs text-muted-foreground">
                                    {{ currentAccount?.email }}
                                </div>
                            </div>
                        </div>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start" class="w-64">
                    <DropdownMenuItem
                        v-for="account in accounts"
                        :key="account.id"
                        @click="handleAccountSwitch(account.id)"
                    >
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-medium text-primary-foreground"
                            >
                                {{ account.email.substring(0, 2).toUpperCase() }}
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium">{{ account.name }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ account.email }}
                                </div>
                            </div>
                        </div>
                    </DropdownMenuItem>
                    <Separator class="my-1" />
                    <DropdownMenuItem @click="handleAddAccount">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        Add another account
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

        <!-- Compose Button -->
        <div class="p-4">
            <Button class="w-full" @click="openCompose">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="mr-2 h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Compose
            </Button>
        </div>

        <!-- Folders -->
        <div class="flex-1 overflow-y-auto px-2">
            <nav class="space-y-1">
                <button
                    v-for="folder in accountFolders"
                    :key="folder.id"
                    :class="[
                        'flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors',
                        currentFolderId === folder.id
                            ? 'bg-sidebar-accent font-medium text-sidebar-accent-foreground'
                            : 'text-muted-foreground hover:bg-sidebar-accent/50 hover:text-foreground',
                    ]"
                    @click="handleFolderClick(folder.id)"
                >
                    <div class="flex items-center gap-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path :d="folderIcons[folder.type] || folderIcons.inbox" />
                        </svg>
                        <span>{{ folder.name }}</span>
                    </div>
                    <Badge
                        v-if="folder.unreadCount > 0"
                        variant="secondary"
                        class="ml-auto"
                    >
                        {{ folder.unreadCount }}
                    </Badge>
                </button>
            </nav>

            <!-- Labels Section -->
            <div class="mt-6">
                <div class="mb-2 flex items-center justify-between px-3">
                    <span class="text-xs font-semibold text-muted-foreground">LABELS</span>
                    <button
                        @click="emit('openLabelManager')"
                        class="text-xs text-muted-foreground hover:text-foreground"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </button>
                </div>
                <nav v-if="labels.length > 0" class="space-y-1">
                    <button
                        v-for="label in labels"
                        :key="label.id"
                        :class="[
                            'flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors',
                            currentLabelId === label.id
                                ? 'bg-sidebar-accent font-medium text-sidebar-accent-foreground'
                                : 'text-muted-foreground hover:bg-sidebar-accent/50 hover:text-foreground',
                        ]"
                        @click="handleLabelClick(label.id)"
                    >
                        <div
                            class="h-3 w-3 rounded-full"
                            :style="{ backgroundColor: label.color }"
                        />
                        <span>{{ label.name }}</span>
                    </button>
                </nav>
                <div
                    v-else
                    class="px-3 py-2 text-xs text-muted-foreground"
                >
                    No labels yet
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="border-t border-sidebar-border p-3">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button class="flex w-full items-center gap-3 rounded-lg px-2 py-2 text-sm transition-colors hover:bg-sidebar-accent/50">
                        <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
                            <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                            <AvatarFallback class="rounded-lg text-black dark:text-white">
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <span class="flex-1 truncate text-left font-medium">{{ user.name }}</span>
                        <Settings class="h-4 w-4 text-muted-foreground" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start" class="w-56">
                    <DropdownMenuLabel class="p-0 font-normal">
                        <div class="flex items-center gap-2 px-2 py-1.5 text-left text-sm">
                            <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
                                <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                                <AvatarFallback class="rounded-lg text-black dark:text-white">
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-medium">{{ user.name }}</span>
                                <span class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
                            </div>
                        </div>
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem :as-child="true">
                        <Link class="block w-full cursor-pointer" :href="edit()" prefetch>
                            <Settings class="mr-2 h-4 w-4" />
                            Settings
                        </Link>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem :as-child="true">
                        <Link
                            class="block w-full cursor-pointer"
                            :href="logout()"
                            @click="handleLogout"
                            as="button"
                        >
                            <LogOut class="mr-2 h-4 w-4" />
                            Log out
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
