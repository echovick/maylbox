<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Send,
    Mail,
    ArrowRight,
    Inbox,
    Tag,
    Search,
    Shield,
    Lock,
    Globe,
    Zap,
    Monitor,
    Smartphone,
    FolderOpen,
    Sparkles,
    MessageSquare,
} from 'lucide-vue-next';
import AppearanceToggle from '@/components/AppearanceToggle.vue';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';

defineProps<{
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Maylbox - All Your Emails, One Powerful Inbox" />

    <div class="min-h-screen bg-white dark:bg-gray-950">
        <!-- 1. Navbar -->
        <nav class="sticky top-0 z-50 border-b border-gray-100 bg-white/95 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-950/95">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-2">
                    <img src="/assets/imgs/maylbox-logo-icon.png" alt="Maylbox" class="h-7 w-auto" />
                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Maylbox</span>
                </div>
                <div class="hidden items-center gap-8 md:flex">
                    <a href="#features" class="text-sm text-gray-600 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">Features</a>
                    <a href="#ai" class="text-sm text-gray-600 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">AI</a>
                    <a href="#security" class="text-sm text-gray-600 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">Security</a>
                    <a href="#faq" class="text-sm text-gray-600 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">FAQ</a>
                </div>
                <div class="flex items-center gap-3">
                    <AppearanceToggle />
                    <Link href="/login" class="text-sm text-gray-600 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">
                        Log in
                    </Link>
                    <Link v-if="canRegister" href="/register">
                        <Button size="sm">Get Maylbox Free</Button>
                    </Link>
                </div>
            </div>
        </nav>

        <!-- 2. Hero -->
        <section class="px-6 pb-20 pt-24 md:pb-28 md:pt-32">
            <div class="mx-auto max-w-3xl text-center">
                <div class="mb-6 flex justify-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-1.5 dark:border-amber-800 dark:bg-amber-900/30">
                        <Sparkles class="h-4 w-4 text-amber-500" />
                        <span class="text-sm font-medium text-amber-700 dark:text-amber-300">Now with AI-powered features</span>
                    </div>
                </div>
                <h1 class="mb-5 text-4xl font-bold tracking-tight text-gray-900 md:text-6xl dark:text-gray-100">
                    All your emails. One powerful inbox.
                </h1>
                <p class="mx-auto mb-8 max-w-2xl text-lg text-gray-600 md:text-xl dark:text-gray-300">
                    Connect Gmail, Outlook, Yahoo, or any email provider in 60 seconds.
                    AI-powered organization, smart compose, and a modern interface that just works.
                </p>
                <Link v-if="canRegister" href="/register">
                    <Button size="lg" class="gap-2 px-8 text-base">
                        Get Started Free
                        <ArrowRight class="h-4 w-4" />
                    </Button>
                </Link>
            </div>

            <!-- Hero Inbox Mockup -->
            <div class="mx-auto mt-16 max-w-5xl">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl shadow-gray-200/50 dark:border-gray-700 dark:bg-gray-900 dark:shadow-black/30">
                    <div class="flex items-center gap-2 border-b border-gray-100 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/50">
                        <div class="h-3 w-3 rounded-full bg-gray-300 dark:bg-gray-600" />
                        <div class="h-3 w-3 rounded-full bg-gray-300 dark:bg-gray-600" />
                        <div class="h-3 w-3 rounded-full bg-gray-300 dark:bg-gray-600" />
                        <div class="ml-4 h-5 w-48 rounded bg-gray-200 dark:bg-gray-700" />
                    </div>
                    <div class="flex" style="height: 380px">
                        <!-- Sidebar with Account Switcher -->
                        <div class="hidden w-52 shrink-0 border-r border-gray-100 bg-gray-50/50 p-3 sm:block dark:border-gray-800 dark:bg-gray-800/30">
                            <!-- Account Switcher -->
                            <div class="mb-3 space-y-1">
                                <div class="flex items-center gap-2 rounded-lg bg-primary/10 px-2.5 py-1.5">
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                                    </svg>
                                    <span class="truncate text-xs font-medium text-primary">me@gmail.com</span>
                                </div>
                                <div class="flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-gray-500 dark:text-gray-400">
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24">
                                        <path d="M11.4 24H0V12.6h11.4V24z" fill="#F25022" />
                                        <path d="M24 24H12.6V12.6H24V24z" fill="#00A4EF" />
                                        <path d="M11.4 11.4H0V0h11.4v11.4z" fill="#7FBA00" />
                                        <path d="M24 11.4H12.6V0H24v11.4z" fill="#FFB900" />
                                    </svg>
                                    <span class="truncate text-xs">work@outlook.com</span>
                                </div>
                                <div class="flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-gray-500 dark:text-gray-400">
                                    <Globe class="h-4 w-4 shrink-0 text-gray-400" />
                                    <span class="truncate text-xs">hello@mysite.com</span>
                                </div>
                            </div>
                            <Separator class="my-2" />
                            <div class="mb-4 flex items-center gap-2 rounded-lg bg-primary/10 px-3 py-2">
                                <Inbox class="h-4 w-4 text-primary" />
                                <span class="text-sm font-medium text-primary">Inbox</span>
                                <span class="ml-auto rounded-full bg-primary px-1.5 py-0.5 text-[10px] font-medium text-primary-foreground">12</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                                    <div class="h-3.5 w-3.5 rounded bg-gray-200 dark:bg-gray-700" />
                                    Starred
                                </div>
                                <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                                    <div class="h-3.5 w-3.5 rounded bg-gray-200 dark:bg-gray-700" />
                                    Sent
                                </div>
                                <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                                    <div class="h-3.5 w-3.5 rounded bg-gray-200 dark:bg-gray-700" />
                                    Drafts
                                </div>
                            </div>
                        </div>
                        <!-- Email List -->
                        <div class="w-72 shrink-0 border-r border-gray-100 dark:border-gray-800">
                            <div class="border-b border-gray-100 px-3 py-2.5 dark:border-gray-800">
                                <div class="flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-1.5 dark:bg-gray-800">
                                    <Search class="h-3.5 w-3.5 text-gray-400 dark:text-gray-400" />
                                    <span class="text-xs text-gray-400 dark:text-gray-400">Search emails...</span>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                                <div class="bg-primary/5 px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Sarah Chen</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-400">2m ago</span>
                                    </div>
                                    <div class="mt-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">Q1 Planning Review</div>
                                    <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-300">Here's the updated timeline for...</div>
                                </div>
                                <div class="px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Alex Rivera</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-400">1h ago</span>
                                    </div>
                                    <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">Design system updates</div>
                                    <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-300">I've pushed the new component...</div>
                                </div>
                                <div class="px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Jordan Park</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-400">3h ago</span>
                                    </div>
                                    <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">Lunch tomorrow?</div>
                                    <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-300">Want to try that new place...</div>
                                </div>
                                <div class="px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Priya Sharma</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-400">5h ago</span>
                                    </div>
                                    <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">Invoice #4821</div>
                                    <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-300">Please find attached the...</div>
                                </div>
                                <div class="px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mike Thompson</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-400">1d ago</span>
                                    </div>
                                    <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">Re: API Documentation</div>
                                    <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-300">Looks good! Just a few minor...</div>
                                </div>
                            </div>
                        </div>
                        <!-- Email Viewer -->
                        <div class="hidden flex-1 p-6 md:block">
                            <div class="mb-1 flex items-center justify-between">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Q1 Planning Review</h3>
                                <span class="text-xs text-gray-400 dark:text-gray-400">Today, 10:34 AM</span>
                            </div>
                            <div class="mb-4 flex items-center gap-2">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-violet-100 text-[10px] font-medium text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">SC</div>
                                <span class="text-xs text-gray-500 dark:text-gray-300">Sarah Chen &lt;sarah@acme.co&gt;</span>
                            </div>
                            <div class="space-y-2.5 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                                <p>Hi team,</p>
                                <p>Here's the updated timeline for Q1. I've made adjustments based on our last discussion and moved the design milestone up by two weeks.</p>
                                <p>Key changes:</p>
                                <div class="space-y-1 pl-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-1 w-1 rounded-full bg-gray-400 dark:bg-gray-500" />
                                        <span class="text-xs text-gray-500 dark:text-gray-300">Design review moved to Jan 15</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1 w-1 rounded-full bg-gray-400 dark:bg-gray-500" />
                                        <span class="text-xs text-gray-500 dark:text-gray-300">Beta launch target: Feb 1</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Provider Logo Strip -->
        <section class="bg-gray-50 px-6 py-16 dark:bg-gray-900">
            <div class="mx-auto max-w-4xl text-center">
                <p class="mb-8 text-sm font-medium tracking-wide text-gray-500 uppercase dark:text-gray-400">Works with your favorite email providers</p>
                <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12">
                    <!-- Gmail -->
                    <div class="flex flex-col items-center gap-2">
                        <svg class="h-8 w-8" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Gmail</span>
                    </div>
                    <!-- Outlook -->
                    <div class="flex flex-col items-center gap-2">
                        <svg class="h-8 w-8" viewBox="0 0 24 24">
                            <path d="M11.4 24H0V12.6h11.4V24z" fill="#F25022" />
                            <path d="M24 24H12.6V12.6H24V24z" fill="#00A4EF" />
                            <path d="M11.4 11.4H0V0h11.4v11.4z" fill="#7FBA00" />
                            <path d="M24 11.4H12.6V0H24v11.4z" fill="#FFB900" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Outlook</span>
                    </div>
                    <!-- Yahoo -->
                    <div class="flex flex-col items-center gap-2">
                        <svg class="h-8 w-8" viewBox="0 0 30 30">
                            <path d="M24.31 4.5h-3.71l-1.88 4.5-2.1-4.5H12.7l4.23 8.37V18h4.02v-5.13L24.31 4.5zM8.16 21.08a2.65 2.65 0 100 5.3 2.65 2.65 0 000-5.3z" fill="#6001D2" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Yahoo</span>
                    </div>
                    <!-- iCloud -->
                    <div class="flex flex-col items-center gap-2">
                        <svg class="h-8 w-8" viewBox="0 0 24 24">
                            <path d="M19.35 10.04A7.49 7.49 0 0012 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 000 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z" fill="#A3AAAE" />
                        </svg>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">iCloud</span>
                    </div>
                    <!-- Custom Domain -->
                    <div class="flex flex-col items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center">
                            <Globe class="h-7 w-7 text-gray-400 dark:text-gray-500" />
                        </div>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Custom Domain</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. How It Works -->
        <section class="px-6 py-20 md:py-28">
            <div class="mx-auto max-w-4xl">
                <h2 class="mb-4 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    Up and running in 60 seconds
                </h2>
                <p class="mx-auto mb-14 max-w-xl text-center text-gray-600 dark:text-gray-300">
                    Three simple steps to a better email experience.
                </p>
                <div class="grid md:grid-cols-[1fr_auto_1fr_auto_1fr]">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">1</div>
                        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">Create your account</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Sign up for free in seconds. No credit card required.</p>
                    </div>
                    <!-- Connector -->
                    <div class="hidden items-center md:flex">
                        <div class="w-12 border-t-2 border-dashed border-gray-300 dark:border-gray-600" />
                    </div>
                    <!-- Step 2 -->
                    <div class="mt-8 text-center md:mt-0">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">2</div>
                        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">Connect your email</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Choose your provider and sign in securely via OAuth or IMAP.</p>
                    </div>
                    <!-- Connector -->
                    <div class="hidden items-center md:flex">
                        <div class="w-12 border-t-2 border-dashed border-gray-300 dark:border-gray-600" />
                    </div>
                    <!-- Step 3 -->
                    <div class="mt-8 text-center md:mt-0">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">3</div>
                        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">Enjoy your new inbox</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Your emails sync instantly. AI organizes everything.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Testimonials -->
        <section class="bg-gray-50 px-6 py-20 md:py-24 dark:bg-gray-900">
            <div class="mx-auto max-w-5xl">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-800/50">
                        <p class="mb-4 text-gray-700 dark:text-gray-300">
                            "I connected my Gmail and Outlook accounts in under a minute.
                            Having everything in one place with a clean interface has been a game-changer."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-sm font-medium text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">JM</div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">James Mitchell</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Freelance Developer</div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-800/50">
                        <p class="mb-4 text-gray-700 dark:text-gray-300">
                            "The AI categorization is surprisingly accurate. It sorted months of
                            backlog across my three email accounts without me lifting a finger."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-sm font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">AL</div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Aisha Lopez</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Startup Founder</div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-800/50">
                        <p class="mb-4 text-gray-700 dark:text-gray-300">
                            "Managing five email accounts used to mean five tabs. Now it's just Maylbox.
                            Switching between work and personal is seamless."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-sm font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">DK</div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Daniel Kim</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Agency Owner</div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-800/50">
                        <p class="mb-4 text-gray-700 dark:text-gray-300">
                            "OAuth sign-in for Gmail took seconds — no app passwords needed.
                            The security-first approach gave our team the confidence to switch."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-sm font-medium text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">RP</div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Rachel Patel</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Head of Operations</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Feature: Auto-Organize -->
        <section id="features" class="px-6 py-20 md:py-28">
            <div class="mx-auto max-w-5xl">
                <div class="mb-4 flex items-center justify-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/40">
                        <FolderOpen class="h-5 w-5 text-blue-600 dark:text-blue-400" :stroke-width="2.5" />
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/30">
                        <Sparkles class="h-5 w-5 text-amber-500 dark:text-amber-400" :stroke-width="2" />
                    </div>
                </div>
                <h2 class="mb-4 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    Every email organized, across every account.
                </h2>
                <p class="mx-auto mb-12 max-w-2xl text-center text-gray-600 dark:text-gray-300">
                    Maylbox uses AI to intelligently categorize your incoming mail across all your connected
                    accounts. Labels, folders, and smart filters work together to surface what matters.
                </p>

                <div class="mb-12 grid items-center gap-10 md:grid-cols-2">
                    <!-- Left: Inbox mockup with labels -->
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900">
                        <div class="border-b border-gray-100 bg-gray-50 px-4 py-2.5 dark:border-gray-800 dark:bg-gray-800/50">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-300">Inbox — Auto-categorized</span>
                        </div>
                        <div class="divide-y divide-gray-50 dark:divide-gray-800">
                            <div class="flex items-center gap-3 px-4 py-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[10px] font-medium text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">T</div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Team standup notes</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-300">Morning sync summary...</div>
                                </div>
                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Work</span>
                            </div>
                            <div class="flex items-center gap-3 px-4 py-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-green-100 text-[10px] font-medium text-green-600 dark:bg-green-900/40 dark:text-green-400">S</div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Your order has shipped</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-300">Tracking number: 1Z...</div>
                                </div>
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">Shopping</span>
                            </div>
                            <div class="flex items-center gap-3 px-4 py-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-purple-100 text-[10px] font-medium text-purple-600 dark:bg-purple-900/40 dark:text-purple-400">N</div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Weekly tech digest</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-300">Top stories this week...</div>
                                </div>
                                <span class="rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-medium text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">Newsletter</span>
                            </div>
                        </div>
                    </div>
                    <!-- Right: Description -->
                    <div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900 dark:text-gray-100">Smart categorization</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Incoming emails are automatically tagged and routed based on sender, subject,
                            and content — across all your connected accounts. Create custom rules or let
                            Maylbox's AI handle the sorting so you spend less time organizing and more time doing.
                        </p>
                    </div>
                </div>

                <!-- Two side-by-side cards -->
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Split your inbox into custom views</h4>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Tabs for every category you care about</p>
                        </div>
                        <div class="bg-gray-50 p-4 dark:bg-gray-800/50">
                            <div class="flex gap-2">
                                <span class="rounded-full bg-primary px-3 py-1 text-xs font-medium text-primary-foreground">All</span>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">Work</span>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">Personal</span>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">Updates</span>
                            </div>
                            <div class="mt-3 space-y-2">
                                <div class="h-8 rounded-lg bg-white ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700" />
                                <div class="h-8 rounded-lg bg-white ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700" />
                                <div class="h-8 rounded-lg bg-white ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700" />
                            </div>
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Group and filter by topic, sender, label</h4>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Find exactly what you need, fast</p>
                        </div>
                        <div class="bg-gray-50 p-4 dark:bg-gray-800/50">
                            <div class="flex flex-wrap gap-2">
                                <span class="flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                    <Tag class="h-3 w-3" /> Work
                                </span>
                                <span class="flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                    <div class="h-2 w-2 rounded-full bg-blue-400" /> From: team
                                </span>
                                <span class="flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                    <div class="h-2 w-2 rounded-full bg-green-400" /> Has attachment
                                </span>
                            </div>
                            <div class="mt-3 space-y-2">
                                <div class="h-8 rounded-lg bg-white ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700" />
                                <div class="h-8 rounded-lg bg-white ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Feature: AI-Powered -->
        <section id="ai" class="bg-gray-50 px-6 py-20 md:py-28 dark:bg-gray-900">
            <div class="mx-auto max-w-5xl">
                <div class="mb-4 flex justify-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/30">
                        <Sparkles class="h-5 w-5 text-amber-500 dark:text-amber-400" :stroke-width="2" />
                    </div>
                </div>
                <h2 class="mb-4 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    AI that actually helps you with email.
                </h2>
                <p class="mx-auto mb-12 max-w-2xl text-center text-gray-600 dark:text-gray-300">
                    Smart features that save you time without getting in the way.
                </p>

                <div class="grid items-center gap-10 md:grid-cols-2">
                    <!-- Left: AI Mockup -->
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900">
                        <div class="border-b border-gray-100 bg-gray-50 px-4 py-2.5 dark:border-gray-800 dark:bg-gray-800/50">
                            <div class="flex items-center gap-2">
                                <Sparkles class="h-3.5 w-3.5 text-amber-500" />
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-300">AI Assistant</span>
                            </div>
                        </div>
                        <div class="space-y-3 p-4">
                            <!-- Smart Reply Card -->
                            <div class="rounded-lg border border-blue-100 bg-blue-50/50 p-3 dark:border-blue-900/40 dark:bg-blue-900/20">
                                <div class="mb-1.5 flex items-center gap-2">
                                    <MessageSquare class="h-3.5 w-3.5 text-blue-500" />
                                    <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Smart Reply Suggestion</span>
                                </div>
                                <p class="text-xs text-blue-600 dark:text-blue-400">"Thanks for the update! I'll review the timeline and get back to you by end of day."</p>
                            </div>
                            <!-- Auto-Category Card -->
                            <div class="rounded-lg border border-amber-100 bg-amber-50/50 p-3 dark:border-amber-900/40 dark:bg-amber-900/20">
                                <div class="mb-1.5 flex items-center gap-2">
                                    <Tag class="h-3.5 w-3.5 text-amber-500" />
                                    <span class="text-xs font-medium text-amber-700 dark:text-amber-300">Auto-Categorized</span>
                                </div>
                                <div class="flex gap-1.5">
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Work</span>
                                    <span class="rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-medium text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">Project Update</span>
                                </div>
                            </div>
                            <!-- Thread Summary Card -->
                            <div class="rounded-lg border border-green-100 bg-green-50/50 p-3 dark:border-green-900/40 dark:bg-green-900/20">
                                <div class="mb-1.5 flex items-center gap-2">
                                    <Inbox class="h-3.5 w-3.5 text-green-500" />
                                    <span class="text-xs font-medium text-green-700 dark:text-green-300">Thread Summary</span>
                                </div>
                                <p class="text-xs text-green-600 dark:text-green-400">3 messages about Q1 timeline. Key decision: design review moved to Jan 15.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Right: Feature List -->
                    <div class="space-y-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <MessageSquare class="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">AI-drafted replies</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Get smart reply suggestions based on the conversation context.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Tag class="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Intelligent categorization</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Emails are automatically labeled and sorted as they arrive.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Inbox class="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Thread summaries</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Catch up on long threads with concise, AI-generated summaries.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Search class="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Smart search</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Search across all accounts using natural language queries.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Feature: Smart Compose -->
        <section class="px-6 py-20 md:py-28">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-12 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    Every reply drafted, intelligently.
                </h2>
                <div class="grid items-center gap-10 md:grid-cols-2">
                    <!-- Left: Feature list -->
                    <div class="space-y-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Zap class="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Snippets</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Save and reuse common replies with a quick shortcut.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Send class="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">AI-suggested quick replies</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Respond in one click with smart, context-aware suggested replies.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Inbox class="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Smart threading</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Conversations grouped automatically so you never lose context.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <FolderOpen class="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Labels & organization</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Color-coded labels and folders to keep everything in its place.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Right: Compose mockup -->
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900">
                        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Message</span>
                            <div class="flex gap-1.5">
                                <div class="h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600" />
                                <div class="h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600" />
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="mb-3 flex items-center gap-2 border-b border-gray-100 pb-3 dark:border-gray-800">
                                <span class="text-xs text-gray-400 dark:text-gray-400">To:</span>
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">sarah@acme.co</span>
                            </div>
                            <div class="mb-3 flex items-center gap-2 border-b border-gray-100 pb-3 dark:border-gray-800">
                                <span class="text-xs text-gray-400 dark:text-gray-400">Subject:</span>
                                <span class="text-xs text-gray-700 dark:text-gray-300">Re: Q1 Planning Review</span>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                <p>Hi Sarah,</p>
                                <p>Thanks for sending this over. The updated timeline looks great — I'm aligned with moving the design review up.</p>
                                <p>I'll share the revised resource plan by EOD tomorrow.</p>
                                <p class="text-gray-400 dark:text-gray-400">Best,<br />You</p>
                            </div>
                            <div class="mt-4 flex items-center gap-2">
                                <div class="rounded-lg bg-primary px-4 py-1.5 text-xs font-medium text-primary-foreground">Send</div>
                                <div class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs text-gray-500 dark:bg-gray-800 dark:text-gray-300">Attach</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. CTA: Connect Your Email -->
        <section class="bg-gray-50 px-6 py-20 md:py-24 dark:bg-gray-900">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="mb-4 text-2xl font-bold tracking-tight text-gray-900 md:text-3xl dark:text-gray-100">
                    Your emails, reunited. Connect every account in seconds.
                </h2>
                <div class="mb-8 flex items-center justify-center gap-4">
                    <!-- Overlapping provider icons -->
                    <div class="flex -space-x-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-gray-50 bg-white dark:border-gray-900 dark:bg-gray-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                            </svg>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-gray-50 bg-white dark:border-gray-900 dark:bg-gray-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path d="M11.4 24H0V12.6h11.4V24z" fill="#F25022" />
                                <path d="M24 24H12.6V12.6H24V24z" fill="#00A4EF" />
                                <path d="M11.4 11.4H0V0h11.4v11.4z" fill="#7FBA00" />
                                <path d="M24 11.4H12.6V0H24v11.4z" fill="#FFB900" />
                            </svg>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-gray-50 bg-white dark:border-gray-900 dark:bg-gray-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path d="M14.54 8.83l3.32-7.6h-3.06l-1.98 4.87L10.9 1.23H7.7l3.44 7.6-.1 2.66v4.28h3.4v-4.28l.1-2.66z" fill="#6001D2" />
                                <path d="M20.23 1.23h-2.96l-1.5 3.6 1.68 3.89 2.78-7.49zM5.07 18.77a2.12 2.12 0 100 4.24 2.12 2.12 0 000-4.24z" fill="#6001D2" />
                            </svg>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-gray-50 bg-white dark:border-gray-900 dark:bg-gray-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path d="M19.35 10.04A7.49 7.49 0 0012 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 000 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z" fill="#A3AAAE" />
                            </svg>
                        </div>
                    </div>
                    <ArrowRight class="h-5 w-5 text-gray-300 dark:text-gray-600" />
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/5">
                        <img src="/assets/imgs/maylbox-logo-icon.png" alt="Maylbox" class="h-8 w-auto" />
                    </div>
                </div>
                <Link v-if="canRegister" href="/register">
                    <Button size="lg" class="gap-2 px-8">
                        Get Started Free
                        <ArrowRight class="h-4 w-4" />
                    </Button>
                </Link>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Free forever. No credit card required.</p>
            </div>
        </section>

        <!-- 10. Feature: Works Everywhere -->
        <section class="px-6 py-20 md:py-28">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-4 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    Available anywhere. Just open your browser.
                </h2>
                <p class="mx-auto mb-12 max-w-2xl text-center text-gray-600 dark:text-gray-300">
                    No downloads required. Works on any device with a browser.
                </p>

                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Desktop mockup -->
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                        <div class="flex items-center gap-2 border-b border-gray-100 bg-gray-50 px-4 py-2.5 dark:border-gray-800 dark:bg-gray-800/50">
                            <Monitor class="h-4 w-4 text-gray-400 dark:text-gray-300" />
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-300">Desktop</span>
                        </div>
                        <div class="p-4">
                            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex h-48">
                                    <div class="w-1/4 border-r border-gray-100 bg-gray-50 p-2 dark:border-gray-800 dark:bg-gray-800/50">
                                        <div class="mb-1.5 h-5 rounded bg-primary/10" />
                                        <div class="space-y-1">
                                            <div class="h-4 rounded bg-gray-100 dark:bg-gray-800" />
                                            <div class="h-4 rounded bg-gray-100 dark:bg-gray-800" />
                                            <div class="h-4 rounded bg-gray-100 dark:bg-gray-800" />
                                        </div>
                                    </div>
                                    <div class="w-1/3 border-r border-gray-100 p-2 dark:border-gray-800">
                                        <div class="space-y-1.5">
                                            <div class="rounded bg-primary/5 p-1.5">
                                                <div class="mb-1 h-2 w-2/3 rounded bg-gray-300 dark:bg-gray-600" />
                                                <div class="h-2 w-full rounded bg-gray-200 dark:bg-gray-700" />
                                            </div>
                                            <div class="rounded p-1.5">
                                                <div class="mb-1 h-2 w-1/2 rounded bg-gray-200 dark:bg-gray-700" />
                                                <div class="h-2 w-full rounded bg-gray-100 dark:bg-gray-800" />
                                            </div>
                                            <div class="rounded p-1.5">
                                                <div class="mb-1 h-2 w-3/4 rounded bg-gray-200 dark:bg-gray-700" />
                                                <div class="h-2 w-full rounded bg-gray-100 dark:bg-gray-800" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 p-2">
                                        <div class="mb-2 h-3 w-3/4 rounded bg-gray-300 dark:bg-gray-600" />
                                        <div class="space-y-1.5">
                                            <div class="h-2 w-full rounded bg-gray-100 dark:bg-gray-800" />
                                            <div class="h-2 w-full rounded bg-gray-100 dark:bg-gray-800" />
                                            <div class="h-2 w-2/3 rounded bg-gray-100 dark:bg-gray-800" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Mobile mockup -->
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                        <div class="flex items-center gap-2 border-b border-gray-100 bg-gray-50 px-4 py-2.5 dark:border-gray-800 dark:bg-gray-800/50">
                            <Smartphone class="h-4 w-4 text-gray-400 dark:text-gray-300" />
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-300">Mobile</span>
                        </div>
                        <div class="flex justify-center p-4">
                            <div class="w-48 overflow-hidden rounded-2xl border-2 border-gray-200 dark:border-gray-700">
                                <div class="bg-gray-50 px-3 py-2 dark:bg-gray-800/50">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-semibold text-gray-700 dark:text-gray-300">Inbox</span>
                                        <Search class="h-3 w-3 text-gray-400 dark:text-gray-300" />
                                    </div>
                                </div>
                                <div class="divide-y divide-gray-50 dark:divide-gray-800">
                                    <div class="px-3 py-2.5">
                                        <div class="mb-0.5 h-2 w-2/3 rounded bg-gray-300 dark:bg-gray-600" />
                                        <div class="mb-0.5 h-1.5 w-full rounded bg-gray-200 dark:bg-gray-700" />
                                        <div class="h-1.5 w-3/4 rounded bg-gray-100 dark:bg-gray-800" />
                                    </div>
                                    <div class="px-3 py-2.5">
                                        <div class="mb-0.5 h-2 w-1/2 rounded bg-gray-300 dark:bg-gray-600" />
                                        <div class="mb-0.5 h-1.5 w-full rounded bg-gray-200 dark:bg-gray-700" />
                                        <div class="h-1.5 w-2/3 rounded bg-gray-100 dark:bg-gray-800" />
                                    </div>
                                    <div class="px-3 py-2.5">
                                        <div class="mb-0.5 h-2 w-3/4 rounded bg-gray-300 dark:bg-gray-600" />
                                        <div class="mb-0.5 h-1.5 w-full rounded bg-gray-200 dark:bg-gray-700" />
                                        <div class="h-1.5 w-1/2 rounded bg-gray-100 dark:bg-gray-800" />
                                    </div>
                                    <div class="px-3 py-2.5">
                                        <div class="mb-0.5 h-2 w-2/3 rounded bg-gray-200 dark:bg-gray-700" />
                                        <div class="mb-0.5 h-1.5 w-full rounded bg-gray-100 dark:bg-gray-800" />
                                        <div class="h-1.5 w-3/4 rounded bg-gray-100 dark:bg-gray-800" />
                                    </div>
                                </div>
                                <div class="flex justify-center bg-gray-50 py-1.5 dark:bg-gray-800/50">
                                    <div class="h-1 w-12 rounded-full bg-gray-300 dark:bg-gray-600" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. Security Section -->
        <section id="security" class="bg-gray-50 px-6 py-20 md:py-28 dark:bg-gray-900">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-12 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    Built with data security in mind.
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center dark:border-gray-800 dark:bg-gray-800/50">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                            <Shield class="h-6 w-6 text-primary" />
                        </div>
                        <h3 class="mb-2 text-base font-semibold text-gray-900 dark:text-gray-100">Your credentials, encrypted.</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Email credentials are encrypted at rest and in transit. We never store your password in plain text.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center dark:border-gray-800 dark:bg-gray-800/50">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                            <Lock class="h-6 w-6 text-primary" />
                        </div>
                        <h3 class="mb-2 text-base font-semibold text-gray-900 dark:text-gray-100">No training on your data.</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Your emails are yours. We don't use your content to train models or sell data to third parties.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center dark:border-gray-800 dark:bg-gray-800/50">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                            <Globe class="h-6 w-6 text-primary" />
                        </div>
                        <h3 class="mb-2 text-base font-semibold text-gray-900 dark:text-gray-100">OAuth 2.0 & IMAP over SSL.</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Sign in securely with Google or Microsoft OAuth. IMAP connections use SSL/TLS encryption.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 12. FAQ -->
        <section id="faq" class="px-6 py-20 md:py-28">
            <div class="mx-auto max-w-2xl">
                <h2 class="mb-10 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    Questions & answers
                </h2>
                <Accordion type="single" collapsible class="w-full">
                    <AccordionItem value="item-1">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            What email providers does Maylbox support?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            Maylbox supports Gmail and Outlook via secure OAuth sign-in, plus Yahoo, iCloud,
                            and any email provider that supports IMAP — including Zoho, Fastmail, and custom
                            domain hosting services like Namecheap and MXRoute.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-2">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            How does the 60-second setup work?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            Create your free account, choose your email provider, and sign in. For Gmail and
                            Outlook, just click "Sign in with Google" or "Sign in with Microsoft" — no passwords
                            to copy. For other providers, enter your IMAP credentials. Your emails start syncing
                            immediately.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-3">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            Is Maylbox free to use?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            Yes! Maylbox offers a free tier that covers most use cases. You can connect
                            your email accounts and use all core features — reading, composing, organizing,
                            AI features, and searching — at no cost. No credit card required.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-4">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            How does the AI work? Is it reading my emails?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            AI features like smart replies, categorization, and thread summaries process your
                            email content only when you use those features. Your data is never used to train
                            AI models, and processing happens securely on our servers with strict privacy
                            controls. You can disable AI features at any time.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-5">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            Can I connect multiple email accounts?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            Absolutely. You can connect multiple email accounts from different providers —
                            for example, a Gmail for personal, Outlook for work, and a custom domain for your
                            business. Switch between them seamlessly from the sidebar, and each account syncs
                            independently.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-6">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            Is my email data stored on your servers?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            Maylbox caches email metadata (like subjects and senders) to provide a fast
                            experience, but your full email content remains on your provider's servers.
                            Cached data is encrypted and you can clear it at any time.
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </section>

        <!-- 13. Footer -->
        <footer class="border-t border-gray-200 bg-white px-6 py-16 dark:border-gray-800 dark:bg-gray-950">
            <div class="mx-auto max-w-5xl">
                <div class="grid gap-10 md:grid-cols-4">
                    <div>
                        <div class="mb-3 flex items-center gap-2">
                            <img src="/assets/imgs/maylbox-logo-icon.png" alt="Maylbox" class="h-6 w-auto" />
                            <span class="text-base font-semibold text-gray-900 dark:text-gray-100">Maylbox</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            A modern email client for all your accounts. Connect Gmail, Outlook, Yahoo, or any provider and enjoy a fast, AI-powered inbox.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Product</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li><a href="#features" class="transition hover:text-gray-900 dark:hover:text-gray-100">Features</a></li>
                            <li><a href="#ai" class="transition hover:text-gray-900 dark:hover:text-gray-100">AI</a></li>
                            <li><a href="#security" class="transition hover:text-gray-900 dark:hover:text-gray-100">Security</a></li>
                            <li><a href="#faq" class="transition hover:text-gray-900 dark:hover:text-gray-100">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Resources</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li><a href="#" class="transition hover:text-gray-900 dark:hover:text-gray-100">Documentation</a></li>
                            <li><a href="#" class="transition hover:text-gray-900 dark:hover:text-gray-100">Support</a></li>
                            <li><a href="#" class="transition hover:text-gray-900 dark:hover:text-gray-100">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Company</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li><a href="#" class="transition hover:text-gray-900 dark:hover:text-gray-100">About</a></li>
                            <li><a href="#" class="transition hover:text-gray-900 dark:hover:text-gray-100">Privacy</a></li>
                            <li><a href="#" class="transition hover:text-gray-900 dark:hover:text-gray-100">Terms</a></li>
                            <li><a href="#" class="transition hover:text-gray-900 dark:hover:text-gray-100">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <Separator class="my-8" />
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <p class="text-sm text-gray-500 dark:text-gray-400">&copy; 2026 Maylbox. All rights reserved.</p>
                    <div class="flex items-center gap-4">
                        <!-- Twitter/X -->
                        <a href="#" class="text-gray-500 transition hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-300">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <!-- GitHub -->
                        <a href="#" class="text-gray-500 transition hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-300">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
