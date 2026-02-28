<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConnectAccount from '@/components/email/ConnectAccount.vue';

const props = defineProps<{
    userEmail: string;
}>();

const isSubmitting = ref(false);
const apiError = ref<string | null>(null);

function getCsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

const handleConnect = async (accountData: any) => {
    isSubmitting.value = true;
    apiError.value = null;

    try {
        const response = await fetch('/api/email-accounts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': getCsrfToken(),
            },
            credentials: 'same-origin',
            body: JSON.stringify(accountData),
        });

        if (response.status === 201) {
            router.visit('/mail');
            return;
        }

        const data = await response.json();

        if (response.status === 422) {
            const errors = data.errors;
            const firstError = Object.values(errors).flat()[0] as string;
            apiError.value = firstError || 'Validation failed. Please check your input.';
        } else {
            apiError.value = data.message || 'Something went wrong. Please try again.';
        }
    } catch {
        apiError.value = 'Network error. Please check your connection and try again.';
    } finally {
        isSubmitting.value = false;
    }
};

const handleClose = () => {
    // Stay on setup page — onboarding is required
};
</script>

<template>
    <Head title="Connect Your Account" />

    <div v-if="apiError" class="fixed top-4 left-1/2 z-50 -translate-x-1/2">
        <div class="rounded-md border border-destructive/50 bg-destructive/10 px-4 py-3 text-sm text-destructive">
            {{ apiError }}
        </div>
    </div>

    <ConnectAccount :prefill-email="userEmail" @connect="handleConnect" @close="handleClose" />
</template>
