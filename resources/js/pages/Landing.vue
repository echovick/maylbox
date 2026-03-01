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
    PenLine,
    Monitor,
    Smartphone,
    FolderOpen,
    Sparkles,
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
    <Head title="Maylbox - The Inbox That Works for You" />

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
                    <img src="/assets/imgs/maylbox-logo-icon.png" alt="Maylbox" class="h-14 w-auto" />
                </div>
                <h1 class="mb-5 text-4xl font-bold tracking-tight text-gray-900 md:text-6xl dark:text-gray-100">
                    The inbox that works for you
                </h1>
                <p class="mx-auto mb-8 max-w-2xl text-lg text-gray-600 md:text-xl dark:text-gray-300">
                    Connect your custom domain email to a modern client that syncs instantly,
                    organizes effortlessly, and lets you focus on what matters.
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
                        <!-- Sidebar -->
                        <div class="hidden w-52 shrink-0 border-r border-gray-100 bg-gray-50/50 p-3 sm:block dark:border-gray-800 dark:bg-gray-800/30">
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
                                <Separator class="my-2" />
                                <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                                    <div class="h-2.5 w-2.5 rounded-full bg-blue-400" />
                                    Work
                                </div>
                                <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-400" />
                                    Personal
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

        <!-- 3. Social Proof / Testimonials -->
        <section class="bg-gray-50 px-6 py-20 md:py-24 dark:bg-gray-900">
            <div class="mx-auto max-w-5xl">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-800/50">
                        <p class="mb-4 text-gray-700 dark:text-gray-300">
                            "Switched from Roundcube to Maylbox and it's night and day. The interface is clean,
                            fast, and actually enjoyable to use with my custom domain."
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
                            "Finally, a web email client that doesn't look like it was built in 2005.
                            IMAP setup took 30 seconds and everything just synced."
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
                            "I manage three domains for my business and Maylbox handles them all
                            beautifully. The threading and search make it a breeze."
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
                            "We moved our whole team off a clunky webmail solution. Maylbox is fast,
                            modern, and the security features gave us peace of mind."
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

        <!-- 4. Feature: Auto-Organize -->
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
                    Every email organized, automatically.
                </h2>
                <p class="mx-auto mb-12 max-w-2xl text-center text-gray-600 dark:text-gray-300">
                    Maylbox intelligently categorizes your incoming mail so your inbox stays
                    clean. Labels, folders, and smart filters work together to surface what matters.
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
                            and content. Create custom rules or let Maylbox's defaults handle the sorting
                            — so you spend less time organizing and more time doing.
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

        <!-- 5. Feature: Smart Compose -->
        <section class="bg-gray-50 px-6 py-20 md:py-28 dark:bg-gray-900">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-12 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    Every reply drafted, quickly.
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
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Quick replies</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Respond in one click with smart suggested replies.</p>
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

        <!-- 6. CTA: Connect Your Email -->
        <section class="px-6 py-20 md:py-24">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="mb-4 text-2xl font-bold tracking-tight text-gray-900 md:text-3xl dark:text-gray-100">
                    Made for speed. Connect your email and see for yourself.
                </h2>
                <div class="mb-8 flex items-center justify-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <Mail class="h-6 w-6 text-gray-400 dark:text-gray-300" />
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
            </div>
        </section>

        <!-- 7. Feature: Modern Editor -->
        <section class="bg-gray-50 px-6 py-20 md:py-28 dark:bg-gray-900">
            <div class="mx-auto max-w-5xl">
                <div class="mb-4 flex justify-center">
                    <PenLine class="h-6 w-6 text-primary" />
                </div>
                <h2 class="mb-4 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    It feels just like a native app.
                </h2>
                <p class="mx-auto mb-12 max-w-2xl text-center text-gray-600 dark:text-gray-300">
                    Quickly compose beautiful emails with a clean, modern editor.
                </p>

                <!-- Editor Mockup -->
                <div class="mx-auto max-w-3xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-3 dark:border-gray-800">
                        <div class="flex gap-1.5">
                            <div class="h-3 w-3 rounded-full bg-gray-200 dark:bg-gray-700" />
                            <div class="h-3 w-3 rounded-full bg-gray-200 dark:bg-gray-700" />
                            <div class="h-3 w-3 rounded-full bg-gray-200 dark:bg-gray-700" />
                        </div>
                    </div>
                    <div class="border-b border-gray-100 px-5 py-3 dark:border-gray-800">
                        <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-300">
                            <span class="font-medium text-gray-600 dark:text-gray-300">B</span>
                            <span class="italic text-gray-600 dark:text-gray-300">I</span>
                            <span class="text-gray-600 underline dark:text-gray-300">U</span>
                            <Separator orientation="vertical" class="h-4" />
                            <span>Link</span>
                            <span>Image</span>
                            <span>List</span>
                            <span>Quote</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            <p>Hey team,</p>
                            <p>Quick update on the project — we've wrapped up the design phase and the dev sprint starts Monday. Here's what's coming:</p>
                            <div class="rounded-lg border-l-2 border-primary/30 bg-primary/5 p-3 text-sm text-gray-600 dark:text-gray-300">
                                "The new dashboard design reduces average task completion time by 40%."
                            </div>
                            <p>I'll send the full brief by Friday. Let me know if you have questions.</p>
                            <p class="text-gray-400 dark:text-gray-400">— Team Lead</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-100 px-5 py-3 dark:border-gray-800">
                        <div class="flex gap-2">
                            <div class="h-7 w-7 rounded bg-gray-100 dark:bg-gray-800" />
                            <div class="h-7 w-7 rounded bg-gray-100 dark:bg-gray-800" />
                        </div>
                        <div class="rounded-lg bg-primary px-5 py-1.5 text-xs font-medium text-primary-foreground">Send</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Feature: Works Everywhere -->
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

        <!-- 9. Security Section -->
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
                            Industry-standard protocols ensure your connection is always secure and authenticated.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. FAQ -->
        <section id="faq" class="px-6 py-20 md:py-28">
            <div class="mx-auto max-w-2xl">
                <h2 class="mb-10 text-center text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-100">
                    Questions & answers
                </h2>
                <Accordion type="single" collapsible class="w-full">
                    <AccordionItem value="item-1">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            What email providers are compatible with Maylbox?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            Maylbox works with any email provider that supports IMAP — including Gmail,
                            Outlook, Yahoo, Zoho, Fastmail, and custom domain hosting services like
                            Namecheap, MXRoute, and more. If your provider supports IMAP, Maylbox can connect to it.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-2">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            Is Maylbox free to use?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            Yes! Maylbox offers a free tier that covers most use cases. You can connect
                            your email account and use all core features — reading, composing, organizing,
                            and searching — at no cost.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-3">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            How does IMAP connection work?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            When you connect an account, Maylbox uses your IMAP credentials to sync your
                            email in real-time. Your messages stay on your provider's servers — we act as a
                            client, not a host. All connections are encrypted using SSL/TLS.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-4">
                        <AccordionTrigger class="text-left text-base text-gray-900 dark:text-gray-100">
                            Can I connect multiple email accounts?
                        </AccordionTrigger>
                        <AccordionContent class="text-gray-600 dark:text-gray-300">
                            Absolutely. You can connect multiple email accounts from different providers and
                            switch between them seamlessly within the Maylbox interface. Each account syncs
                            independently.
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-5">
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

        <!-- 11. Footer -->
        <footer class="border-t border-gray-200 bg-white px-6 py-16 dark:border-gray-800 dark:bg-gray-950">
            <div class="mx-auto max-w-5xl">
                <div class="grid gap-10 md:grid-cols-4">
                    <div>
                        <div class="mb-3 flex items-center gap-2">
                            <img src="/assets/imgs/maylbox-logo-icon.png" alt="Maylbox" class="h-6 w-auto" />
                            <span class="text-base font-semibold text-gray-900 dark:text-gray-100">Maylbox</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            A modern email client for custom domain users. Connect via IMAP and enjoy a fast, clean inbox.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Product</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li><a href="#features" class="transition hover:text-gray-900 dark:hover:text-gray-100">Features</a></li>
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
