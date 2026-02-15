import { ref, computed } from 'vue';
import type { Email, EmailSearchParams, Folder, EmailThread, Label } from '@/types/email';
import {
    dummyEmails,
    dummyFolders,
    dummyAccounts,
    dummyLabels,
    dummyThreads,
} from '@/data/dummyEmails';

// Global state for emails
const emails = ref<Email[]>([...dummyEmails]);
const selectedEmailId = ref<string | null>(null);
const currentFolderId = ref<string>('folder-1'); // Default to Inbox
const currentAccountId = ref<string>('1'); // Default to first account
const searchQuery = ref<string>('');
const isSearching = ref<boolean>(false);
const currentLabelId = ref<string | null>(null); // For filtering by label
const labels = ref([...dummyLabels]);

export function useEmails() {
    // Computed
    const selectedEmail = computed(() => {
        return emails.value.find(email => email.id === selectedEmailId.value);
    });

    const currentFolder = computed(() => {
        return dummyFolders.find(folder => folder.id === currentFolderId.value);
    });

    const filteredEmails = computed(() => {
        let results = emails.value;

        // Filter by label if one is selected (takes priority over folder)
        if (currentLabelId.value) {
            results = results.filter(email =>
                email.labels?.some(label => label.id === currentLabelId.value)
            );
            // Still filter by account
            results = results.filter(email => email.accountId === currentAccountId.value);
        } else {
            // Normal folder filtering
            results = results.filter(
                email =>
                    email.folderId === currentFolderId.value &&
                    email.accountId === currentAccountId.value,
            );
        }

        // Apply search filter if there's a search query
        if (searchQuery.value.trim()) {
            const query = searchQuery.value.toLowerCase().trim();
            results = results.filter(
                email =>
                    email.subject.toLowerCase().includes(query) ||
                    email.bodyText?.toLowerCase().includes(query) ||
                    email.from.name?.toLowerCase().includes(query) ||
                    email.from.email.toLowerCase().includes(query) ||
                    email.snippet.toLowerCase().includes(query) ||
                    email.to.some(
                        addr =>
                            addr.name?.toLowerCase().includes(query) ||
                            addr.email.toLowerCase().includes(query),
                    ),
            );
        }

        return results;
    });

    const unreadCount = computed(() => {
        return filteredEmails.value.filter(email => !email.isRead).length;
    });

    const selectedThread = computed(() => {
        if (!selectedEmail.value || !selectedEmail.value.threadId) {
            return null;
        }
        return dummyThreads.find(
            thread => thread.id === selectedEmail.value!.threadId,
        );
    });

    // Actions
    function selectEmail(emailId: string) {
        selectedEmailId.value = emailId;
        // Mark as read after selection (simulating auto-mark after 2 seconds)
        setTimeout(() => {
            markAsRead(emailId);
        }, 2000);
    }

    function markAsRead(emailId: string, read = true) {
        const email = emails.value.find(e => e.id === emailId);
        if (email) {
            email.isRead = read;
        }
    }

    function toggleStar(emailId: string) {
        const email = emails.value.find(e => e.id === emailId);
        if (email) {
            email.isStarred = !email.isStarred;
        }
    }

    function deleteEmail(emailId: string) {
        // Move to trash folder
        const email = emails.value.find(e => e.id === emailId);
        if (email) {
            email.folderId = 'folder-4'; // Trash folder
        }
        // If currently selected, deselect
        if (selectedEmailId.value === emailId) {
            selectedEmailId.value = null;
        }
    }

    function moveToFolder(emailId: string, folderId: string) {
        const email = emails.value.find(e => e.id === emailId);
        if (email) {
            email.folderId = folderId;
        }
    }

    function setCurrentFolder(folderId: string) {
        currentFolderId.value = folderId;
        selectedEmailId.value = null; // Deselect email when changing folders
    }

    function setCurrentAccount(accountId: string) {
        currentAccountId.value = accountId;
        selectedEmailId.value = null; // Deselect email when changing accounts
    }

    function setSearchQuery(query: string) {
        searchQuery.value = query;
        isSearching.value = query.trim().length > 0;
    }

    function clearSearch() {
        searchQuery.value = '';
        isSearching.value = false;
    }

    // Label management
    function setCurrentLabel(labelId: string | null) {
        currentLabelId.value = labelId;
        selectedEmailId.value = null; // Deselect email when changing labels
    }

    function addLabelToEmail(emailId: string, label: Label) {
        const email = emails.value.find(e => e.id === emailId);
        if (email) {
            if (!email.labels) {
                email.labels = [];
            }
            // Check if label already exists
            if (!email.labels.some(l => l.id === label.id)) {
                email.labels.push(label);
            }
        }
    }

    function removeLabelFromEmail(emailId: string, labelId: string) {
        const email = emails.value.find(e => e.id === emailId);
        if (email && email.labels) {
            email.labels = email.labels.filter(l => l.id !== labelId);
        }
    }

    function createLabel(labelData: Omit<Label, 'id'>) {
        const newLabel: Label = {
            id: `label-${Date.now()}`,
            ...labelData,
        };
        labels.value.push(newLabel);
        return newLabel;
    }

    function updateLabel(label: Label) {
        const index = labels.value.findIndex(l => l.id === label.id);
        if (index !== -1) {
            labels.value[index] = label;
            // Update the label in all emails that have it
            emails.value.forEach(email => {
                if (email.labels) {
                    const labelIndex = email.labels.findIndex(l => l.id === label.id);
                    if (labelIndex !== -1) {
                        email.labels[labelIndex] = label;
                    }
                }
            });
        }
    }

    function deleteLabel(labelId: string) {
        // Remove from labels array
        labels.value = labels.value.filter(l => l.id !== labelId);
        // Remove from all emails
        emails.value.forEach(email => {
            if (email.labels) {
                email.labels = email.labels.filter(l => l.id !== labelId);
            }
        });
        // Clear current label if it's the one being deleted
        if (currentLabelId.value === labelId) {
            currentLabelId.value = null;
        }
    }

    function searchEmails(params: EmailSearchParams): Email[] {
        let results = [...emails.value];

        // Filter by account
        if (params.accountId) {
            results = results.filter(e => e.accountId === params.accountId);
        }

        // Filter by folder
        if (params.folderId) {
            results = results.filter(e => e.folderId === params.folderId);
        }

        // Filter by query (search in subject, body, from)
        if (params.q) {
            const query = params.q.toLowerCase();
            results = results.filter(
                e =>
                    e.subject.toLowerCase().includes(query) ||
                    e.bodyText?.toLowerCase().includes(query) ||
                    e.from.name?.toLowerCase().includes(query) ||
                    e.from.email.toLowerCase().includes(query) ||
                    e.snippet.toLowerCase().includes(query),
            );
        }

        // Filter by from
        if (params.from) {
            const from = params.from.toLowerCase();
            results = results.filter(
                e =>
                    e.from.email.toLowerCase().includes(from) ||
                    e.from.name?.toLowerCase().includes(from),
            );
        }

        // Filter by read status
        if (params.isRead !== undefined) {
            results = results.filter(e => e.isRead === params.isRead);
        }

        // Filter by starred status
        if (params.isStarred !== undefined) {
            results = results.filter(e => e.isStarred === params.isStarred);
        }

        // Filter by attachments
        if (params.hasAttachment !== undefined) {
            results = results.filter(
                e => e.hasAttachments === params.hasAttachment,
            );
        }

        // Filter by labels
        if (params.labelIds && params.labelIds.length > 0) {
            results = results.filter(e =>
                e.labels?.some(label => params.labelIds?.includes(label.id)),
            );
        }

        // Filter by date range
        if (params.dateFrom) {
            results = results.filter(
                e => new Date(e.date) >= new Date(params.dateFrom!),
            );
        }

        if (params.dateTo) {
            results = results.filter(
                e => new Date(e.date) <= new Date(params.dateTo!),
            );
        }

        return results;
    }

    return {
        // State
        emails,
        selectedEmail,
        selectedEmailId,
        selectedThread,
        currentFolder,
        currentFolderId,
        currentAccountId,
        currentLabelId,
        filteredEmails,
        unreadCount,
        searchQuery,
        isSearching,
        labels,

        // Actions
        selectEmail,
        markAsRead,
        toggleStar,
        deleteEmail,
        moveToFolder,
        setCurrentFolder,
        setCurrentAccount,
        setSearchQuery,
        clearSearch,
        searchEmails,

        // Label actions
        setCurrentLabel,
        addLabelToEmail,
        removeLabelFromEmail,
        createLabel,
        updateLabel,
        deleteLabel,

        // Static data (for now)
        folders: dummyFolders,
        accounts: dummyAccounts,
        threads: dummyThreads,
    };
}
