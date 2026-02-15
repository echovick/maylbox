<script setup lang="ts">
import { ref, watch } from 'vue';
import { useCompose } from '@/composables/useCompose';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Badge } from '@/components/ui/badge';

const {
    isComposing,
    draft,
    hasContent,
    canSend,
    closeCompose,
    sendEmail,
    saveDraft,
    addRecipient,
    removeRecipient,
    addAttachment,
    removeAttachment,
} = useCompose();

// Local state
const toInput = ref('');
const ccInput = ref('');
const bccInput = ref('');
const showCc = ref(false);
const showBcc = ref(false);
const isSending = ref(false);

// File upload
const fileInput = ref<HTMLInputElement | null>(null);

const handleAddRecipient = (type: 'to' | 'cc' | 'bcc', value: string) => {
    const trimmed = value.trim();
    if (!trimmed) return;

    // Simple email validation
    if (!trimmed.includes('@')) return;

    const [localPart, domain] = trimmed.split('@');
    if (!domain) return;

    addRecipient(type, { email: trimmed });

    // Clear input
    if (type === 'to') toInput.value = '';
    if (type === 'cc') ccInput.value = '';
    if (type === 'bcc') bccInput.value = '';
};

const handleToKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
        e.preventDefault();
        handleAddRecipient('to', toInput.value);
    }
};

const handleCcKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
        e.preventDefault();
        handleAddRecipient('cc', ccInput.value);
    }
};

const handleBccKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
        e.preventDefault();
        handleAddRecipient('bcc', bccInput.value);
    }
};

const handleSend = async () => {
    if (!canSend.value) return;

    isSending.value = true;
    // Simulate sending delay
    await new Promise(resolve => setTimeout(resolve, 1000));
    sendEmail();
    isSending.value = false;
};

const handleSaveDraft = () => {
    saveDraft();
};

const handleClose = () => {
    if (hasContent.value) {
        // TODO: Show confirmation dialog
        if (confirm('Save draft before closing?')) {
            saveDraft();
        }
    }
    closeCompose();
};

const handleFileSelect = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const files = target.files;
    if (!files) return;

    Array.from(files).forEach(file => {
        addAttachment(file);
    });

    // Reset input
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const triggerFileSelect = () => {
    fileInput.value?.click();
};
</script>

