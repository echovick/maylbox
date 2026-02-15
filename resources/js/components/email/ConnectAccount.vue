<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Separator } from '@/components/ui/separator';

const props = defineProps<{
    prefillEmail?: string;
}>();

const emit = defineEmits<{
    close: [];
    connect: [accountData: any];
}>();

// State
const email = ref('');
const password = ref('');
const isConnecting = ref(false);
const error = ref<string | null>(null);
const showAdvancedSettings = ref(false);

// OAuth providers
const oauthProviders = [
    {
        id: 'gmail',
        name: 'Gmail',
        icon: 'M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z',
        color: 'bg-red-500',
    },
    {
        id: 'outlook',
        name: 'Outlook',
        icon: 'M24 7.387v9.226c0 1.001-.772 1.772-1.772 1.772H12v-6.42l6.42-3.21zm0-3.193v.966l-6.42 3.209-5.193-2.578 4.516-2.258c1.16-.58 2.902-.29 3.676.58zM10.323 1.29L1.772 5.226A1.772 1.772 0 0 0 0 6.998v10.387c0 .966.772 1.772 1.772 1.772h8.551zm1.677 7.742v9.226h10.228c.58 0 1.159-.29 1.545-.773l-6.42-3.21z',
        color: 'bg-blue-500',
    },
];

// IMAP settings (for advanced mode)
const imapSettings = ref({
    imapHost: '',
    imapPort: 993,
    smtpHost: '',
    smtpPort: 587,
});

// Detect provider from email
const detectedProvider = computed(() => {
    const emailLower = email.value.toLowerCase();
    if (emailLower.includes('@gmail.com')) return 'gmail';
    if (emailLower.includes('@outlook.com') || emailLower.includes('@hotmail.com')) return 'outlook';
    if (emailLower.includes('@yahoo.com')) return 'yahoo';
    return null;
});

// Show OAuth suggestion
const suggestOAuth = computed(() => {
    return detectedProvider.value === 'gmail' || detectedProvider.value === 'outlook';
});

// Auto-detect IMAP settings based on email domain
const detectImapSettings = () => {
    const emailLower = email.value.toLowerCase();

    if (emailLower.includes('@gmail.com')) {
        imapSettings.value.imapHost = 'imap.gmail.com';
        imapSettings.value.imapPort = 993;
        imapSettings.value.smtpHost = 'smtp.gmail.com';
        imapSettings.value.smtpPort = 587;
    } else if (emailLower.includes('@outlook.com') || emailLower.includes('@hotmail.com')) {
        imapSettings.value.imapHost = 'outlook.office365.com';
        imapSettings.value.imapPort = 993;
        imapSettings.value.smtpHost = 'smtp.office365.com';
        imapSettings.value.smtpPort = 587;
    } else if (emailLower.includes('@yahoo.com')) {
        imapSettings.value.imapHost = 'imap.mail.yahoo.com';
        imapSettings.value.imapPort = 993;
        imapSettings.value.smtpHost = 'smtp.mail.yahoo.com';
        imapSettings.value.smtpPort = 587;
    }
};

// Pre-fill email if provided
onMounted(() => {
    if (props.prefillEmail) {
        email.value = props.prefillEmail;
        detectImapSettings();
    }
});

// OAuth connection
const connectOAuth = (providerId: string) => {
    isConnecting.value = true;
    error.value = null;

    // TODO: Implement actual OAuth flow
    // For now, simulate OAuth flow
    setTimeout(() => {
        const accountData = {
            type: 'oauth',
            provider: providerId,
            email: props.prefillEmail || `user@${providerId}.com`,
            name: `My ${providerId.charAt(0).toUpperCase() + providerId.slice(1)} Account`,
            // OAuth tokens would be returned from OAuth callback
            access_token: 'oauth_access_token_here',
            refresh_token: 'oauth_refresh_token_here',
        };

        emit('connect', accountData);
        isConnecting.value = false;
    }, 1500);
};

