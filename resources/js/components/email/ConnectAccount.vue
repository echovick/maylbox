<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Spinner } from '@/components/ui/spinner';

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
        // Google logo SVG path
        icon: 'M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z',
        color: 'bg-white',
        textColor: 'text-gray-700',
    },
    {
        id: 'outlook',
        name: 'Outlook',
        // Microsoft logo SVG path
        icon: 'M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z',
        color: 'bg-white',
        textColor: 'text-gray-700',
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

    // Emit to parent component which will handle the redirect
    emit('connect', accountData);

    // Keep connecting state active until parent handles it
    setTimeout(() => {
        isConnecting.value = false;
    }, 2000);
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-muted/30 p-4">
        <Card class="w-full max-w-lg">
            <CardHeader>
                <CardTitle class="text-2xl">Connect your email account</CardTitle>
                <CardDescription>
                    {{ suggestOAuth ? 'We recommend using OAuth for quick and secure setup' : 'Enter your email credentials to get started' }}
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <Alert v-if="error" variant="destructive">
                    <AlertDescription>{{ error }}</AlertDescription>
                </Alert>

                <!-- Email & Password Form -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label for="email">Your Email</Label>
                        <Input
                            id="email"
                            v-model="email"
                            type="email"
                            placeholder="you@example.com"
                            :disabled="isConnecting"
                            @blur="detectImapSettings"
                        />
                        <p v-if="suggestOAuth" class="text-xs text-muted-foreground">
                            ✨ {{ detectedProvider === 'gmail' ? 'Gmail' : 'Outlook' }} detected - OAuth recommended below
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Email Account Password</Label>
                        <Input
                            id="password"
                            v-model="password"
                            type="password"
                            placeholder="••••••••"
                            :disabled="isConnecting"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ suggestOAuth
                                ? 'Enter your email password, or use OAuth below for easier setup'
                                : imapSettings.imapHost
                                    ? '✨ Settings auto-detected - just enter your email password'
                                    : 'Enter the password for your email account'
                            }}
                        </p>
                    </div>

                    <Button
                        class="w-full"
                        size="lg"
                        :disabled="isConnecting || !email || !password"
                        @click="connectImap"
                    >
                        <Spinner v-if="isConnecting" class="mr-2" />
                        Connect Account
                    </Button>
                </div>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t" />
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-background px-2 text-muted-foreground">Or</span>
                    </div>
                </div>

                <!-- OAuth Buttons -->
                <div class="space-y-3">
                    <Button
                        v-for="provider in oauthProviders"
                        :key="provider.id"
                        variant="outline"
                        size="lg"
                        class="w-full justify-start h-auto py-3 hover:bg-muted/50"
                        :disabled="isConnecting"
                        @click="connectOAuth(provider.id)"
                    >
                        <div class="flex items-center gap-3 w-full">
                            <div
                                :class="[
                                    provider.color,
                                    'flex h-10 w-10 shrink-0 items-center justify-center rounded border',
                                ]"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    :fill="provider.id === 'gmail' ? 'currentColor' : 'currentColor'"
                                    :class="[
                                        'h-5 w-5',
                                        provider.id === 'gmail' ? 'text-[#EA4335]' : 'text-[#0078D4]'
                                    ]"
                                >
                                    <path :d="provider.icon" />
                                </svg>
                            </div>
                            <div class="flex-1 text-left">
                                <div :class="['font-medium', provider.textColor]">
                                    Continue with {{ provider.name }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    Quick and secure OAuth setup
                                </div>
                            </div>
                        </div>
                    </Button>
                </div>

                <!-- Advanced Settings Toggle -->
                <div v-if="!suggestOAuth" class="text-center">
                    <button
                        @click="showAdvancedSettings = !showAdvancedSettings"
                        class="text-sm text-muted-foreground hover:text-foreground underline"
                    >
                        {{ showAdvancedSettings ? 'Hide' : 'Show' }} advanced settings
                    </button>
                </div>

                <!-- Advanced IMAP/SMTP Settings -->
                <div v-if="showAdvancedSettings" class="space-y-4 rounded-lg border p-4">
                    <p class="text-sm font-medium">Manual IMAP/SMTP Configuration</p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="imapHost" class="text-xs">IMAP Host</Label>
                            <Input
                                id="imapHost"
                                v-model="imapSettings.imapHost"
                                placeholder="imap.example.com"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="imapPort" class="text-xs">IMAP Port</Label>
                            <Input
                                id="imapPort"
                                v-model.number="imapSettings.imapPort"
                                type="number"
                                placeholder="993"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="smtpHost" class="text-xs">SMTP Host</Label>
                            <Input
                                id="smtpHost"
                                v-model="imapSettings.smtpHost"
                                placeholder="smtp.example.com"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="smtpPort" class="text-xs">SMTP Port</Label>
                            <Input
                                id="smtpPort"
                                v-model.number="imapSettings.smtpPort"
                                type="number"
                                placeholder="587"
                            />
                        </div>
                    </div>
                </div>

                <p class="text-center text-xs text-muted-foreground">
                    By connecting, you agree to Maylbox's access to your email for syncing and sending.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
