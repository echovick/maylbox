<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import type { Label } from '@/types/email';

defineProps<{
    open: boolean;
    labels: Label[];
}>();

const emit = defineEmits<{
    close: [];
    create: [label: Omit<Label, 'id'>];
    update: [label: Label];
    delete: [labelId: string];
}>();

// State
const editingLabel = ref<Label | null>(null);
const newLabelName = ref('');
const newLabelColor = ref('#3b82f6');

// Available colors
const colors = [
    { name: 'Blue', value: '#3b82f6' },
    { name: 'Red', value: '#ef4444' },
    { name: 'Green', value: '#10b981' },
    { name: 'Yellow', value: '#f59e0b' },
    { name: 'Purple', value: '#8b5cf6' },
    { name: 'Pink', value: '#ec4899' },
    { name: 'Indigo', value: '#6366f1' },
    { name: 'Teal', value: '#14b8a6' },
    { name: 'Orange', value: '#f97316' },
    { name: 'Gray', value: '#6b7280' },
];

const handleCreate = () => {
    if (!newLabelName.value.trim()) return;

    emit('create', {
        name: newLabelName.value.trim(),
        color: newLabelColor.value,
    });

    newLabelName.value = '';
    newLabelColor.value = '#3b82f6';
};

const startEdit = (label: Label) => {
    editingLabel.value = { ...label };
};

const cancelEdit = () => {
    editingLabel.value = null;
};

const saveEdit = () => {
    if (editingLabel.value) {
        emit('update', editingLabel.value);
        editingLabel.value = null;
    }
};

const handleDelete = (labelId: string) => {
    if (confirm('Are you sure you want to delete this label?')) {
        emit('delete', labelId);
    }
};
</script>

<template>
    <Dialog :open="open" @update:open="(val) => !val && emit('close')">
        <DialogContent class="max-w-2xl">
            <DialogHeader>
                <DialogTitle>Manage Labels</DialogTitle>
                <DialogDescription>
                    Create and organize labels to categorize your emails.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-6">
                <!-- Create New Label -->
                <div class="space-y-3">
                    <h3 class="text-sm font-medium">Create New Label</h3>
                    <div class="flex gap-2">
                        <div class="flex-1 space-y-2">
                            <Input
                                v-model="newLabelName"
                                placeholder="Label name"
                                @keydown.enter="handleCreate"
                            />
                        </div>
                        <div class="flex gap-2">
                            <select
                                v-model="newLabelColor"
                                class="rounded-md border border-input bg-background px-3 text-sm"
                            >
                                <option
                                    v-for="color in colors"
                                    :key="color.value"
                                    :value="color.value"
                                >
                                    {{ color.name }}
                                </option>
                            </select>
                            <div
                                class="h-10 w-10 shrink-0 rounded"
                                :style="{ backgroundColor: newLabelColor }"
                            />
                            <Button @click="handleCreate">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mr-2 h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                                Create
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Existing Labels -->
                <div class="space-y-3">
                    <h3 class="text-sm font-medium">Your Labels</h3>
                    <div class="max-h-80 space-y-2 overflow-y-auto">
                        <div
                            v-if="labels.length === 0"
                            class="py-8 text-center text-sm text-muted-foreground"
                        >
                            No labels yet. Create one above!
                        </div>
                        <div
                            v-for="label in labels"
                            :key="label.id"
                            class="flex items-center gap-3 rounded-lg border border-sidebar-border p-3"
                        >
                            <!-- Editing mode -->
                            <template v-if="editingLabel?.id === label.id">
                                <div
                                    class="h-8 w-8 shrink-0 rounded"
                                    :style="{ backgroundColor: editingLabel.color }"
                                />
                                <Input
                                    v-model="editingLabel.name"
                                    class="flex-1"
                                    @keydown.enter="saveEdit"
                                />
                                <select
                                    v-model="editingLabel.color"
                                    class="rounded-md border border-input bg-background px-2 py-1 text-sm"
                                >
                                    <option
                                        v-for="color in colors"
                                        :key="color.value"
                                        :value="color.value"
                                    >
                                        {{ color.name }}
                                    </option>
                                </select>
                                <div class="flex gap-1">
                                    <Button size="sm" @click="saveEdit">Save</Button>
                                    <Button size="sm" variant="ghost" @click="cancelEdit">
                                        Cancel
                                    </Button>
                                </div>
                            </template>

                            <!-- View mode -->
                            <template v-else>
                                <div
                                    class="h-8 w-8 shrink-0 rounded"
                                    :style="{ backgroundColor: label.color }"
                                />
                                <span class="flex-1 font-medium">{{ label.name }}</span>
                                <div class="flex gap-1">
                                    <Button
                                        size="sm"
                                        variant="ghost"
                                        @click="startEdit(label)"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"
                                            />
                                            <path
                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"
                                            />
                                        </svg>
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="ghost"
                                        @click="handleDelete(label.id)"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                            />
                                        </svg>
                                    </Button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button variant="outline" @click="emit('close')">Close</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