// IMAP connection
const connectImap = () => {
    error.value = null;

    // Validate form
    if (!email.value || !password.value) {
        error.value = 'Email and password are required';
        return;
    }

    // Auto-detect settings if not already done
    if (!imapSettings.value.imapHost) {
        detectImapSettings();
    }

    // Check if settings were detected
    if (!imapSettings.value.imapHost || !imapSettings.value.smtpHost) {
        error.value = 'Could not auto-detect email settings. Please use advanced settings.';
        showAdvancedSettings.value = true;
        return;
    }

    isConnecting.value = true;

    // Prepare account data
    const accountData = {
        type: 'imap',
        email: email.value,
        name: email.value,
        imap_host: imapSettings.value.imapHost,
        imap_port: imapSettings.value.imapPort,
        imap_encryption: 'ssl',
        imap_password: password.value,
        smtp_host: imapSettings.value.smtpHost,
        smtp_port: imapSettings.value.smtpPort,
        smtp_encryption: 'tls',
        smtp_password: password.value,
    };

    // Emit to parent component which will handle the API call
    emit('connect', accountData);
    isConnecting.value = false;
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-muted/30 p-4">
        <Card class="w-full max-w-2xl">
            <CardHeader>
                <CardTitle class="text-2xl">Connect Your Email Account</CardTitle>
                <CardDescription>
                    Get started by connecting your email account. Choose OAuth for quick setup or
                    IMAP for custom configurations.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <!-- Tab Switcher -->
                <div class="mb-6 grid w-full grid-cols-2 gap-2 rounded-lg bg-muted p-1">
                    <Button
                        :variant="activeTab === 'oauth' ? 'default' : 'ghost'"
                        @click="activeTab = 'oauth'"
                    >
                        OAuth (Recommended)
                    </Button>
                    <Button
                        :variant="activeTab === 'imap' ? 'default' : 'ghost'"
                        @click="activeTab = 'imap'"
                    >
                        IMAP/SMTP
                    </Button>
                </div>

                <!-- OAuth Tab -->
                <div v-if="activeTab === 'oauth'" class="space-y-4">
                        <Alert v-if="error" variant="destructive">
                            <AlertDescription>{{ error }}</AlertDescription>
                        </Alert>

                        <p class="text-sm text-muted-foreground">
                            Connect with one click. Maylbox uses OAuth 2.0 for secure authentication
                            without storing your password.
                        </p>

                        <div class="grid gap-4">
                            <Button
                                v-for="provider in oauthProviders"
                                :key="provider.id"
                                variant="outline"
                                size="lg"
                                class="h-auto py-4"
                                :disabled="isConnecting"
                                @click="connectOAuth(provider.id)"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="[
                                            provider.color,
                                            'flex h-10 w-10 items-center justify-center rounded',
                                        ]"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="white"
                                            class="h-6 w-6"
                                        >
                                            <path :d="provider.icon" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-medium">Continue with {{ provider.name }}</div>
                                        <div class="text-xs text-muted-foreground">
                                            Quick and secure setup
                                        </div>
                                    </div>
                                </div>
                            </Button>
                        </div>

                        <p class="text-center text-xs text-muted-foreground">
                            By connecting, you agree to Maylbox's access to your email for syncing and
                            sending.
                        </p>
                </div>

                <!-- IMAP Tab -->
                <div v-if="activeTab === 'imap'" class="space-y-4">
                        <Alert v-if="error" variant="destructive">
                            <AlertDescription>{{ error }}</AlertDescription>
                        </Alert>

                        <p class="text-sm text-muted-foreground">
                            Connect using IMAP/SMTP for custom email providers or self-hosted servers.
                        </p>

                        <div class="space-y-4">
                            <!-- Email & Password -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="email">Email Address</Label>
                                    <Input
                                        id="email"
                                        v-model="imapForm.email"
                                        type="email"
                                        placeholder="you@example.com"
                                        @blur="detectImapSettings"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="password">Password</Label>
                                    <Input
                                        id="password"
                                        v-model="imapForm.password"
                                        type="password"
                                        placeholder="••••••••"
                                    />
                                </div>
                            </div>

                            <!-- Account Name -->
                            <div class="space-y-2">
                                <Label for="name">Account Name (Optional)</Label>
                                <Input
                                    id="name"
                                    v-model="imapForm.name"
                                    placeholder="My Work Email"
                                />
                            </div>

                            <!-- IMAP Settings -->
                            <div class="space-y-2">
                                <Label class="text-sm font-semibold">IMAP Settings (Incoming Mail)</Label>
                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="imapHost" class="text-xs">Host</Label>
                                        <Input
                                            id="imapHost"
                                            v-model="imapForm.imapHost"
                                            placeholder="imap.example.com"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="imapPort" class="text-xs">Port</Label>
                                        <Input
                                            id="imapPort"
                                            v-model.number="imapForm.imapPort"
                                            type="number"
                                            placeholder="993"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- SMTP Settings -->
                            <div class="space-y-2">
                                <Label class="text-sm font-semibold">SMTP Settings (Outgoing Mail)</Label>
                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="smtpHost" class="text-xs">Host</Label>
                                        <Input
                                            id="smtpHost"
                                            v-model="imapForm.smtpHost"
                                            placeholder="smtp.example.com"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="smtpPort" class="text-xs">Port</Label>
                                        <Input
                                            id="smtpPort"
                                            v-model.number="imapForm.smtpPort"
                                            type="number"
                                            placeholder="587"
                                        />
                                    </div>
                                </div>
                            </div>

                            <Alert>
                                <AlertDescription class="text-xs">
                                    <strong>Note:</strong> Settings are auto-detected for Gmail, Outlook, and Yahoo.
                                    For other providers, check your email provider's documentation.
                                </AlertDescription>
                            </Alert>

                            <div class="flex gap-2">
                                <Button
                                    class="flex-1"
                                    :disabled="isConnecting"
                                    @click="connectImap"
                                >
                                    {{ isConnecting ? 'Connecting...' : 'Connect Account' }}
                                </Button>
                                <Button variant="outline" @click="emit('close')">
                                    Cancel
                                </Button>
                            </div>
                        </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
