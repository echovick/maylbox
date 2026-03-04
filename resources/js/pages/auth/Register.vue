<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ExternalLink } from 'lucide-vue-next';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
</script>

<template>
    <AuthBase
        title="Closed Beta"
        description="Maylbox is currently in closed beta"
    >
        <Head title="Closed Beta - Maylbox" />

        <div class="flex flex-col items-center gap-6 text-center">
            <div class="space-y-2">
                <p class="text-muted-foreground">
                    We're not open for public registration yet, but we'd love to have you on board.
                </p>
                <p class="text-muted-foreground">
                    Fill out the form below to request access:
                </p>
            </div>

            <a
                href="https://forms.gle/ZFsgXtttFL1tuETV6"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-medium text-primary-foreground transition hover:bg-primary/90"
            >
                Request Beta Access
                <ExternalLink class="h-4 w-4" />
            </a>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                >Log in</TextLink>
            </div>
        </div>
    </AuthBase>
</template>
