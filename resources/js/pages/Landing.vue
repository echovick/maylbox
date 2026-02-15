<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

defineProps<{
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Maylbox - Email That Just Works" />

    <div class="min-h-screen bg-background">
        <!-- Navigation -->
        <nav class="border-b border-sidebar-border">
            <div class="mx-auto max-w-7xl px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-primary"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m2 7 10 7 10-7" />
                        </svg>
                        <span class="text-xl font-bold text-foreground">Maylbox</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <Link href="/login">
                            <Button variant="ghost">Sign In</Button>
                        </Link>
                        <Link v-if="canRegister" href="/register">
                            <Button>Get Started</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="mx-auto max-w-7xl px-6 py-20 md:py-32">
            <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
                <div class="flex flex-col justify-center">
                    <Badge class="mb-6 w-fit" variant="secondary">
                        Custom Domain Email Client
                    </Badge>
                    <h1 class="mb-6 text-4xl font-bold tracking-tight text-foreground md:text-6xl">
                        Webmail, but better
                    </h1>
                    <p class="mb-8 text-xl text-muted-foreground md:text-2xl">
                        Connect your custom domain email. Modern interface. No hosting required.
                        Sign up → connect inbox → send email in under 60 seconds.
                    </p>
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <Link v-if="canRegister" href="/register">
                            <Button size="lg" class="w-full sm:w-auto">
                                Get Started Free
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="ml-2 h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </Button>
                        </Link>
                        <Button size="lg" variant="outline" class="w-full sm:w-auto">
                            View Demo
                        </Button>
                    </div>
                    <div class="mt-8 flex items-center gap-6 text-sm text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-green-500"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <path d="M22 4 12 14.01l-3-3" />
                            </svg>
                            No credit card
                        </div>
                        <div class="flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-green-500"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <path d="M22 4 12 14.01l-3-3" />
                            </svg>
                            Free forever
                        </div>
                        <div class="flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-green-500"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <path d="M22 4 12 14.01l-3-3" />
                            </svg>
                            Setup in 60s
                        </div>
                    </div>
                </div>

                <!-- Hero Image / Screenshot -->
                <div class="relative">
                    <div
                        class="rounded-xl border border-sidebar-border bg-muted/30 p-4 shadow-2xl"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-red-500" />
                            <div class="h-3 w-3 rounded-full bg-yellow-500" />
                            <div class="h-3 w-3 rounded-full bg-green-500" />
                        </div>
                        <div
                            class="aspect-video rounded-lg border border-sidebar-border bg-background"
                        >
                            <!-- Simplified inbox preview -->
                            <div class="flex h-full">
                                <!-- Sidebar -->
                                <div class="w-1/4 border-r border-sidebar-border p-3">
                                    <div class="mb-2 rounded bg-primary/10 p-2 text-xs font-medium">
                                        Inbox
                                    </div>
                                    <div class="space-y-1">
                                        <div class="rounded bg-muted/50 p-2 text-xs">Sent</div>
                                        <div class="rounded bg-muted/50 p-2 text-xs">Drafts</div>
                                    </div>
                                </div>
                                <!-- Email list -->
                                <div class="flex-1 border-r border-sidebar-border">
                                    <div
                                        class="space-y-px border-b border-sidebar-border bg-background p-2"
                                    >
                                        <div class="rounded bg-muted/30 p-2">
                                            <div class="mb-1 h-2 w-1/3 rounded bg-muted-foreground/30" />
                                            <div class="h-2 w-full rounded bg-muted-foreground/20" />
                                        </div>
                                        <div class="rounded p-2">
                                            <div class="mb-1 h-2 w-1/4 rounded bg-muted-foreground/30" />
                                            <div class="h-2 w-5/6 rounded bg-muted-foreground/20" />
                                        </div>
                                        <div class="rounded p-2">
                                            <div class="mb-1 h-2 w-1/3 rounded bg-muted-foreground/30" />
                                            <div class="h-2 w-4/5 rounded bg-muted-foreground/20" />
                                        </div>
                                    </div>
                                </div>
                                <!-- Email viewer -->
                                <div class="flex-1 p-3">
                                    <div class="mb-3 h-3 w-3/4 rounded bg-muted-foreground/30" />
                                    <div class="space-y-2">
                                        <div class="h-2 w-full rounded bg-muted-foreground/20" />
                                        <div class="h-2 w-full rounded bg-muted-foreground/20" />
                                        <div class="h-2 w-2/3 rounded bg-muted-foreground/20" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="border-t border-sidebar-border bg-muted/30 py-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-foreground md:text-4xl">
                        Everything you need. Nothing you don't.
                    </h2>
                    <p class="text-lg text-muted-foreground">
                        No fluff. No startup cosplay. Just a solid email client.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="rounded-lg border border-sidebar-border bg-background p-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-primary"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Quick Setup
                        </h3>
                        <p class="text-muted-foreground">
                            Connect via OAuth (Gmail, Outlook) or IMAP in seconds. Auto-detection for common providers.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="rounded-lg border border-sidebar-border bg-background p-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-primary"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="17 8 12 3 7 8" />
                                <line x1="12" x2="12" y1="3" y2="15" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Custom Domain
                        </h3>
                        <p class="text-muted-foreground">
                            Use your own domain email. We're a client, not a host. Keep your existing provider.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="rounded-lg border border-sidebar-border bg-background p-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-primary"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Privacy First
                        </h3>
                        <p class="text-muted-foreground">
                            End-to-end encryption. Your credentials never touch our servers. OAuth 2.0 security.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="rounded-lg border border-sidebar-border bg-background p-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-primary"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Smart Threading
                        </h3>
                        <p class="text-muted-foreground">
                            Conversation view that actually works. Collapse, expand, and navigate threads effortlessly.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="rounded-lg border border-sidebar-border bg-background p-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-primary"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Powerful Search
                        </h3>
                        <p class="text-muted-foreground">
                            Find anything instantly. Search across subject, body, sender, and recipients in real-time.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="rounded-lg border border-sidebar-border bg-background p-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-primary"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                                <line x1="7" x2="7.01" y1="7" y2="7" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Labels & Organization
                        </h3>
                        <p class="text-muted-foreground">
                            Create custom labels with colors. Filter and organize your inbox your way.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-foreground md:text-4xl">
                        From zero to inbox in 60 seconds
                    </h2>
                    <p class="text-lg text-muted-foreground">
                        The fastest way to get your custom domain email working
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">
                            1
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Sign Up
                        </h3>
                        <p class="text-muted-foreground">
                            Create your account in seconds. No credit card required.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">
                            2
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Connect Email
                        </h3>
                        <p class="text-muted-foreground">
                            OAuth for Gmail/Outlook or IMAP for custom domains. Auto-detection included.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">
                            3
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-foreground">
                            Start Emailing
                        </h3>
                        <p class="text-muted-foreground">
                            Your inbox syncs instantly. Compose, reply, and organize from a modern interface.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="border-t border-sidebar-border bg-primary/5 py-20">
            <div class="mx-auto max-w-4xl px-6 text-center">
                <h2 class="mb-4 text-3xl font-bold text-foreground md:text-4xl">
                    Ready to upgrade your email?
                </h2>
                <p class="mb-8 text-lg text-muted-foreground">
                    Join thousands using Maylbox for their custom domain email.
                </p>
                <div class="flex flex-col justify-center gap-4 sm:flex-row">
                    <Link v-if="canRegister" href="/register">
                        <Button size="lg" class="w-full sm:w-auto">
                            Get Started Free
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="ml-2 h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </Button>
                    </Link>
                    <Link href="/login">
                        <Button size="lg" variant="outline" class="w-full sm:w-auto">
                            Sign In
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-sidebar-border py-12">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid gap-8 md:grid-cols-4">
                    <div>
                        <div class="mb-4 flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-primary"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m2 7 10 7 10-7" />
                            </svg>
                            <span class="text-lg font-bold text-foreground">Maylbox</span>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Email that just works. Custom domain. Modern interface.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold text-foreground">Product</h4>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li><a href="#" class="hover:text-foreground">Features</a></li>
                            <li><a href="#" class="hover:text-foreground">Pricing</a></li>
                            <li><a href="#" class="hover:text-foreground">Security</a></li>
                            <li><a href="#" class="hover:text-foreground">Roadmap</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold text-foreground">Resources</h4>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li><a href="#" class="hover:text-foreground">Documentation</a></li>
                            <li><a href="#" class="hover:text-foreground">API Reference</a></li>
                            <li><a href="#" class="hover:text-foreground">Support</a></li>
                            <li><a href="#" class="hover:text-foreground">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold text-foreground">Company</h4>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li><a href="#" class="hover:text-foreground">About</a></li>
                            <li><a href="#" class="hover:text-foreground">Privacy</a></li>
                            <li><a href="#" class="hover:text-foreground">Terms</a></li>
                            <li><a href="#" class="hover:text-foreground">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-12 border-t border-sidebar-border pt-8 text-center text-sm text-muted-foreground">
                    © 2026 Maylbox. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
