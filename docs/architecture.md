# Maylbox.co - System Architecture

## Table of Contents
1. [System Overview](#system-overview)
2. [Technology Stack](#technology-stack)
3. [High-Level Architecture](#high-level-architecture)
4. [Component Architecture](#component-architecture)
5. [Database Design](#database-design)
6. [API Design](#api-design)
7. [Email Integration](#email-integration)
8. [Security Architecture](#security-architecture)
9. [Performance & Scalability](#performance--scalability)
10. [Infrastructure](#infrastructure)

---

## System Overview

Maylbox.co is a modern webmail client built with Laravel and Vue.js that connects to users' existing email accounts via IMAP/SMTP protocols. The system is designed for speed, simplicity, and scalability.

### Core Principles
- **Protocol-based**: Uses standard IMAP/SMTP (not a proprietary email service)
- **Metadata-centric**: Stores email metadata locally, references originals on provider servers
- **Background sync**: Asynchronous email fetching via queues
- **Real-time updates**: WebSocket/SSE for instant UI updates
- **Optimistic UI**: Immediate feedback before server confirmation

---

## Technology Stack

### Frontend
```
├── Framework: Vue 3 (Composition API)
├── Server Integration: Inertia.js
├── UI Components: Reka UI
├── Styling: Tailwind CSS v4
├── Rich Text Editor: Tiptap
├── Real-time: Laravel Echo + Pusher/Soketi
├── State Management: Vue Composables + Pinia (if needed)
└── Build Tool: Vite
```

### Backend
```
├── Framework: Laravel 12
├── Language: PHP 8.2+
├── Authentication: Laravel Fortify + Sanctum
├── Email Protocols: IMAP (php-imap), SMTP (Swift Mailer)
├── Queue System: Laravel Queues (Redis driver)
├── Cache: Redis
├── Storage: S3-compatible (MinIO/AWS S3)
├── WebSockets: Laravel Reverb or Soketi
└── Search: Laravel Scout + Meilisearch
```

### Database
- **Development**: SQLite
- **Production**: PostgreSQL 15+
- **Full-text Search**: PostgreSQL tsvector or Meilisearch

### Infrastructure
- **Hosting**: Laravel Forge + DigitalOcean/AWS
- **CDN**: Cloudflare
- **Monitoring**: Laravel Pulse + Sentry
- **Analytics**: Plausible or Fathom

---

## High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Client Layer                             │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐                │
│  │  Browser   │  │   Mobile   │  │  Desktop   │                │
│  │   (Vue)    │  │  (Future)  │  │  (Future)  │                │
│  └─────┬──────┘  └──────┬─────┘  └──────┬─────┘                │
│        │                │                │                       │
│        └────────────────┼────────────────┘                       │
│                         │                                        │
└─────────────────────────┼────────────────────────────────────────┘
                          │ HTTPS/WSS
┌─────────────────────────┼────────────────────────────────────────┐
│                    Application Layer                             │
│  ┌──────────────────────▼──────────────────────┐                │
│  │        Laravel Application (Inertia)        │                │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  │                │
│  │  │   Web    │  │   API    │  │   Auth   │  │                │
│  │  │  Routes  │  │  Routes  │  │ (Fortify)│  │                │
│  │  └─────┬────┘  └─────┬────┘  └─────┬────┘  │                │
│  │        └──────────────┼─────────────┘        │                │
│  │                       │                      │                │
│  │  ┌────────────────────▼───────────────────┐  │                │
│  │  │        Controllers Layer              │  │                │
│  │  │  • EmailController                    │  │                │
│  │  │  • AccountController                  │  │                │
│  │  │  • ComposeController                  │  │                │
│  │  └────────────┬──────────────────────────┘  │                │
│  │               │                              │                │
│  │  ┌────────────▼──────────────────────────┐  │                │
│  │  │        Service Layer                  │  │                │
│  │  │  • EmailSyncService                   │  │                │
│  │  │  • EmailSendService                   │  │                │
│  │  │  • AccountValidationService           │  │                │
│  │  │  • SearchService                      │  │                │
│  │  └────────────┬──────────────────────────┘  │                │
│  │               │                              │                │
│  │  ┌────────────▼──────────────────────────┐  │                │
│  │  │        Repository Layer               │  │                │
│  │  │  • EmailRepository                    │  │                │
│  │  │  • AccountRepository                  │  │                │
│  │  │  • ContactRepository                  │  │                │
│  │  └────────────┬──────────────────────────┘  │                │
│  │               │                              │                │
│  └───────────────┼──────────────────────────────┘                │
│                  │                                               │
│  ┌───────────────▼──────────────────────────────┐               │
│  │           Background Jobs (Queue)            │               │
│  │  • SyncEmailAccountJob                       │               │
│  │  • FetchNewEmailsJob                         │               │
│  │  • SendEmailJob                              │               │
│  │  • ProcessAttachmentJob                      │               │
│  │  • IndexEmailForSearchJob                    │               │
│  └───────────────┬──────────────────────────────┘               │
└──────────────────┼──────────────────────────────────────────────┘
                   │
┌──────────────────┼──────────────────────────────────────────────┐
│              Data & External Layer                               │
│  ┌────────────────▼─────────────┐  ┌────────────────────────┐  │
│  │       PostgreSQL Database     │  │    Redis Cache/Queue   │  │
│  │  • Users                      │  │  • Session storage     │  │
│  │  • EmailAccounts              │  │  • Queue jobs          │  │
│  │  • Emails (metadata)          │  │  • Email cache         │  │
│  │  • Threads                    │  │  • Rate limiting       │  │
│  │  • Folders, Labels, etc.      │  │  • Real-time pub/sub   │  │
│  └───────────────────────────────┘  └────────────────────────┘  │
│                                                                  │
│  ┌──────────────────────────────┐  ┌────────────────────────┐  │
│  │    S3-Compatible Storage      │  │    Meilisearch Index   │  │
│  │  • Email attachments          │  │  • Full-text search    │  │
│  │  • User avatars               │  │  • Indexed email body  │  │
│  └──────────────────────────────┘  └────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                   │
┌──────────────────┼──────────────────────────────────────────────┐
│         External Email Providers (IMAP/SMTP)                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │  Gmail   │  │ Outlook  │  │  Yahoo   │  │  Custom  │       │
│  │  (IMAP)  │  │  (IMAP)  │  │  (IMAP)  │  │  (IMAP)  │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
└─────────────────────────────────────────────────────────────────┘
```

---

## Component Architecture

### Backend Components

#### 1. Controllers
```php
app/Http/Controllers/
├── EmailController.php          # List, show, delete emails
├── ComposeController.php        # Compose, send, save drafts
├── EmailAccountController.php   # Add, edit, remove accounts
├── FolderController.php         # Manage folders
├── LabelController.php          # Manage labels
├── SearchController.php         # Search emails
├── ContactController.php        # Manage contacts
└── FilterController.php         # Email filters/rules
```

#### 2. Services
```php
app/Services/
├── Email/
│   ├── EmailSyncService.php           # Sync emails from IMAP
│   ├── EmailSendService.php           # Send via SMTP
│   ├── EmailParserService.php         # Parse email content
│   ├── ThreadingService.php           # Group emails into threads
│   └── AttachmentService.php          # Handle attachments
├── Account/
│   ├── AccountValidationService.php   # Validate IMAP/SMTP
│   ├── OAuthService.php               # Handle OAuth flows
│   └── AutoConfigService.php          # Auto-detect provider settings
├── Search/
│   └── SearchService.php              # Full-text search
└── Encryption/
    └── CredentialEncryptionService.php # Encrypt/decrypt credentials
```

#### 3. Jobs (Queue)
```php
app/Jobs/
├── SyncEmailAccountJob.php      # Full account sync
├── FetchNewEmailsJob.php        # Incremental fetch
├── SendEmailJob.php             # Send email async
├── ProcessAttachmentJob.php     # Download/store attachments
├── IndexEmailJob.php            # Index for search
└── CleanupOldEmailsJob.php      # Archive/delete old emails
```

#### 4. Models
```php
app/Models/
├── User.php
├── EmailAccount.php    # IMAP/SMTP connection details
├── Email.php           # Email metadata
├── EmailThread.php     # Conversation grouping
├── Folder.php          # Inbox, Sent, etc.
├── Label.php           # User-defined tags
├── Attachment.php      # File metadata
├── Contact.php         # Email contacts
└── Filter.php          # Email rules
```

#### 5. Events & Listeners
```php
app/Events/
├── EmailReceived.php
├── EmailSent.php
├── AccountSynced.php
└── NewEmailAvailable.php

app/Listeners/
├── NotifyUserOfNewEmail.php
├── UpdateEmailCache.php
└── BroadcastEmailEvent.php
```

### Frontend Components

```
resources/js/
├── pages/
│   ├── Dashboard.vue               # Main email interface
│   ├── Compose.vue                 # Email composition
│   ├── EmailShow.vue               # Single email view
│   ├── accounts/
│   │   ├── Index.vue               # Manage accounts
│   │   ├── Create.vue              # Add account
│   │   └── Edit.vue                # Edit account
│   ├── settings/
│   │   ├── Profile.vue
│   │   ├── Appearance.vue
│   │   └── Filters.vue
│   └── auth/                       # Existing auth pages
├── components/
│   ├── email/
│   │   ├── EmailList.vue           # Inbox/folder list
│   │   ├── EmailListItem.vue       # Single email row
│   │   ├── EmailThread.vue         # Threaded conversation
│   │   ├── EmailViewer.vue         # Email content display
│   │   └── EmailComposer.vue       # Rich text editor
│   ├── sidebar/
│   │   ├── AccountSidebar.vue      # Account switcher
│   │   ├── FolderList.vue          # Folders & labels
│   │   └── QuickActions.vue        # Compose, search
│   ├── ui/                         # Existing Reka UI components
│   └── layout/
│       ├── AppLayout.vue
│       └── MailLayout.vue
├── composables/
│   ├── useEmail.ts                 # Email operations
│   ├── useEmailAccount.ts          # Account management
│   ├── useEmailSync.ts             # Real-time sync
│   ├── useCompose.ts               # Composition state
│   └── useSearch.ts                # Search functionality
└── types/
    ├── email.ts
    ├── account.ts
    └── ...
```

---

## Database Design

### Entity Relationship Diagram

```
┌─────────────────┐
│     users       │
├─────────────────┤
│ id              │──┐
│ name            │  │
│ email           │  │
│ password        │  │
│ created_at      │  │
│ updated_at      │  │
└─────────────────┘  │
                     │
         ┌───────────┘
         │
         │ 1:N
         │
┌────────▼───────────┐
│  email_accounts    │
├────────────────────┤
│ id                 │──┐
│ user_id (FK)       │  │
│ name               │  │
│ email              │  │
│ imap_host          │  │
│ imap_port          │  │
│ imap_encryption    │  │
│ imap_username      │  │
│ imap_password_enc  │  │  (encrypted)
│ smtp_host          │  │
│ smtp_port          │  │
│ smtp_encryption    │  │
│ smtp_username      │  │
│ smtp_password_enc  │  │  (encrypted)
│ oauth_provider     │  │
│ oauth_token_enc    │  │  (encrypted)
│ last_synced_at     │  │
│ is_default         │  │
│ created_at         │  │
│ updated_at         │  │
└────────────────────┘  │
                        │
            ┌───────────┘
            │
            │ 1:N
            │
┌───────────▼─────────────┐       ┌──────────────────┐
│       folders           │       │   email_threads  │
├─────────────────────────┤       ├──────────────────┤
│ id                      │──┐    │ id               │──┐
│ email_account_id (FK)   │  │    │ subject          │  │
│ name                    │  │    │ participants     │  │
│ type (enum)             │  │    │ created_at       │  │
│ remote_name             │  │    │ updated_at       │  │
│ created_at              │  │    └──────────────────┘  │
│ updated_at              │  │                           │
└─────────────────────────┘  │                           │
                             │                           │
                 ┌───────────┘                           │
                 │                                       │
                 │ 1:N                             1:N   │
                 │                                       │
┌────────────────▼────────────────────────────────────────┐
│                    emails                               │
├─────────────────────────────────────────────────────────┤
│ id                                                      │
│ email_account_id (FK)                                   │
│ thread_id (FK, nullable)                                │
│ folder_id (FK)                                          │
│ uid                        # IMAP UID                   │
│ message_id                 # RFC 5322 Message-ID        │
│ in_reply_to                                             │
│ references                                              │
│ from_address                                            │
│ from_name                                               │
│ to (JSON)                                               │
│ cc (JSON)                                               │
│ bcc (JSON)                                              │
│ reply_to (JSON)                                         │
│ subject                                                 │
│ body_text                  # Plain text version         │
│ body_html                  # HTML version               │
│ snippet                    # First 200 chars            │
│ date                       # Email date                 │
│ size                       # Bytes                      │
│ is_read                                                 │
│ is_starred                                              │
│ is_draft                                                │
│ has_attachments                                         │
│ created_at                                              │
│ updated_at                                              │
└──────────────┬──────────────────────────────────────────┘
               │
               │ 1:N
               │
┌──────────────▼──────────┐
│      attachments        │
├─────────────────────────┤
│ id                      │
│ email_id (FK)           │
│ filename                │
│ content_type            │
│ size                    │
│ storage_path            │  # S3 path
│ content_id              │  # For inline images
│ is_inline               │
│ created_at              │
│ updated_at              │
└─────────────────────────┘

┌─────────────────────────┐       ┌─────────────────────────┐
│        labels           │       │      email_label        │
├─────────────────────────┤       ├─────────────────────────┤
│ id                      │──────▶│ email_id (FK)           │
│ user_id (FK)            │       │ label_id (FK)           │
│ name                    │       └─────────────────────────┘
│ color                   │
│ created_at              │
│ updated_at              │
└─────────────────────────┘

┌─────────────────────────┐
│       contacts          │
├─────────────────────────┤
│ id                      │
│ user_id (FK)            │
│ email                   │
│ name                    │
│ avatar_url              │
│ last_contacted_at       │
│ contact_count           │
│ created_at              │
│ updated_at              │
└─────────────────────────┘

┌─────────────────────────┐
│        filters          │
├─────────────────────────┤
│ id                      │
│ email_account_id (FK)   │
│ name                    │
│ conditions (JSON)       │
│ actions (JSON)          │
│ is_active               │
│ order                   │
│ created_at              │
│ updated_at              │
└─────────────────────────┘
```

### Key Database Tables

#### users
Standard Laravel users table with Fortify authentication.

#### email_accounts
```sql
CREATE TABLE email_accounts (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,

    -- IMAP settings
    imap_host VARCHAR(255) NOT NULL,
    imap_port INT NOT NULL,
    imap_encryption VARCHAR(10), -- 'ssl', 'tls', null
    imap_username VARCHAR(255) NOT NULL,
    imap_password_encrypted TEXT NOT NULL,

    -- SMTP settings
    smtp_host VARCHAR(255) NOT NULL,
    smtp_port INT NOT NULL,
    smtp_encryption VARCHAR(10),
    smtp_username VARCHAR(255) NOT NULL,
    smtp_password_encrypted TEXT NOT NULL,

    -- OAuth (for Gmail, Outlook)
    oauth_provider VARCHAR(50), -- 'google', 'microsoft'
    oauth_token_encrypted TEXT,
    oauth_refresh_token_encrypted TEXT,
    oauth_expires_at TIMESTAMP,

    -- Metadata
    is_default BOOLEAN DEFAULT FALSE,
    last_synced_at TIMESTAMP,
    sync_enabled BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_user_id (user_id),
    INDEX idx_email (email)
);
```

#### emails
```sql
CREATE TABLE emails (
    id BIGSERIAL PRIMARY KEY,
    email_account_id BIGINT NOT NULL REFERENCES email_accounts(id) ON DELETE CASCADE,
    thread_id BIGINT REFERENCES email_threads(id) ON DELETE SET NULL,
    folder_id BIGINT REFERENCES folders(id) ON DELETE CASCADE,

    -- Email identifiers
    uid VARCHAR(255) NOT NULL, -- IMAP UID
    message_id VARCHAR(998) NOT NULL, -- RFC 5322 limit
    in_reply_to VARCHAR(998),
    references TEXT,

    -- Email headers
    from_address VARCHAR(255) NOT NULL,
    from_name VARCHAR(255),
    to_addresses JSON NOT NULL,
    cc_addresses JSON,
    bcc_addresses JSON,
    reply_to_addresses JSON,
    subject TEXT,

    -- Email body
    body_text TEXT,
    body_html TEXT,
    snippet VARCHAR(200),

    -- Metadata
    date TIMESTAMP NOT NULL,
    size INT,
    is_read BOOLEAN DEFAULT FALSE,
    is_starred BOOLEAN DEFAULT FALSE,
    is_draft BOOLEAN DEFAULT FALSE,
    has_attachments BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_account_folder (email_account_id, folder_id),
    INDEX idx_thread (thread_id),
    INDEX idx_date (date DESC),
    INDEX idx_uid (email_account_id, uid),
    INDEX idx_message_id (message_id),
    INDEX idx_is_read (is_read),

    UNIQUE (email_account_id, uid)
);

-- Full-text search index
CREATE INDEX idx_emails_search ON emails
USING GIN (to_tsvector('english', coalesce(subject, '') || ' ' || coalesce(body_text, '')));
```

#### email_threads
```sql
CREATE TABLE email_threads (
    id BIGSERIAL PRIMARY KEY,
    subject VARCHAR(500) NOT NULL,
    participants JSON NOT NULL, -- [{email, name}]
    last_message_at TIMESTAMP NOT NULL,
    message_count INT DEFAULT 1,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_last_message (last_message_at DESC)
);
```

#### folders
```sql
CREATE TABLE folders (
    id BIGSERIAL PRIMARY KEY,
    email_account_id BIGINT NOT NULL REFERENCES email_accounts(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL, -- 'inbox', 'sent', 'drafts', 'trash', 'spam', 'custom'
    remote_name VARCHAR(255), -- IMAP folder name
    unread_count INT DEFAULT 0,
    total_count INT DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_account_type (email_account_id, type),
    UNIQUE (email_account_id, remote_name)
);
```

#### attachments
```sql
CREATE TABLE attachments (
    id BIGSERIAL PRIMARY KEY,
    email_id BIGINT NOT NULL REFERENCES emails(id) ON DELETE CASCADE,
    filename VARCHAR(255) NOT NULL,
    content_type VARCHAR(127) NOT NULL,
    size INT NOT NULL,
    storage_path VARCHAR(500) NOT NULL, -- S3 key
    content_id VARCHAR(255), -- For inline images
    is_inline BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_email_id (email_id)
);
```

#### labels
```sql
CREATE TABLE labels (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7), -- Hex color

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    UNIQUE (user_id, name)
);

CREATE TABLE email_label (
    email_id BIGINT NOT NULL REFERENCES emails(id) ON DELETE CASCADE,
    label_id BIGINT NOT NULL REFERENCES labels(id) ON DELETE CASCADE,

    PRIMARY KEY (email_id, label_id)
);
```

#### contacts
```sql
CREATE TABLE contacts (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    avatar_url VARCHAR(500),
    last_contacted_at TIMESTAMP,
    contact_count INT DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    UNIQUE (user_id, email),
    INDEX idx_last_contacted (user_id, last_contacted_at DESC)
);
```

#### filters
```sql
CREATE TABLE filters (
    id BIGSERIAL PRIMARY KEY,
    email_account_id BIGINT NOT NULL REFERENCES email_accounts(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    conditions JSON NOT NULL, -- [{field: 'from', operator: 'contains', value: '@example.com'}]
    actions JSON NOT NULL, -- [{action: 'move_to_folder', folder_id: 123}]
    is_active BOOLEAN DEFAULT TRUE,
    order INT DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_account_active (email_account_id, is_active, order)
);
```

---

## API Design

### RESTful API Endpoints

#### Email Accounts
```
GET    /api/accounts                 - List all accounts
POST   /api/accounts                 - Add new account
GET    /api/accounts/{id}            - Get account details
PUT    /api/accounts/{id}            - Update account
DELETE /api/accounts/{id}            - Remove account
POST   /api/accounts/{id}/test       - Test connection
POST   /api/accounts/{id}/sync       - Trigger manual sync
GET    /api/accounts/{id}/folders    - List folders
```

#### Emails
```
GET    /api/emails                   - List emails (paginated, filtered)
GET    /api/emails/{id}              - Get single email
DELETE /api/emails/{id}              - Delete email
PATCH  /api/emails/{id}/read         - Mark as read/unread
PATCH  /api/emails/{id}/star         - Star/unstar
POST   /api/emails/{id}/move         - Move to folder
POST   /api/emails/{id}/labels       - Add/remove labels
```

#### Compose & Send
```
POST   /api/compose                  - Create draft
PUT    /api/compose/{id}             - Update draft
POST   /api/compose/{id}/send        - Send email
POST   /api/compose/send             - Compose & send in one
DELETE /api/compose/{id}             - Delete draft
POST   /api/attachments              - Upload attachment
```

#### Search
```
GET    /api/search                   - Search emails
  ?q=query
  &account_id=123
  &folder_id=456
  &from=email@example.com
  &has_attachment=true
  &date_from=2025-01-01
  &date_to=2025-12-31
```

#### Folders & Labels
```
GET    /api/folders                  - List folders
POST   /api/folders                  - Create custom folder
PUT    /api/folders/{id}             - Rename folder
DELETE /api/folders/{id}             - Delete folder

GET    /api/labels                   - List labels
POST   /api/labels                   - Create label
PUT    /api/labels/{id}              - Update label
DELETE /api/labels/{id}              - Delete label
```

#### Contacts
```
GET    /api/contacts                 - List contacts
GET    /api/contacts/search          - Autocomplete search
POST   /api/contacts                 - Create contact
PUT    /api/contacts/{id}            - Update contact
DELETE /api/contacts/{id}            - Delete contact
```

#### Filters
```
GET    /api/filters                  - List filters
POST   /api/filters                  - Create filter
PUT    /api/filters/{id}             - Update filter
DELETE /api/filters/{id}             - Delete filter
PATCH  /api/filters/{id}/toggle      - Enable/disable filter
```

### Inertia.js Routes (Server-Side Rendering)

```php
// Web routes (Inertia responses)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mail', [EmailController::class, 'index'])->name('mail.index');
    Route::get('/mail/compose', [ComposeController::class, 'create'])->name('mail.compose');
    Route::get('/mail/{email}', [EmailController::class, 'show'])->name('mail.show');
    Route::get('/accounts', [EmailAccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [EmailAccountController::class, 'create'])->name('accounts.create');
});
```

---

## Email Integration

### IMAP Integration

#### Connection Flow
```php
1. User provides: email, password (or OAuth token)
2. AutoConfigService attempts to detect provider settings
3. Test IMAP connection
4. Store encrypted credentials in database
5. Queue initial sync job
```

#### Email Syncing Strategy

**Initial Sync**
```
1. Connect to IMAP server
2. List all folders
3. For each folder:
   - Fetch UIDs of all messages (or last 1000)
   - Queue jobs to fetch message details in batches of 50
4. Process each batch:
   - Fetch headers + body
   - Parse email content
   - Extract attachments (store references, download async)
   - Build threads
   - Index for search
```

**Incremental Sync (Every 5 minutes)**
```
1. Connect to IMAP server
2. For each folder:
   - Use IMAP IDLE or poll for new UIDs
   - Fetch only new messages since last sync
3. Process new messages
4. Broadcast real-time event to frontend
```

#### IMAP Commands Used
```
- LOGIN / AUTHENTICATE (OAuth)
- SELECT folder
- SEARCH (to get UIDs)
- FETCH (headers, body, flags)
- IDLE (push notifications, if supported)
- STORE (mark as read, flagged)
- MOVE/COPY (move between folders)
- EXPUNGE (delete permanently)
```

### SMTP Integration

#### Sending Flow
```php
1. User composes email
2. Save as draft (optional)
3. On send:
   - Validate recipients
   - Queue SendEmailJob
4. Job processes:
   - Connect to SMTP server
   - Authenticate
   - Send message
   - Save to "Sent" folder via IMAP APPEND
   - Update draft status
   - Broadcast success event
```

#### SMTP Error Handling
```
- Connection timeout → Retry 3x with exponential backoff
- Authentication failure → Notify user, mark account as invalid
- Recipient rejection → Return error to user immediately
- Rate limiting → Queue with delay
```

### OAuth Integration (Gmail, Microsoft)

#### OAuth Providers Supported
- Google (Gmail)
- Microsoft (Outlook, Office 365)

#### OAuth Flow
```
1. User clicks "Connect Gmail"
2. Redirect to Google OAuth consent screen
3. User authorizes
4. Receive authorization code
5. Exchange for access token + refresh token
6. Store encrypted tokens
7. Use access token for IMAP/SMTP with OAuth SASL
8. Refresh token before expiry
```

### Auto-Configuration

Provider detection based on email domain:

```php
// Example provider configs
[
    'gmail.com' => [
        'imap' => ['host' => 'imap.gmail.com', 'port' => 993, 'encryption' => 'ssl'],
        'smtp' => ['host' => 'smtp.gmail.com', 'port' => 465, 'encryption' => 'ssl'],
        'oauth' => 'google'
    ],
    'outlook.com' => [
        'imap' => ['host' => 'outlook.office365.com', 'port' => 993, 'encryption' => 'ssl'],
        'smtp' => ['host' => 'smtp.office365.com', 'port' => 587, 'encryption' => 'tls'],
        'oauth' => 'microsoft'
    ],
    // Fallback: use Autoconfig/Autodiscover protocols
]
```

---

## Security Architecture

### Authentication & Authorization
- **Laravel Fortify**: Registration, login, 2FA
- **Laravel Sanctum**: API token authentication (for future mobile apps)
- **Session-based auth**: For web application

### Data Encryption

#### At Rest
```
- Email credentials: Encrypted with Laravel's encryption (AES-256-CBC)
- OAuth tokens: Encrypted
- Email bodies: NOT encrypted (stored in plain text for search)
- Attachments: Optionally encrypted (future feature)
```

#### In Transit
```
- HTTPS only (enforced)
- IMAP/SMTP over SSL/TLS
- WebSocket over WSS
```

### Rate Limiting

```php
// Email sending limits
- Per user: 100 emails/hour, 500/day
- Per account: 50 emails/hour, 200/day

// API limits
- Authenticated users: 120 requests/minute
- Guests: 10 requests/minute

// IMAP sync limits
- Respect provider rate limits
- Gmail: ~15 requests/second
- Outlook: ~30 requests/minute
```

### CSRF Protection
- Laravel built-in CSRF protection for all POST/PUT/DELETE requests
- Inertia.js automatically handles CSRF tokens

### XSS Protection
- Email HTML sanitization (strip scripts, forms)
- CSP headers to prevent inline scripts
- Vue's automatic template escaping

### Spam Prevention
- Email verification required
- Rate limiting on sending
- Monitor for abuse patterns
- Report abuse mechanism

---

## Performance & Scalability

### Caching Strategy

```
┌─────────────────────────────────────────────────┐
│              Cache Layers                       │
├─────────────────────────────────────────────────┤
│ 1. Application Cache (Redis)                    │
│    - User sessions (30 min TTL)                 │
│    - Email metadata (5 min TTL)                 │
│    - Folder unread counts (1 min TTL)           │
│    - Contact autocomplete (15 min TTL)          │
│                                                  │
│ 2. Database Query Cache                         │
│    - Common queries cached by Laravel           │
│                                                  │
│ 3. CDN Cache (Cloudflare)                       │
│    - Static assets (JS, CSS, images)            │
│    - Long-term cache with cache busting         │
└─────────────────────────────────────────────────┘
```

### Queue Architecture

```
┌─────────────────────────────────────────────────┐
│               Queue Workers                     │
├─────────────────────────────────────────────────┤
│ High Priority Queue (2 workers)                 │
│   - SendEmailJob (immediate)                    │
│   - MarkAsReadJob (user-facing)                 │
│                                                  │
│ Default Queue (4 workers)                       │
│   - FetchNewEmailsJob                           │
│   - ProcessAttachmentJob                        │
│   - IndexEmailJob                               │
│                                                  │
│ Low Priority Queue (2 workers)                  │
│   - SyncEmailAccountJob (full sync)             │
│   - CleanupOldEmailsJob                         │
│   - UpdateContactsJob                           │
└─────────────────────────────────────────────────┘
```

### Database Optimization

```sql
-- Partitioning (for large deployments)
-- Partition emails table by date (monthly)
CREATE TABLE emails_2025_01 PARTITION OF emails
FOR VALUES FROM ('2025-01-01') TO ('2025-02-01');

-- Indexes (defined in schema above)
-- Covering indexes for common queries
CREATE INDEX idx_inbox_unread
ON emails (email_account_id, folder_id, is_read, date DESC)
WHERE is_read = FALSE;
```

### Real-Time Updates

```
┌─────────────────────────────────────────────────┐
│          Real-Time Architecture                 │
├─────────────────────────────────────────────────┤
│ Events:                                         │
│   - NewEmailReceived                            │
│   - EmailSent                                   │
│   - EmailRead                                   │
│   - FolderCountUpdated                          │
│                                                  │
│ Broadcast via Laravel Reverb/Soketi:            │
│   - Private channel: user.{userId}              │
│   - Frontend listens via Laravel Echo           │
│   - Optimistic UI updates + event confirmation  │
└─────────────────────────────────────────────────┘
```

### Scalability Considerations

**Horizontal Scaling**
- Stateless application servers (scale with load balancer)
- Separate queue workers (auto-scale based on queue depth)
- Database read replicas for queries

**Vertical Scaling**
- PostgreSQL connection pooling (PgBouncer)
- Redis cluster for cache
- S3 for attachments (infinite storage)

**Performance Targets**
- Support 10,000 concurrent users
- Process 1,000 emails/second
- Sub-second page loads (P95)

---

## Infrastructure

### Development Environment
```
Docker Compose:
  - PHP 8.2 (FPM)
  - PostgreSQL 15
  - Redis 7
  - Meilisearch (optional)
  - MailHog (email testing)
  - Soketi (WebSockets)

or

Laravel Sail (default)
```

### Production Deployment

```
┌─────────────────────────────────────────────────┐
│          Production Stack                       │
├─────────────────────────────────────────────────┤
│ Load Balancer (Cloudflare + Nginx)             │
│   ├── SSL Termination                           │
│   ├── DDoS protection                           │
│   └── CDN for static assets                     │
│                                                  │
│ Application Servers (2+ instances)              │
│   ├── Laravel PHP-FPM                           │
│   ├── Nginx                                     │
│   └── Supervisor (queue workers)                │
│                                                  │
│ Database (Managed PostgreSQL)                   │
│   ├── Primary (write)                           │
│   └── Read replicas (2x)                        │
│                                                  │
│ Cache & Queue (Managed Redis)                   │
│   ├── Redis Cluster                             │
│   └── Persistent storage                        │
│                                                  │
│ Object Storage (S3-compatible)                  │
│   └── Attachments, avatars                      │
│                                                  │
│ Search (Meilisearch Cloud)                      │
│   └── Full-text email search                    │
│                                                  │
│ WebSockets (Laravel Reverb or Soketi)           │
│   └── Real-time updates                         │
│                                                  │
│ Monitoring                                      │
│   ├── Laravel Pulse (app metrics)               │
│   ├── Sentry (error tracking)                   │
│   └── Plausible (analytics)                     │
└─────────────────────────────────────────────────┘
```

### Deployment Pipeline

```
GitHub → GitHub Actions → Laravel Forge → Production

CI/CD Steps:
1. Run tests (Pest)
2. Run linters (Pint, ESLint)
3. Build frontend assets (Vite)
4. Deploy to staging
5. Run smoke tests
6. Deploy to production (zero-downtime)
```

### Backup Strategy
- **Database**: Automated daily backups (30-day retention)
- **Redis**: Persistence enabled (AOF + RDB)
- **Attachments**: S3 versioning enabled
- **Application Code**: Git repository

---

## Monitoring & Observability

### Metrics to Track
```
Application:
  - Response time (P50, P95, P99)
  - Error rate
  - Queue depth
  - Active users

Email:
  - Emails synced/hour
  - Emails sent/hour
  - IMAP connection failures
  - SMTP send failures
  - Average sync time

Infrastructure:
  - CPU/Memory usage
  - Database connections
  - Redis memory usage
  - Disk I/O
```

### Logging
```
Channels:
  - daily: Application logs (Laravel.log)
  - stderr: Production errors (streamed to monitoring)
  - email: Critical errors (email admin)

Log Levels:
  - Emergency: System down
  - Alert: Immediate action needed
  - Error: Runtime errors
  - Warning: Deprecated usage
  - Info: Interesting events
  - Debug: Detailed debugging (dev only)
```

### Alerting
```
Alerts:
  - Error rate > 5% → Slack + Email
  - Queue depth > 10,000 → Scale workers
  - Database connection pool exhausted → Alert DBA
  - Disk space < 10% → Alert DevOps
  - IMAP sync failures > 20% → Investigate provider issues
```

---

## Future Enhancements

### Phase 2
- Mobile apps (React Native or Flutter)
- Desktop apps (Tauri)
- Browser extension (compose from any page)
- Calendar integration
- Advanced filters with AI
- Email templates library

### Phase 3
- Team collaboration features
- Shared inboxes
- Email analytics dashboard
- Custom email hosting (@maylbox.co)
- White-label solution for enterprises
- Plugin/extension system

---

## Conclusion

This architecture provides a solid foundation for Maylbox.co to deliver a fast, reliable, and delightful email experience. The system is designed to scale from MVP to serving millions of users while maintaining the core principle: **minimal clicks from signup to first email sent**.

Key strengths:
- ✅ Modern tech stack (Laravel 12 + Vue 3)
- ✅ Battle-tested email protocols (IMAP/SMTP)
- ✅ Performance-first (caching, queues, real-time)
- ✅ Secure by design (encryption, OAuth, rate limiting)
- ✅ Scalable architecture (horizontal scaling, managed services)
