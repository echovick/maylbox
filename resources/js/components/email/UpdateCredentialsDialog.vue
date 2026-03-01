<script setup lang="ts">
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    open: boolean;
    accountId: string;
    accountEmail: string;
    syncError: string | null;
}>();

const emit = defineEmits<{
    close: [];
    submit: [data: Record<string, any>];
}>();

const password = ref('');
const imapHost = ref('');
const imapPort = ref('');
const showAdvanced = ref(false);
const isSubmitting = ref(false);
const error = ref('');

watch(() => props.open, (val) => {
    if (val) {
        password.value = '';
        imapHost.value = '';
        imapPort.value = '';
        showAdvanced.value = false;
        isSubmitting.value = false;
        error.value = '';
    }
});

const handleSubmit = () => {
    if (!password.value.trim()) {
        error.value = 'Password is required.';
        return;
    }

    error.value = '';
    isSubmitting.value = true;

    const data: Record<string, any> = {
        imap_password: password.value,
        smtp_password: password.value,
    };

    if (imapHost.value.trim()) data.imap_host = imapHost.value.trim();
    if (imapPort.value.trim()) data.imap_port = parseInt(imapPort.value.trim());

    emit('submit', data);
};
</script>

<template>
    <Dialog :open="open" @update:open="(val: boolean) => { if (!val) emit('close'); }">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Update Email Credentials</DialogTitle>
                <DialogDescription>
                    Update the password for <strong>{{ accountEmail }}</strong> to fix the connection.
                </DialogDescription>
            </DialogHeader>

            <div v-if="syncError" class="rounded-md bg-destructive/10 px-3 py-2 text-sm text-destructive">
                {{ syncError }}
            </div>

            <form class="space-y-4" @submit.prevent="handleSubmit">
                <div class="space-y-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        v-model="password"
                        type="password"
                        placeholder="Enter your email password"
                        autofocus
                    />
                    <p class="text-xs text-muted-foreground">
                        If your email provider uses app passwords (e.g. Gmail, Yahoo), use an app-specific password.
                    </p>
                </div>

                <button
                    type="button"
                    class="text-xs text-muted-foreground underline hover:text-foreground"
                    @click="showAdvanced = !showAdvanced"
                >
                    {{ showAdvanced ? 'Hide' : 'Show' }} advanced settings
                </button>

                <div v-if="showAdvanced" class="space-y-3">
                    <div class="space-y-2">
                        <Label for="imapHost">IMAP Host</Label>
                        <Input
                            id="imapHost"
                            v-model="imapHost"
                            placeholder="Leave blank to keep current"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="imapPort">IMAP Port</Label>
                        <Input
                            id="imapPort"
                            v-model="imapPort"
                            type="number"
                            placeholder="Leave blank to keep current"
                        />
                    </div>
                </div>

                <div v-if="error" class="text-sm text-destructive">
                    {{ error }}
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="emit('close')">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Saving...' : 'Save & Reconnect' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
