import { ref, computed } from 'vue';
import type { Email, EmailAddress, EmailSearchParams, EmailThread, Folder, Label } from '@/types/email';

// API response shape for paginated emails
interface PaginatedResponse {
    data: any[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}

// Account shape from Inertia props
interface AccountProp {
    id: number;
    name: string;
    email: string;
    type: string;
    provider: string | null;
    is_default: boolean;
    is_active: boolean;
    sync_status: string;
    last_synced_at: string | null;
    sync_error: string | null;
}

// Global state
const emails = ref<Email[]>([]);
const folders = ref<Folder[]>([]);
const accounts = ref<AccountProp[]>([]);
const labels = ref<Label[]>([]);
const selectedEmailId = ref<string | null>(null);
const currentFolderId = ref<string>('');
const currentAccountId = ref<string>('');
const currentLabelId = ref<string | null>(null);
const searchQuery = ref<string>('');
const isSearching = ref<boolean>(false);
const isLoading = ref<boolean>(false);
const syncStatus = ref<string>('');
const syncError = ref<string | null>(null);

// Thread state
const threadEmailsCache = ref<Email[]>([]);
const isLoadingThread = ref(false);

// Pagination state
const currentPage = ref(1);
const totalPages = ref(1);
const totalEmails = ref(0);

// Initialized flag to prevent double init
let initialized = false;

export function useEmails() {
    // --- Helpers to map API data to frontend types ---
    function mapApiEmailToEmail(raw: any): Email {
        return {
            id: String(raw.id),
            folderId: String(raw.folder_id),
            accountId: String(raw.email_account_id),
            uid: String(raw.uid),
            messageId: raw.message_id || '',
            inReplyTo: raw.in_reply_to || undefined,
            from: { email: raw.from_email, name: raw.from_name || undefined },
            to: raw.to || [],
            cc: raw.cc || undefined,
            bcc: raw.bcc || undefined,
            replyTo: raw.reply_to || undefined,
            subject: raw.subject || '(No Subject)',
            bodyText: raw.body_text || undefined,
            bodyHtml: raw.body_html || undefined,
            snippet: raw.snippet || '',
            date: raw.date || '',
            size: raw.size || 0,
            isRead: !!raw.is_read,
            isStarred: !!raw.is_starred,
            isDraft: !!raw.is_draft,
            hasAttachments: !!raw.has_attachments,
            attachments: raw.attachments_meta?.map((a: any, i: number) => ({
                id: `att-${raw.id}-${i}`,
                filename: a.filename,
                contentType: a.contentType,
                size: a.size,
                isInline: a.isInline,
            })) || undefined,
        };
    }

    function mapApiFolder(raw: any): Folder {
        return {
            id: String(raw.id),
            accountId: String(raw.email_account_id),
            name: raw.name,
            type: raw.type,
            remoteName: raw.remote_name,
            unreadCount: raw.unread_count || 0,
            totalCount: raw.total_count || 0,
        };
    }

    // --- API calls ---
    async function fetchFolders(accountId: string): Promise<void> {
        try {
            const res = await fetch(`/api/email-accounts/${accountId}/folders`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const data = await res.json();
            folders.value = data.map(mapApiFolder);
        } catch (e) {
            console.error('Failed to fetch folders:', e);
        }
    }

    async function fetchEmails(accountId: string, folderId: string, page = 1): Promise<void> {
        isLoading.value = true;
        try {
            const params = new URLSearchParams({
                account_id: accountId,
                folder_id: folderId,
                page: String(page),
            });
            const res = await fetch(`/api/emails?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const data: PaginatedResponse = await res.json();
            emails.value = data.data.map(mapApiEmailToEmail);
            currentPage.value = data.current_page;
            totalPages.value = data.last_page;
            totalEmails.value = data.total;
        } catch (e) {
            console.error('Failed to fetch emails:', e);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchEmailDetail(emailId: string): Promise<Email | null> {
        try {
            const res = await fetch(`/api/emails/${emailId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return null;
            const raw = await res.json();
            const mapped = mapApiEmailToEmail(raw);

            // Merge into existing object to preserve reactivity
            const idx = emails.value.findIndex(e => e.id === emailId);
            if (idx !== -1) {
                Object.assign(emails.value[idx], mapped);
            }

            // Also merge into thread cache for cross-folder emails
            const cacheIdx = threadEmailsCache.value.findIndex(e => e.id === emailId);
            if (cacheIdx !== -1) {
                Object.assign(threadEmailsCache.value[cacheIdx], mapped);
            }

            return mapped;
        } catch (e) {
            console.error('Failed to fetch email detail:', e);
            return null;
        }
    }

    async function fetchThreadEmails(accountId: string, messageIds: string[]): Promise<Email[]> {
        try {
            const params = new URLSearchParams({ account_id: accountId });
            messageIds.forEach(id => params.append('message_ids[]', id));
            const res = await fetch(`/api/emails/thread?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return [];
            const data = await res.json();
            return (data as any[]).map(mapApiEmailToEmail);
        } catch (e) {
            console.error('Failed to fetch thread emails:', e);
            return [];
        }
    }

    function buildThreadChain(startEmail: Email, allEmails: Email[]): Email[] {
        const byMessageId = new Map<string, Email>();
        const byInReplyTo = new Map<string, Email[]>();

        for (const e of allEmails) {
            if (e.messageId) byMessageId.set(e.messageId, e);
            if (e.inReplyTo) {
                const list = byInReplyTo.get(e.inReplyTo) || [];
                list.push(e);
                byInReplyTo.set(e.inReplyTo, list);
            }
        }

        const visited = new Set<string>();
        const chain: Email[] = [];
        const queue = [startEmail];

        while (queue.length > 0) {
            const current = queue.shift()!;
            if (visited.has(current.id)) continue;
            visited.add(current.id);
            chain.push(current);

            // Follow inReplyTo upward
            if (current.inReplyTo) {
                const parent = byMessageId.get(current.inReplyTo);
                if (parent && !visited.has(parent.id)) queue.push(parent);
            }

            // Follow replies downward
            if (current.messageId) {
                const replies = byInReplyTo.get(current.messageId) || [];
                for (const reply of replies) {
                    if (!visited.has(reply.id)) queue.push(reply);
                }
            }
        }

        chain.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
        return chain;
    }

    async function patchEmail(emailId: string, data: Record<string, any>): Promise<void> {
        try {
            const res = await fetch(`/api/emails/${emailId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]*)/)?.[1] || ''
                    ),
                },
                body: JSON.stringify(data),
            });
            if (!res.ok) return;
            await res.json();

            // If the API returns the updated folder unread_count, update it
            if (data.is_read !== undefined) {
                // Refresh folders to get updated counts
                await fetchFolders(currentAccountId.value);
            }
        } catch (e) {
            console.error('Failed to update email:', e);
        }
    }

    // --- Initialization ---
    async function initializeFromProps(accountProps: AccountProp[], defaultAccountId: number | null): Promise<void> {
        if (initialized && accounts.value.length > 0) return;
        initialized = true;

        accounts.value = accountProps;

        const accountId = defaultAccountId
            ? String(defaultAccountId)
            : String(accountProps[0]?.id || '');

        if (!accountId) return;

        currentAccountId.value = accountId;

        // Fetch folders, then select inbox, then fetch emails
        await fetchFolders(accountId);

        const inboxFolder = folders.value.find(f => f.type === 'inbox');
        if (inboxFolder) {
            currentFolderId.value = inboxFolder.id;
            await fetchEmails(accountId, inboxFolder.id);
        } else if (folders.value.length > 0) {
            currentFolderId.value = folders.value[0].id;
            await fetchEmails(accountId, folders.value[0].id);
        }

        // Set sync status from account props
        const account = accountProps.find(a => a.id === Number(accountId));
        if (account) {
            syncStatus.value = account.sync_status;

            if (account.sync_status === 'failed') {
                syncError.value = account.sync_error || 'Sync failed. Please try again.';
            } else if (account.sync_status === 'pending' || account.sync_status === 'syncing') {
                pollSyncStatus();
            }
        }
    }

    // --- Computed ---
    const selectedEmail = computed(() => {
        return emails.value.find(email => email.id === selectedEmailId.value);
    });

    const currentFolder = computed(() => {
        return folders.value.find(folder => folder.id === currentFolderId.value);
    });

    const filteredEmails = computed(() => {
        let results = emails.value;

        // Apply client-side search filter
        if (searchQuery.value.trim()) {
            const query = searchQuery.value.toLowerCase().trim();
            results = results.filter(
                email =>
                    email.subject.toLowerCase().includes(query) ||
                    email.snippet.toLowerCase().includes(query) ||
                    email.from.name?.toLowerCase().includes(query) ||
                    email.from.email.toLowerCase().includes(query) ||
                    email.to.some(
                        addr =>
                            addr.name?.toLowerCase().includes(query) ||
                            addr.email.toLowerCase().includes(query),
                    ),
            );
        }

        // Filter by label if one is selected
        if (currentLabelId.value) {
            results = results.filter(email =>
                email.labels?.some(label => label.id === currentLabelId.value)
            );
        }

        return results;
    });

    const unreadCount = computed(() => {
        return filteredEmails.value.filter(email => !email.isRead).length;
    });

    const selectedThread = computed<EmailThread | null>(() => {
        const email = selectedEmail.value;
        if (!email) return null;

        // Merge current folder emails + cross-folder cache, deduplicate by id
        const allEmails = [...emails.value];
        const seenIds = new Set(allEmails.map(e => e.id));
        for (const e of threadEmailsCache.value) {
            if (!seenIds.has(e.id)) {
                allEmails.push(e);
                seenIds.add(e.id);
            }
        }

        const chain = buildThreadChain(email, allEmails);
        if (chain.length < 2) return null;

        // Strip RE:/FWD:/FW: prefixes for thread subject
        const subject = chain[0].subject.replace(/^(re|fwd|fw):\s*/i, '').trim() || chain[0].subject;

        // Collect unique participants
        const participantMap = new Map<string, EmailAddress>();
        for (const e of chain) {
            participantMap.set(e.from.email, e.from);
            for (const addr of e.to) {
                participantMap.set(addr.email, addr);
            }
        }

        return {
            id: `thread-${chain[0].id}`,
            subject,
            participants: Array.from(participantMap.values()),
            lastMessageAt: chain[chain.length - 1].date,
            messageCount: chain.length,
            emails: chain,
            hasUnread: chain.some(e => !e.isRead),
        };
    });

    // --- Actions ---
    async function selectEmail(emailId: string) {
        selectedEmailId.value = emailId;

        // Mark as read after 2 seconds
        setTimeout(() => {
            markAsRead(emailId);
        }, 2000);

        // Thread detection: check if this email is part of a thread
        const email = emails.value.find(e => e.id === emailId);
        if (!email) return;

        // Collect known message IDs from local emails that form a chain
        const localChain = buildThreadChain(email, emails.value);
        const hasThread = email.inReplyTo || localChain.length > 1;

        if (hasThread) {
            isLoadingThread.value = true;
            try {
                // Gather all message IDs from the local chain to find cross-folder members
                const messageIds = localChain
                    .flatMap(e => [e.messageId, e.inReplyTo].filter(Boolean) as string[]);
                const uniqueIds = [...new Set(messageIds)];

                const crossFolderEmails = await fetchThreadEmails(currentAccountId.value, uniqueIds);
                threadEmailsCache.value = crossFolderEmails;

                // Fetch full body content for all thread emails missing body, in parallel
                const allChainEmails = [...localChain];
                const seenIds = new Set(allChainEmails.map(e => e.id));
                for (const e of crossFolderEmails) {
                    if (!seenIds.has(e.id)) {
                        allChainEmails.push(e);
                        seenIds.add(e.id);
                    }
                }

                const needsBody = allChainEmails.filter(e => !e.bodyHtml && !e.bodyText);
                await Promise.all(needsBody.map(e => fetchEmailDetail(e.id)));
            } finally {
                isLoadingThread.value = false;
            }
        } else {
            threadEmailsCache.value = [];
        }
    }

    function markAsRead(emailId: string, read = true) {
        const email = emails.value.find(e => e.id === emailId);
        if (email && email.isRead !== read) {
            email.isRead = read;
            patchEmail(emailId, { is_read: read });
        }
    }

    function toggleStar(emailId: string) {
        const email = emails.value.find(e => e.id === emailId);
        if (email) {
            email.isStarred = !email.isStarred;
            patchEmail(emailId, { is_starred: email.isStarred });
        }
    }

    function deleteEmail(emailId: string) {
        // For now, just remove from the local list
        emails.value = emails.value.filter(e => e.id !== emailId);
        if (selectedEmailId.value === emailId) {
            selectedEmailId.value = null;
        }
    }

    function moveToFolder(emailId: string, folderId: string) {
        // Not yet supported via API — local only
        const email = emails.value.find(e => e.id === emailId);
        if (email) {
            email.folderId = folderId;
        }
    }

    async function setCurrentFolder(folderId: string) {
        currentFolderId.value = folderId;
        currentLabelId.value = null;
        selectedEmailId.value = null;
        threadEmailsCache.value = [];
        currentPage.value = 1;
        await fetchEmails(currentAccountId.value, folderId);
    }

    async function setCurrentAccount(accountId: string) {
        currentAccountId.value = accountId;
        selectedEmailId.value = null;
        threadEmailsCache.value = [];
        currentPage.value = 1;

        await fetchFolders(accountId);

        const inboxFolder = folders.value.find(f => f.type === 'inbox');
        if (inboxFolder) {
            currentFolderId.value = inboxFolder.id;
            await fetchEmails(accountId, inboxFolder.id);
        } else if (folders.value.length > 0) {
            currentFolderId.value = folders.value[0].id;
            await fetchEmails(accountId, folders.value[0].id);
        }
    }

    function setSearchQuery(query: string) {
        searchQuery.value = query;
        isSearching.value = query.trim().length > 0;
    }

    function clearSearch() {
        searchQuery.value = '';
        isSearching.value = false;
    }

    // Label management (client-side only for now)
    function setCurrentLabel(labelId: string | null) {
        currentLabelId.value = labelId;
        selectedEmailId.value = null;
    }

    function addLabelToEmail(emailId: string, label: Label) {
        const email = emails.value.find(e => e.id === emailId);
        if (email) {
            if (!email.labels) email.labels = [];
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
        const newLabel: Label = { id: `label-${Date.now()}`, ...labelData };
        labels.value.push(newLabel);
        return newLabel;
    }

    function updateLabel(label: Label) {
        const index = labels.value.findIndex(l => l.id === label.id);
        if (index !== -1) {
            labels.value[index] = label;
            emails.value.forEach(email => {
                if (email.labels) {
                    const li = email.labels.findIndex(l => l.id === label.id);
                    if (li !== -1) email.labels[li] = label;
                }
            });
        }
    }

    function deleteLabel(labelId: string) {
        labels.value = labels.value.filter(l => l.id !== labelId);
        emails.value.forEach(email => {
            if (email.labels) {
                email.labels = email.labels.filter(l => l.id !== labelId);
            }
        });
        if (currentLabelId.value === labelId) {
            currentLabelId.value = null;
        }
    }

    function searchEmails(params: EmailSearchParams): Email[] {
        let results = [...emails.value];
        if (params.q) {
            const query = params.q.toLowerCase();
            results = results.filter(
                e =>
                    e.subject.toLowerCase().includes(query) ||
                    e.snippet.toLowerCase().includes(query) ||
                    e.from.name?.toLowerCase().includes(query) ||
                    e.from.email.toLowerCase().includes(query),
            );
        }
        if (params.isRead !== undefined) results = results.filter(e => e.isRead === params.isRead);
        if (params.isStarred !== undefined) results = results.filter(e => e.isStarred === params.isStarred);
        return results;
    }

    // --- Credentials ---
    async function updateCredentials(accountId: string, data: Record<string, any>): Promise<boolean> {
        try {
            const res = await fetch(`/api/email-accounts/${accountId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]*)/)?.[1] || ''
                    ),
                },
                body: JSON.stringify(data),
            });
            if (!res.ok) return false;
            const result = await res.json();
            syncStatus.value = result.account?.sync_status || 'pending';
            syncError.value = null;
            pollSyncStatus();
            return true;
        } catch (e) {
            console.error('Failed to update credentials:', e);
            return false;
        }
    }

    // --- Sync ---
    async function refreshEmails(): Promise<void> {
        if (!currentAccountId.value) return;

        try {
            const res = await fetch(`/api/email-accounts/${currentAccountId.value}/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]*)/)?.[1] || ''
                    ),
                },
            });
            if (!res.ok) return;
            const data = await res.json();
            syncStatus.value = data.sync_status;
            syncError.value = null;
            pollSyncStatus();
        } catch (e) {
            console.error('Failed to trigger sync:', e);
        }
    }

    let pollTimer: ReturnType<typeof setInterval> | null = null;

    function pollSyncStatus(): void {
        if (pollTimer) clearInterval(pollTimer);

        pollTimer = setInterval(async () => {
            if (!currentAccountId.value) return;

            try {
                const res = await fetch(`/api/email-accounts/${currentAccountId.value}/sync-status`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return;
                const data = await res.json();
                syncStatus.value = data.sync_status;
                syncError.value = data.sync_error;

                if (data.sync_status === 'synced' || data.sync_status === 'failed') {
                    if (pollTimer) clearInterval(pollTimer);
                    pollTimer = null;

                    // Auto-refresh folder list + emails on completion
                    if (data.sync_status === 'synced') {
                        await fetchFolders(currentAccountId.value);
                        if (currentFolderId.value) {
                            await fetchEmails(currentAccountId.value, currentFolderId.value, currentPage.value);
                        }
                    }
                }
            } catch {
                // Silently ignore poll errors
            }
        }, 2000);
    }

    // --- Pagination ---
    async function goToPage(page: number): Promise<void> {
        if (page < 1 || page > totalPages.value) return;
        await fetchEmails(currentAccountId.value, currentFolderId.value, page);
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
        isLoading,
        isLoadingThread,
        labels,
        syncStatus,
        syncError,

        // Pagination
        currentPage,
        totalPages,
        totalEmails,
        goToPage,

        // Actions
        initializeFromProps,
        fetchFolders,
        fetchEmails,
        fetchEmailDetail,
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
        refreshEmails,
        updateCredentials,

        // Label actions
        setCurrentLabel,
        addLabelToEmail,
        removeLabelFromEmail,
        createLabel,
        updateLabel,
        deleteLabel,

        // Data
        folders,
        accounts,
        threads: [] as any[],
    };
}
