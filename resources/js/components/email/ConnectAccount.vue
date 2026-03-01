<script setup lang="ts">
import { ArrowLeft, Mail } from 'lucide-vue-next';
import { ref, reactive } from 'vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

const props = defineProps<{
    prefillEmail?: string;
}>();

const emit = defineEmits<{
    close: [];
    connect: [accountData: any];
}>();

type Step = 'select-provider' | 'oauth-provider' | 'imap-provider';

const step = ref<Step>('select-provider');
const selectedProvider = ref<string | null>(null);
const isConnecting = ref(false);
const error = ref<string | null>(null);

const imapForm = reactive({
    email: '',
    password: '',
    imapHost: '',
    imapPort: 993,
    smtpHost: '',
    smtpPort: 587,
});

const providers = [
    {
        id: 'gmail',
        name: 'Gmail',
        type: 'oauth' as const,
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z', fill: '#4285F4' },
            { d: 'M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z', fill: '#34A853' },
            { d: 'M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z', fill: '#FBBC05' },
            { d: 'M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z', fill: '#EA4335' },
        ],
    },
    {
        id: 'outlook',
        name: 'Outlook',
        type: 'oauth' as const,
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M11.4 24H0V12.6h11.4V24z', fill: '#F25022' },
            { d: 'M24 24H12.6V12.6H24V24z', fill: '#00A4EF' },
            { d: 'M11.4 11.4H0V0h11.4v11.4z', fill: '#7FBA00' },
            { d: 'M24 11.4H12.6V0H24v11.4z', fill: '#FFB900' },
        ],
    },
    {
        id: 'yahoo',
        name: 'Yahoo',
        type: 'imap' as const,
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M14.54 8.83l3.32-7.6h-3.06l-1.98 4.87L10.9 1.23H7.7l3.44 7.6-.1 2.66v4.28h3.4v-4.28l.1-2.66z', fill: '#6001D2' },
            { d: 'M20.23 1.23h-2.96l-1.5 3.6 1.68 3.89 2.78-7.49zM5.07 18.77a2.12 2.12 0 100 4.24 2.12 2.12 0 000-4.24z', fill: '#6001D2' },
        ],
        imapDefaults: { host: 'imap.mail.yahoo.com', port: 993 },
        smtpDefaults: { host: 'smtp.mail.yahoo.com', port: 587 },
        passwordLabel: 'App Password',
    },
    {
        id: 'icloud',
        name: 'iCloud',
        type: 'imap' as const,
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M19.35 10.04A7.49 7.49 0 0012 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 000 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z', fill: '#A3AAAE' },
        ],
        imapDefaults: { host: 'imap.mail.me.com', port: 993 },
        smtpDefaults: { host: 'smtp.mail.me.com', port: 587 },
        passwordLabel: 'App-Specific Password',
    },
];

const selectedProviderData = () => providers.find((p) => p.id === selectedProvider.value);

const selectProvider = (providerId: string) => {
    const provider = providers.find((p) => p.id === providerId);
    if (!provider) return;

    selectedProvider.value = providerId;
    error.value = null;

    if (provider.type === 'oauth') {
        step.value = 'oauth-provider';
    } else {
        // Pre-fill IMAP defaults
        imapForm.email = props.prefillEmail || '';
        imapForm.password = '';
        imapForm.imapHost = provider.imapDefaults?.host || '';
        imapForm.imapPort = provider.imapDefaults?.port || 993;
        imapForm.smtpHost = provider.smtpDefaults?.host || '';
        imapForm.smtpPort = provider.smtpDefaults?.port || 587;
        step.value = 'imap-provider';
    }
};

const selectOther = () => {
    selectedProvider.value = 'custom';
    error.value = null;
    imapForm.email = props.prefillEmail || '';
    imapForm.password = '';
    imapForm.imapHost = '';
    imapForm.imapPort = 993;
    imapForm.smtpHost = '';
    imapForm.smtpPort = 587;
    step.value = 'imap-provider';
};

const goBack = () => {
    step.value = 'select-provider';
    selectedProvider.value = null;
    error.value = null;
};

const connectOAuth = () => {
    if (!selectedProvider.value) return;

    isConnecting.value = true;
    error.value = null;

    const providerMap: Record<string, string> = {
        gmail: 'google',
        outlook: 'microsoft',
    };
    const driver = providerMap[selectedProvider.value];
    if (!driver) return;

    window.location.href = `/email-accounts/oauth/${driver}/redirect`;
};

const connectImap = () => {
    error.value = null;

    if (!imapForm.email || !imapForm.password) {
        error.value = 'Email and password are required';
        return;
    }
    if (!imapForm.imapHost || !imapForm.smtpHost) {
        error.value = 'IMAP and SMTP host are required';
        return;
    }

    isConnecting.value = true;

    const accountData = {
        type: 'imap',
        provider: selectedProvider.value,
        email: imapForm.email,
        name: imapForm.email,
        imap_host: imapForm.imapHost,
        imap_port: imapForm.imapPort,
        imap_encryption: 'ssl',
        imap_password: imapForm.password,
        smtp_host: imapForm.smtpHost,
        smtp_port: imapForm.smtpPort,
        smtp_encryption: 'tls',
        smtp_password: imapForm.password,
    };

    emit('connect', accountData);
};

