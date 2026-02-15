<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConnectAccount from '@/components/email/ConnectAccount.vue';
import { useEmails } from '@/composables/useEmails';

const { accounts } = useEmails();

const handleConnect = (accountData: any) => {
    // In production, this would make an API call to save the account
    console.log('Connecting account:', accountData);

    // Simulate successful connection
    setTimeout(() => {
        // Redirect to mail page after successful connection
        router.visit('/mail');
    }, 500);
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

    <ConnectAccount @connect="handleConnect" @close="handleClose" />
</template>
