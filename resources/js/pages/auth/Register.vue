<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff, Mail } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import SocialLoginButtons from '@/components/SocialLoginButtons.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    canRegister?: boolean;
    socialProviders?: string[];
}>();

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
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

    <!-- Registration Form -->
    <AuthBase
        v-else
        title="Create an account"
        description="Get started with your email in under 60 seconds"
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        name="email"
                        placeholder="you@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <div class="relative">
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            :tabindex="2"
                            autocomplete="new-password"
                            name="password"
                            placeholder="Create a password"
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground hover:text-foreground"
                            :tabindex="-1"
                            @click="showPassword = !showPassword"
                        >
                            <component :is="showPassword ? EyeOff : Eye" class="h-4 w-4" />
                        </button>
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm Password</Label>
                    <div class="relative">
                        <Input
                            id="password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            name="password_confirmation"
                            placeholder="Confirm your password"
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground hover:text-foreground"
                            :tabindex="-1"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                        >
                            <component :is="showPasswordConfirmation ? EyeOff : Eye" class="h-4 w-4" />
                        </button>
                    </div>
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    size="lg"
                    tabindex="4"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    Create Account & Continue
                    <svg
                        v-if="!processing"
                        xmlns="http://www.w3.org/2000/svg"
                        class="ml-2 h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </Button>

                <SocialLoginButtons :providers="socialProviders ?? []" />
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
