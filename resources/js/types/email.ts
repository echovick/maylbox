export interface EmailAddress {
    email: string;
    name?: string;
}

export interface EmailAttachment {
    id: string;
    filename: string;
    contentType: string;
    size: number;
    isInline: boolean;
}

export interface Email {
    id: string;
    threadId?: string;
    folderId: string;
    accountId: string;
    uid: string;
    messageId: string;
    inReplyTo?: string;
    from: EmailAddress;
    to: EmailAddress[];
    cc?: EmailAddress[];
    bcc?: EmailAddress[];
    replyTo?: EmailAddress[];
    subject: string;
    bodyText?: string;
    bodyHtml?: string;
    snippet: string;
    date: string;
    size: number;
    isRead: boolean;
    isStarred: boolean;
    isDraft: boolean;
    hasAttachments: boolean;
    attachments?: EmailAttachment[];
    labels?: Label[];
}

export interface EmailThread {
    id: string;
    subject: string;
    participants: EmailAddress[];
    lastMessageAt: string;
    messageCount: number;
    emails: Email[];
    hasUnread: boolean;
}

export interface Folder {
    id: string;
    accountId: string;
    name: string;
    type: 'inbox' | 'sent' | 'drafts' | 'trash' | 'spam' | 'archive' | 'custom';
    remoteName?: string;
    unreadCount: number;
    totalCount: number;
    icon?: string;
}

export interface Label {
    id: string;
    userId: string;
    name: string;
    color: string;
}

export interface EmailAccount {
    id: string;
    userId: string;
    name: string;
    email: string;
    provider?: 'gmail' | 'outlook' | 'custom';
    isDefault: boolean;
    lastSyncedAt?: string;
    syncEnabled: boolean;
    unreadCount: number;
}

export interface EmailFilter {
    field: 'from' | 'to' | 'subject' | 'body' | 'hasAttachment' | 'date';
    operator: 'contains' | 'equals' | 'startsWith' | 'endsWith' | 'before' | 'after';
    value: string | boolean | Date;
}

export interface EmailSearchParams {
    q?: string;
    accountId?: string;
    folderId?: string;
    from?: string;
    to?: string;
    subject?: string;
    hasAttachment?: boolean;
    isRead?: boolean;
    isStarred?: boolean;
    dateFrom?: string;
    dateTo?: string;
    labelIds?: string[];
}

export interface ComposeDraft {
    id?: string;
    from?: EmailAddress;
    to: EmailAddress[];
    cc?: EmailAddress[];
    bcc?: EmailAddress[];
    subject: string;
    bodyHtml: string;
    attachments?: EmailAttachment[];
    inReplyTo?: string;
    replyToEmail?: Email;
    forwardEmail?: Email;
    mode: 'compose' | 'reply' | 'replyAll' | 'forward';
}
