<script setup lang="ts">
import { ref } from 'vue';
import HtmlEmailBody from '@/components/email/HtmlEmailBody.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useCompose } from '@/composables/useCompose';
import { useEmailHelpers } from '@/composables/useEmailHelpers';
import type { EmailThread } from '@/types/email';

const props = defineProps<{
    thread: EmailThread;
}>();

const emit = defineEmits<{
    close: [];
    delete: [emailId: string];
    toggleStar: [emailId: string];
}>();

const {
    formatEmailDate,
    formatFileSize,
    getInitials,
    getAvatarColor,
    formatRecipients,
} = useEmailHelpers();

const { openReply, openForward } = useCompose();

// Track which messages are expanded (all except last are collapsed by default)
const expandedMessages = ref<Set<string>>(
    new Set([props.thread.emails[props.thread.emails.length - 1].id]),
);

const toggleMessage = (emailId: string) => {
    if (expandedMessages.value.has(emailId)) {
        expandedMessages.value.delete(emailId);
    } else {
        expandedMessages.value.add(emailId);
    }
};

const isExpanded = (emailId: string) => {
    return expandedMessages.value.has(emailId);
};

const handleReply = () => {
    const latestEmail = props.thread.emails[props.thread.emails.length - 1];
    openReply(latestEmail);
};

const handleReplyAll = () => {
    const latestEmail = props.thread.emails[props.thread.emails.length - 1];
    openReply(latestEmail, true);
};

const handleForward = () => {
    const latestEmail = props.thread.emails[props.thread.emails.length - 1];
    openForward(latestEmail);
};
</script>

<template>
    <div class="flex h-full flex-col overflow-hidden bg-background">
        <!-- Header -->
        <div class="border-b border-sidebar-border px-4 py-3">
            <div class="mb-2 flex items-center justify-between">
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
                        :class="thread.emails.some(e => e.isStarred) ? 'text-yellow-500' : ''"
                        @click="emit('toggleStar', thread.emails[thread.emails.length - 1].id)"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            :fill="thread.emails.some(e => e.isStarred) ? 'currentColor' : 'none'"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </Button>

                    <Button
                        variant="ghost"
                        size="icon"
                        @click="emit('delete', thread.emails[thread.emails.length - 1].id)"
                    >
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

            <!-- Subject and participant count -->
            <h1 class="mb-1 text-lg font-semibold text-foreground">
                {{ thread.subject }}
            </h1>
            <p class="text-xs text-muted-foreground">
                {{ thread.messageCount }} {{ thread.messageCount === 1 ? 'message' : 'messages' }}
                • {{ formatRecipients(thread.participants) }}
            </p>
        </div>

        <!-- Thread Messages -->
        <div class="flex-1 space-y-2 overflow-y-auto px-4 py-3">
            <div
                v-for="email in thread.emails"
                :key="email.id"
                class="rounded-md border border-sidebar-border"
            >
                <Collapsible :open="isExpanded(email.id)">
                    <!-- Message Header (Always Visible) -->
                    <CollapsibleTrigger
                        as-child
                        @click="toggleMessage(email.id)"
                    >
                        <button
                            class="flex w-full items-center gap-2.5 px-3 py-2 text-left transition-colors hover:bg-sidebar-accent/50"
                        >
                            <Avatar class="h-7 w-7 shrink-0">
                                <AvatarFallback
                                    :class="[
                                        getAvatarColor(email.from.email),
                                        'text-white text-xs',
                                    ]"
                                >
                                    {{ getInitials(email.from) }}
                                </AvatarFallback>
                            </Avatar>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-2">
                                    <div class="flex items-baseline gap-2 overflow-hidden">
                                        <span class="text-sm font-medium text-foreground">
                                            {{ email.from.name || email.from.email }}
                                        </span>
                                        <span
                                            v-if="!isExpanded(email.id)"
                                            class="truncate text-xs text-muted-foreground"
                                        >
                                            {{ email.snippet.substring(0, 60) }}...
                                        </span>
                                    </div>
                                    <span class="shrink-0 text-[11px] text-muted-foreground">
                                        {{ formatEmailDate(email.date) }}
                                    </span>
                                </div>

                                <div
                                    v-if="isExpanded(email.id)"
                                    class="mt-1 text-xs text-muted-foreground"
                                >
                                    <span class="mr-1">To:</span>
                                    {{ formatRecipients(email.to) }}
                                    <span v-if="email.cc && email.cc.length > 0" class="ml-2">
                                        <span class="mr-1">Cc:</span>
                                        {{ formatRecipients(email.cc) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Expand/Collapse Icon -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 shrink-0 text-muted-foreground transition-transform"
                                :class="isExpanded(email.id) ? 'rotate-180' : ''"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                    </CollapsibleTrigger>

                    <!-- Message Body (Collapsible) -->
                    <CollapsibleContent>
                        <div class="border-t border-sidebar-border px-3 pb-3 pt-3">
                            <!-- Loading body -->
                            <div v-if="!email.bodyHtml && !email.bodyText" class="flex items-center justify-center py-4">
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

                            <template v-else>
                                <!-- Labels -->
                                <div
                                    v-if="email.labels && email.labels.length > 0"
                                    class="mb-3 flex gap-2"
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

                                <!-- Email Body -->
                                <HtmlEmailBody v-if="email.bodyHtml" :html="email.bodyHtml" />
                                <div
                                    v-else
                                    class="whitespace-pre-wrap text-sm text-foreground"
                                >
                                    {{ email.bodyText }}
                                </div>

                                <!-- Attachments -->
                            <div
                                v-if="email.attachments && email.attachments.length > 0"
                                class="mt-4"
                            >
                                <h4 class="mb-2 text-sm font-medium text-foreground">
                                    Attachments ({{ email.attachments.length }})
                                </h4>
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
                                            <div class="truncate text-sm font-medium text-foreground">
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
                            </template>
                        </div>
                    </CollapsibleContent>
                </Collapsible>
            </div>
        </div>

        <!-- Action Footer -->
        <div class="border-t border-sidebar-border px-4 py-3">
            <div class="flex gap-2">
                <Button size="sm" @click="handleReply">
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
                <Button variant="outline" size="sm" @click="handleReplyAll">
                    Reply All
                </Button>
                <Button variant="outline" size="sm" @click="handleForward">
                    Forward
                </Button>
            </div>
        </div>
    </div>
</template>
