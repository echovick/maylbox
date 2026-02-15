<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConnectAccount from '@/components/email/ConnectAccount.vue';
import { useEmails } from '@/composables/useEmails';

const props = defineProps<{
    userEmail: string;
}>();

const { accounts } = useEmails();

const handleConnect = (accountData: any) => {
    // For now, just log the connection data and redirect
    // TODO: Connect to backend API when ready
    console.log('Connecting account with data:', accountData);

    // Simulate connection success and redirect to mail page
    setTimeout(() => {
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

    <ConnectAccount :prefill-email="userEmail" @connect="handleConnect" @close="handleClose" />
</template>
