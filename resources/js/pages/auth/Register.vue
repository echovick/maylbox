<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Mail } from 'lucide-vue-next';
import { defineAsyncComponent } from 'vue';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';

const RegisterForm = defineAsyncComponent(() => import('./RegisterForm.vue'));

defineProps<{
    canRegister?: boolean;
    socialProviders?: string[];
}>();
</script>

<template>
    <!-- Closed Beta Message -->
    <AuthBase
        v-if="!canRegister"
        title="Closed Beta"
        description="Maylbox is currently in closed beta"
    >
        <Head title="Closed Beta - Maylbox" />

        <div class="flex flex-col items-center gap-6 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
                <Mail class="h-8 w-8 text-primary" />
            </div>

            <div class="space-y-2">
                <p class="text-muted-foreground">
                    We're not open for public registration yet, but we'd love to have you on board.
                </p>
                <p class="text-muted-foreground">
                    To request access, send an email to:
                </p>
            </div>

            <a
                href="mailto:beta@maylbox.cc?subject=Maylbox%20Beta%20Access%20Request"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-medium text-primary-foreground transition hover:bg-primary/90"
            >
                <Mail class="h-4 w-4" />
                beta@maylbox.cc
            </a>

            <p class="text-xs text-muted-foreground">
                Use the subject line: <span class="font-medium">"Maylbox Beta Access Request"</span>
            </p>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                >Log in</TextLink>
            </div>
        </div>
    </AuthBase>

    <!-- Registration Form (lazy-loaded to avoid importing @/routes/register when registration is disabled) -->
    <AuthBase
        v-else
        title="Create an account"
        description="Get started with your email in under 60 seconds"
    >
        <Head title="Register" />
        <RegisterForm :social-providers="socialProviders" />
    </AuthBase>
</template>