const passwordLabel = () => {
    const provider = providers.find((p) => p.id === selectedProvider.value);
    return (provider as any)?.passwordLabel || 'Password';
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-muted/30 p-4">
        <Card class="w-full max-w-md">
            <CardHeader class="text-center">
                <CardTitle class="text-2xl">Connect your email</CardTitle>
                <CardDescription>
                    Choose your email provider to get started
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <Alert v-if="error" variant="destructive">
                    <AlertDescription>{{ error }}</AlertDescription>
                </Alert>

                <!-- Step 1: Provider selection grid -->
                <template v-if="step === 'select-provider'">
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            v-for="provider in providers"
                            :key="provider.id"
                            class="flex flex-col items-center gap-3 rounded-lg border p-5 transition-colors hover:bg-muted/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            @click="selectProvider(provider.id)"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :viewBox="provider.viewBox"
                                class="h-10 w-10"
                            >
                                <path
                                    v-for="(path, index) in provider.paths"
                                    :key="index"
                                    :d="path.d"
                                    :fill="path.fill"
                                />
                            </svg>
                            <span class="text-sm font-medium">{{ provider.name }}</span>
                        </button>
                    </div>

                    <!-- Other / IMAP -->
                    <button
                        class="flex w-full flex-col items-center gap-3 rounded-lg border p-5 transition-colors hover:bg-muted/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        @click="selectOther"
                    >
                        <Mail class="h-10 w-10 text-muted-foreground" />
                        <span class="text-sm font-medium">Other (IMAP/SMTP)</span>
                    </button>
                </template>

                <!-- Step 2a: OAuth provider -->
                <template v-if="step === 'oauth-provider'">
                    <button
                        class="flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
                        @click="goBack"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Back to providers
                    </button>

                    <div class="flex flex-col items-center gap-6 py-4">
                        <svg
                            v-if="selectedProviderData()"
                            xmlns="http://www.w3.org/2000/svg"
                            :viewBox="selectedProviderData()!.viewBox"
                            class="h-16 w-16"
                        >
                            <path
                                v-for="(path, index) in selectedProviderData()!.paths"
                                :key="index"
                                :d="path.d"
                                :fill="path.fill"
                            />
                        </svg>

                        <Button
                            size="lg"
                            class="w-full"
                            :disabled="isConnecting"
                            @click="connectOAuth"
                        >
                            <Spinner v-if="isConnecting" class="mr-2" />
                            Sign in with {{ selectedProviderData()?.name }}
                        </Button>
                    </div>
                </template>

                <!-- Step 2b: IMAP provider -->
                <template v-if="step === 'imap-provider'">
                    <button
                        class="flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
                        @click="goBack"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Back to providers
                    </button>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="imap-email">Email</Label>
                            <Input
                                id="imap-email"
                                v-model="imapForm.email"
                                type="email"
                                placeholder="you@yourdomain.com"
                                :disabled="isConnecting"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="imap-password">{{ passwordLabel() }}</Label>
                            <Input
                                id="imap-password"
                                v-model="imapForm.password"
                                type="password"
                                :placeholder="`Your ${passwordLabel().toLowerCase()}`"
                                :disabled="isConnecting"
                            />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="imapHost" class="text-xs">IMAP Host</Label>
                                <Input
                                    id="imapHost"
                                    v-model="imapForm.imapHost"
                                    placeholder="imap.example.com"
                                    :disabled="isConnecting"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="imapPort" class="text-xs">IMAP Port</Label>
                                <Input
                                    id="imapPort"
                                    v-model.number="imapForm.imapPort"
                                    type="number"
                                    placeholder="993"
                                    :disabled="isConnecting"
                                />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="smtpHost" class="text-xs">SMTP Host</Label>
                                <Input
                                    id="smtpHost"
                                    v-model="imapForm.smtpHost"
                                    placeholder="smtp.example.com"
                                    :disabled="isConnecting"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="smtpPort" class="text-xs">SMTP Port</Label>
                                <Input
                                    id="smtpPort"
                                    v-model.number="imapForm.smtpPort"
                                    type="number"
                                    placeholder="587"
                                    :disabled="isConnecting"
                                />
                            </div>
                        </div>

                        <Button
                            class="w-full"
                            size="lg"
                            :disabled="isConnecting || !imapForm.email || !imapForm.password"
                            @click="connectImap"
                        >
                            <Spinner v-if="isConnecting" class="mr-2" />
                            Connect Account
                        </Button>
                    </div>
                </template>

                <p class="text-center text-xs text-muted-foreground">
                    By connecting, you agree to Maylbox's access to your email for syncing and sending.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
