<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useEmailHelpers } from '@/composables/useEmailHelpers';
import type { Email } from '@/types/email';

const props = defineProps<{
    email: Email;
    isSelected?: boolean;
}>();

const emit = defineEmits<{
    select: [emailId: string];
}>();

const {
    formatRelativeDate,
    getInitials,
    getAvatarColor,
    truncate,
} = useEmailHelpers();

const avatarColor = computed(() => getAvatarColor(props.email.from.email));
const initials = computed(() => getInitials(props.email.from));

const handleClick = () => {
    emit('select', props.email.id);
};
</script>

<template>
    <div
        :class="[
            'group relative flex cursor-pointer items-start gap-2.5 border-b border-sidebar-border/50 px-3 py-2 transition-colors hover:bg-sidebar-accent/50',
            isSelected ? 'bg-sidebar-accent' : '',
            !email.isRead ? 'bg-background' : 'bg-muted/20',
        ]"
        @click="handleClick"
    >
        <!-- Unread Indicator -->
        <div
            v-if="!email.isRead"
            class="absolute left-0 top-0 h-full w-1 bg-primary"
        />

        <!-- Avatar -->
        <Avatar class="mt-0.5 h-8 w-8 shrink-0">
            <AvatarFallback :class="[avatarColor, 'text-white text-xs']">
                {{ initials }}
            </AvatarFallback>
        </Avatar>

        <!-- Email Content -->
        <div class="min-w-0 flex-1">
            <!-- Header: From and Time -->
            <div class="flex items-baseline justify-between gap-2">
                <span
                    :class="[
                        'truncate text-sm',
                        !email.isRead
                            ? 'font-semibold text-foreground'
                            : 'font-normal text-muted-foreground',
                    ]"
                >
                    {{ email.from.name || email.from.email }}
                </span>
                <span class="shrink-0 text-xs text-muted-foreground">
                    {{ formatRelativeDate(email.date) }}
                </span>
            </div>

            <!-- Subject -->
            <div
                :class="[
                    'truncate text-sm',
                    !email.isRead
                        ? 'font-medium text-foreground'
                        : 'text-muted-foreground',
                ]"
            >
                {{ email.subject }}
            </div>

            <!-- Snippet -->
            <div class="truncate text-xs text-muted-foreground/80">
                {{ truncate(email.snippet, 80) }}
            </div>

            <!-- Footer: Labels and Indicators -->
            <div class="flex items-center gap-2">
                <!-- Labels -->
                <div v-if="email.labels && email.labels.length > 0" class="flex gap-1">
                    <Badge
                        v-for="label in email.labels.slice(0, 3)"
                        :key="label.id"
                        variant="outline"
                        class="text-xs"
                        :style="{ borderColor: label.color }"
                    >
                        {{ label.name }}
                    </Badge>
                </div>

                <!-- Thread Indicator -->
                <div
                    v-if="email.threadId"
                    class="flex items-center gap-1 text-xs text-muted-foreground"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3.5 w-3.5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                    </svg>
                </div>

                <!-- Attachment Indicator -->
                <div
                    v-if="email.hasAttachments"
                    class="flex items-center gap-1 text-xs text-muted-foreground"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3.5 w-3.5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"
                        />
                    </svg>
                    <span>{{ email.attachments?.length }}</span>
                </div>

                <!-- Star Indicator -->
                <div
                    v-if="email.isStarred"
                    class="ml-auto text-yellow-500"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>
