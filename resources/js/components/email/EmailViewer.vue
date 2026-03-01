<script setup lang="ts">
import { computed, onMounted, watch, ref } from 'vue';
import type { Email } from '@/types/email';
import { useEmailHelpers } from '@/composables/useEmailHelpers';
import { useCompose } from '@/composables/useCompose';
import HtmlEmailBody from '@/components/email/HtmlEmailBody.vue';
import { useEmails } from '@/composables/useEmails';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

const props = defineProps<{
    email: Email;
}>();

const { fetchEmailDetail } = useEmails();
const loadingBody = ref(false);

async function ensureBodyLoaded() {
    if (!props.email.bodyHtml && !props.email.bodyText) {
        loadingBody.value = true;
        await fetchEmailDetail(props.email.id);
        loadingBody.value = false;
    }
}

onMounted(ensureBodyLoaded);
watch(() => props.email.id, ensureBodyLoaded);

const emit = defineEmits<{
    close: [];
    delete: [emailId: string];
    toggleStar: [emailId: string];
    moveToFolder: [emailId: string, folderId: string];
}>();

const {
    formatEmailDate,
    formatFileSize,
    getInitials,
    getAvatarColor,
    formatRecipients,
} = useEmailHelpers();

const { openReply, openForward } = useCompose();

const avatarColor = computed(() => getAvatarColor(props.email.from.email));
const initials = computed(() => getInitials(props.email.from));

const handleReply = () => {
    openReply(props.email);
};

const handleReplyAll = () => {
    openReply(props.email, true);
};

const handleForward = () => {
    openForward(props.email);
};

const handleDelete = () => {
    emit('delete', props.email.id);
};

const handleToggleStar = () => {
    emit('toggleStar', props.email.id);
};

</script>

<template>
    <div class="flex h-full flex-col overflow-hidden bg-background">
        <!-- Header -->
        <div class="border-b border-sidebar-border px-6 py-4">
            <div class="mb-4 flex items-center justify-between">
                <!-- Back button (mobile) -->
                <Button
                    variant="ghost"
                    size="icon"
                    class="md:hidden"
                    @click="emit('close')"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M19 12H5M12 19l-7-7 7-7" />
                    </svg>
                </Button>

                <!-- Action Buttons -->
                <div class="flex items-center gap-1">
                    <Button
                        variant="ghost"
                        size="icon"
                        :class="email.isStarred ? 'text-yellow-500' : ''"
                        @click="handleToggleStar"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            :fill="email.isStarred ? 'currentColor' : 'none'"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                            />
                        </svg>
                    </Button>

                    <Button variant="ghost" size="icon" @click="handleDelete">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                        </svg>
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="1" />
                                    <circle cx="12" cy="5" r="1" />
                                    <circle cx="12" cy="19" r="1" />
                                </svg>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem>Mark as unread</DropdownMenuItem>
                            <DropdownMenuItem>Move to folder</DropdownMenuItem>
                            <DropdownMenuItem>Add label</DropdownMenuItem>
                            <DropdownMenuItem>Print</DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <!-- Subject -->
            <h1 class="mb-4 text-2xl font-semibold text-foreground">
                {{ email.subject }}
            </h1>

            <!-- Sender Info -->
            <div class="flex items-start gap-3">
                <Avatar class="h-10 w-10">
                    <AvatarFallback :class="[avatarColor, 'text-white']">
                        {{ initials }}
                    </AvatarFallback>
                </Avatar>

                <div class="flex-1">
                    <div class="flex items-baseline justify-between">
                        <div>
                            <div class="text-sm font-medium text-foreground">
                                {{ email.from.name || email.from.email }}
                            </div>
                            <div class="text-xs text-muted-foreground">
                                {{ email.from.email }}
                            </div>
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ formatEmailDate(email.date) }}
                        </div>
                    </div>

                    <!-- Recipients -->
                    <div class="mt-2 text-xs text-muted-foreground">
                        <span class="mr-1">To:</span>
                        {{ formatRecipients(email.to) }}
                        <span v-if="email.cc && email.cc.length > 0" class="ml-2">
                            <span class="mr-1">Cc:</span>
                            {{ formatRecipients(email.cc) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Labels -->
            <div
                v-if="email.labels && email.labels.length > 0"
                class="mt-3 flex gap-2"
            >
                <Badge
                    v-for="label in email.labels"
                    :key="label.id"
                    variant="outline"
                    class="text-xs"
                    :style="{ borderColor: label.color }"
                >
                    {{ label.name }}
                </Badge>
            </div>
        </div>

        <!-- Email Body -->
        <div class="flex-1 overflow-y-auto px-6 py-6">
            <!-- Loading body -->
            <div v-if="loadingBody" class="flex items-center justify-center py-8">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 animate-spin text-muted-foreground"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                </svg>
            </div>
            <!-- Body Content -->
            <HtmlEmailBody v-if="!loadingBody && email.bodyHtml" :html="email.bodyHtml" />
            <div v-else-if="!loadingBody" class="whitespace-pre-wrap text-sm text-foreground">
                {{ email.bodyText }}
            </div>

            <!-- Attachments -->
            <div
                v-if="email.attachments && email.attachments.length > 0"
                class="mt-6"
            >
                <h3 class="mb-3 text-sm font-medium text-foreground">
                    Attachments ({{ email.attachments.length }})
                </h3>
                <div class="grid gap-2 sm:grid-cols-2">
                    <div
                        v-for="attachment in email.attachments"
                        :key="attachment.id"
                        class="flex items-center gap-3 rounded-lg border border-sidebar-border p-3 transition-colors hover:bg-sidebar-accent/50"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded bg-muted"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-muted-foreground"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                />
                                <path d="M14 2v6h6" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div
                                class="truncate text-sm font-medium text-foreground"
                            >
                                {{ attachment.filename }}
                            </div>
                            <div class="text-xs text-muted-foreground">
                                {{ formatFileSize(attachment.size) }}
                            </div>
                        </div>
                        <Button variant="ghost" size="icon" class="shrink-0">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" x2="12" y1="15" y2="3" />
                            </svg>
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Footer -->
        <div class="border-t border-sidebar-border px-6 py-4">
            <div class="flex gap-2">
                <Button @click="handleReply">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mr-2 h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M9 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-5l-5 5z"
                        />
                    </svg>
                    Reply
                </Button>
                <Button variant="outline" @click="handleReplyAll">
                    Reply All
                </Button>
                <Button variant="outline" @click="handleForward">
                    Forward
                </Button>
            </div>
        </div>
    </div>
</template>