<template>
    <Sheet :open="isComposing" @update:open="isComposing ? closeCompose() : null">
        <SheetContent
            side="right"
            class="w-full p-6 sm:max-w-2xl"
        >
            <SheetHeader class="border-b border-sidebar-border pb-4">
                <SheetTitle>
                    {{
                        draft.mode === 'reply'
                            ? 'Reply'
                            : draft.mode === 'replyAll'
                              ? 'Reply All'
                              : draft.mode === 'forward'
                                ? 'Forward'
                                : 'New Message'
                    }}
                </SheetTitle>
            </SheetHeader>

            <div class="flex h-[calc(100%-8rem)] flex-col gap-4 overflow-hidden pt-4">
                <!-- From -->
                <div v-if="draft.from" class="flex items-center gap-2">
                    <Label class="w-16 shrink-0 text-sm text-muted-foreground">
                        From
                    </Label>
                    <div class="text-sm text-foreground">
                        {{ draft.from.email }}
                    </div>
                </div>

                <!-- To -->
                <div class="flex items-start gap-2">
                    <Label class="w-16 shrink-0 pt-2 text-sm text-muted-foreground">
                        To
                    </Label>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-1">
                            <Badge
                                v-for="recipient in draft.to"
                                :key="recipient.email"
                                variant="secondary"
                                class="gap-1"
                            >
                                {{ recipient.name || recipient.email }}
                                <button
                                    type="button"
                                    class="ml-1 hover:text-destructive"
                                    @click="removeRecipient('to', recipient.email)"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </Badge>
                            <Input
                                v-model="toInput"
                                type="email"
                                placeholder="Add recipients..."
                                class="h-8 flex-1 border-none p-0 focus-visible:ring-0"
                                @keydown="handleToKeydown"
                                @blur="handleAddRecipient('to', toInput)"
                            />
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <Button
                            v-if="!showCc"
                            variant="ghost"
                            size="sm"
                            class="h-8 text-xs"
                            @click="showCc = true"
                        >
                            Cc
                        </Button>
                        <Button
                            v-if="!showBcc"
                            variant="ghost"
                            size="sm"
                            class="h-8 text-xs"
                            @click="showBcc = true"
                        >
                            Bcc
                        </Button>
                    </div>
                </div>

                <!-- Cc -->
                <div v-if="showCc" class="flex items-start gap-2">
                    <Label class="w-16 shrink-0 pt-2 text-sm text-muted-foreground">
                        Cc
                    </Label>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-1">
                            <Badge
                                v-for="recipient in draft.cc"
                                :key="recipient.email"
                                variant="secondary"
                                class="gap-1"
                            >
                                {{ recipient.name || recipient.email }}
                                <button
                                    type="button"
                                    class="ml-1 hover:text-destructive"
                                    @click="removeRecipient('cc', recipient.email)"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </Badge>
                            <Input
                                v-model="ccInput"
                                type="email"
                                placeholder="Add Cc recipients..."
                                class="h-8 flex-1 border-none p-0 focus-visible:ring-0"
                                @keydown="handleCcKeydown"
                                @blur="handleAddRecipient('cc', ccInput)"
                            />
                        </div>
                    </div>
                </div>

                <!-- Bcc -->
                <div v-if="showBcc" class="flex items-start gap-2">
                    <Label class="w-16 shrink-0 pt-2 text-sm text-muted-foreground">
                        Bcc
                    </Label>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-1">
                            <Badge
                                v-for="recipient in draft.bcc"
                                :key="recipient.email"
                                variant="secondary"
                                class="gap-1"
                            >
                                {{ recipient.name || recipient.email }}
                                <button
                                    type="button"
                                    class="ml-1 hover:text-destructive"
                                    @click="removeRecipient('bcc', recipient.email)"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </Badge>
                            <Input
                                v-model="bccInput"
                                type="email"
                                placeholder="Add Bcc recipients..."
                                class="h-8 flex-1 border-none p-0 focus-visible:ring-0"
                                @keydown="handleBccKeydown"
                                @blur="handleAddRecipient('bcc', bccInput)"
                            />
                        </div>
                    </div>
                </div>

                <!-- Subject -->
                <div class="flex items-center gap-2">
                    <Label class="w-16 shrink-0 text-sm text-muted-foreground">
                        Subject
                    </Label>
                    <Input
                        v-model="draft.subject"
                        type="text"
                        placeholder="Subject"
                        class="border-none p-0 focus-visible:ring-0"
                    />
                </div>

                <!-- Divider -->
                <div class="h-px bg-sidebar-border" />

                <!-- Body -->
                <div class="flex-1 overflow-y-auto">
                    <textarea
                        v-model="draft.bodyHtml"
                        placeholder="Compose your message..."
                        class="h-full w-full resize-none border-none bg-transparent p-0 text-sm text-foreground outline-none focus:ring-0"
                    />
                </div>

                <!-- Attachments -->
                <div
                    v-if="draft.attachments && draft.attachments.length > 0"
                    class="space-y-2"
                >
                    <div
                        v-for="attachment in draft.attachments"
                        :key="attachment.id"
                        class="flex items-center gap-2 rounded-lg border border-sidebar-border p-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-muted-foreground"
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
                        <span class="flex-1 truncate text-sm">
                            {{ attachment.filename }}
                        </span>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-6 w-6"
                            @click="removeAttachment(attachment.id)"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="mt-auto flex items-center justify-between border-t border-sidebar-border pt-4"
            >
                <div class="flex gap-1">
                    <Button
                        variant="ghost"
                        size="icon"
                        @click="triggerFileSelect"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"
                            />
                        </svg>
                    </Button>
                    <input
                        ref="fileInput"
                        type="file"
                        multiple
                        class="hidden"
                        @change="handleFileSelect"
                    />

                    <!-- Formatting buttons would go here -->
                </div>

                <div class="flex gap-2">
                    <Button variant="outline" @click="handleSaveDraft">
                        Save Draft
                    </Button>
                    <Button
                        :disabled="!canSend || isSending"
                        @click="handleSend"
                    >
                        <svg
                            v-if="!isSending"
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m22 2-7 20-4-9-9-4Z" />
                            <path d="M22 2 11 13" />
                        </svg>
                        <span v-if="isSending">Sending...</span>
                        <span v-else>Send</span>
                    </Button>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
