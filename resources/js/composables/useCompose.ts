import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';
import { useEmails } from '@/composables/useEmails';
import type { ComposeDraft, Email, EmailAddress } from '@/types/email';

// Global state for composition
const isComposing = ref(false);
const isSending = ref(false);
const draft = ref<ComposeDraft>({
    to: [],
    cc: [],
    bcc: [],
    subject: '',
    bodyHtml: '',
    attachments: [],
    mode: 'compose',
});

export function useCompose() {
    const { accounts } = useEmails();

    function getCurrentAccount() {
        return accounts.value?.[0] || { email: '', name: '' };
    }

    // Actions
    function openCompose() {
        isComposing.value = true;
        resetDraft();
        draft.value.mode = 'compose';
        const account = getCurrentAccount();
        draft.value.from = {
            email: account.email,
            name: account.name,
        };
    }

    function openReply(email: Email, replyAll = false) {
        isComposing.value = true;
        resetDraft();
        draft.value.mode = replyAll ? 'replyAll' : 'reply';
        draft.value.inReplyTo = email.messageId;
        draft.value.replyToEmail = email;

        // Set recipient(s)
        draft.value.to = [email.from];
        if (replyAll && email.to.length > 0) {
            // Include all original recipients except current user
            const currentUserEmail = getCurrentAccount().email;
            draft.value.to = [
                ...draft.value.to,
                ...email.to.filter(addr => addr.email !== currentUserEmail),
            ];
            if (email.cc && email.cc.length > 0) {
                draft.value.cc = email.cc.filter(
                    addr => addr.email !== currentUserEmail,
                );
            }
        }

        // Set subject
        const subjectPrefix = email.subject.startsWith('RE:') ? '' : 'RE: ';
        draft.value.subject = `${subjectPrefix}${email.subject}`;

        // Set from
        const replyAccount = getCurrentAccount();
        draft.value.from = {
            email: replyAccount.email,
            name: replyAccount.name,
        };

        // Add quoted text
        const quotedText = `
<br><br>
<div style="border-left: 2px solid #ccc; padding-left: 10px; color: #666;">
    <p><strong>On ${new Date(email.date).toLocaleString()}, ${email.from.name || email.from.email} wrote:</strong></p>
    ${email.bodyHtml || email.bodyText}
</div>
        `;
        draft.value.bodyHtml = quotedText;
    }

    function openForward(email: Email) {
        isComposing.value = true;
        resetDraft();
        draft.value.mode = 'forward';
        draft.value.forwardEmail = email;

        // Set subject
        const subjectPrefix = email.subject.startsWith('FWD:') ? '' : 'FWD: ';
        draft.value.subject = `${subjectPrefix}${email.subject}`;

        // Set from
        const fwdAccount = getCurrentAccount();
        draft.value.from = {
            email: fwdAccount.email,
            name: fwdAccount.name,
        };

        // Include attachments
        if (email.attachments && email.attachments.length > 0) {
            draft.value.attachments = [...email.attachments];
        }

        // Add forwarded message
        const forwardedText = `
<br><br>
<div style="border: 1px solid #ccc; padding: 10px; background-color: #f9f9f9;">
    <p><strong>---------- Forwarded Message ----------</strong></p>
    <p><strong>From:</strong> ${email.from.name || email.from.email} &lt;${email.from.email}&gt;</p>
    <p><strong>Date:</strong> ${new Date(email.date).toLocaleString()}</p>
    <p><strong>Subject:</strong> ${email.subject}</p>
    <p><strong>To:</strong> ${email.to.map(addr => addr.name || addr.email).join(', ')}</p>
    <br>
    ${email.bodyHtml || email.bodyText}
</div>
        `;
        draft.value.bodyHtml = forwardedText;
    }

    function closeCompose() {
        // TODO: Show "Save draft?" dialog if there's content
        isComposing.value = false;
    }

    function resetDraft() {
        draft.value = {
            to: [],
            cc: [],
            bcc: [],
            subject: '',
            bodyHtml: '',
            attachments: [],
            mode: 'compose',
        };
    }

    function addRecipient(type: 'to' | 'cc' | 'bcc', address: EmailAddress) {
        if (!draft.value[type]) {
            draft.value[type] = [];
        }
        draft.value[type]!.push(address);
    }

    function removeRecipient(
        type: 'to' | 'cc' | 'bcc',
        email: string,
    ) {
        if (draft.value[type]) {
            draft.value[type] = draft.value[type]!.filter(
                addr => addr.email !== email,
            );
        }
    }

    function saveDraft() {
        // TODO: Implement draft saving
        console.log('Saving draft:', draft.value);
    }

    async function sendEmail() {
        if (isSending.value) return;

        const { currentAccountId, fetchFolders, fetchEmails, currentFolderId } = useEmails();

        const accountId = currentAccountId.value;
        if (!accountId) {
            toast.error('No email account selected.');
            return;
        }

        isSending.value = true;

        try {
            const res = await fetch('/api/emails/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]*)/)?.[1] || '',
                    ),
                },
                body: JSON.stringify({
                    account_id: Number(accountId),
                    from_name: draft.value.from?.name || undefined,
                    to: draft.value.to,
                    cc: draft.value.cc?.length ? draft.value.cc : undefined,
                    bcc: draft.value.bcc?.length ? draft.value.bcc : undefined,
                    subject: draft.value.subject,
                    body_html: draft.value.bodyHtml,
                    in_reply_to: draft.value.inReplyTo || undefined,
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                toast.error(data.message || 'Failed to send email.');
                return;
            }

            toast.success('Email sent successfully.');
            isComposing.value = false;
            resetDraft();

            // Refresh folders to update sent count
            await fetchFolders(accountId);
            if (currentFolderId.value) {
                await fetchEmails(accountId, currentFolderId.value);
            }
        } catch {
            toast.error('Network error. Please try again.');
        } finally {
            isSending.value = false;
        }
    }

    function addAttachment(file: File) {
        // TODO: Implement file upload
        const attachment = {
            id: `att-${Date.now()}`,
            filename: file.name,
            contentType: file.type,
            size: file.size,
            isInline: false,
        };
        draft.value.attachments?.push(attachment);
    }

    function removeAttachment(attachmentId: string) {
        if (draft.value.attachments) {
            draft.value.attachments = draft.value.attachments.filter(
                att => att.id !== attachmentId,
            );
        }
    }

    // Computed
    const hasContent = computed(() => {
        return (
            draft.value.to.length > 0 ||
            draft.value.subject.length > 0 ||
            draft.value.bodyHtml.length > 0 ||
            (draft.value.attachments && draft.value.attachments.length > 0)
        );
    });

    const canSend = computed(() => {
        return (
            draft.value.to.length > 0 &&
            draft.value.subject.length > 0 &&
            draft.value.bodyHtml.length > 0
        );
    });

    return {
        // State
        isComposing,
        isSending,
        draft,
        hasContent,
        canSend,

        // Actions
        openCompose,
        openReply,
        openForward,
        closeCompose,
        addRecipient,
        removeRecipient,
        saveDraft,
        sendEmail,
        addAttachment,
        removeAttachment,
    };
}
