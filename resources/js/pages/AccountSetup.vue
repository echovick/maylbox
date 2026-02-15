<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConnectAccount from '@/components/email/ConnectAccount.vue';
import { useEmails } from '@/composables/useEmails';

const props = defineProps<{
    userEmail: string;
}>();

const { accounts } = useEmails();

const handleConnect = async (accountData: any) => {
    try {
        const response = await fetch('/api/email-accounts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify(accountData),
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Failed to connect account');
        }

        const data = await response.json();
        console.log('Account connected:', data);

        // Redirect to mail page after successful connection
        router.visit('/mail');
    } catch (error) {
        console.error('Error connecting account:', error);
        // Error will be shown in ConnectAccount component
    }
};

const handleClose = () => {
    // If user has existing accounts, go to mail
    if (accounts.length > 0) {
        router.visit('/mail');
    }
    // Otherwise stay on setup page (onboarding required)
};
</script>

<template>
    <Head title="Connect Your Account" />

    <ConnectAccount :prefill-email="userEmail" @connect="handleConnect" @close="handleClose" />
</template>
