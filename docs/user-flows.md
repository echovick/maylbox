# Maylbox.co - User Flows

## Table of Contents
1. [The "5-Click" Onboarding Flow](#the-5-click-onboarding-flow)
2. [Email Account Connection Flows](#email-account-connection-flows)
3. [Email Reading Flow](#email-reading-flow)
4. [Email Composition Flow](#email-composition-flow)
5. [Email Organization Flows](#email-organization-flows)
6. [Search Flow](#search-flow)
7. [Settings & Management Flows](#settings--management-flows)
8. [Edge Cases & Error Flows](#edge-cases--error-flows)

---

## The "5-Click" Onboarding Flow

**Goal**: Get users from landing page to sending their first email in 5 clicks or less.

### Primary Path (New User)

```
┌─────────────────────────────────────────────────────────────────┐
│                      LANDING PAGE                               │
│  "Welcome to Maylbox - Email, but better"                       │
│                                                                  │
│  [Get Started] (Click 1)                                        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                  SIGNUP PAGE (Simplified)                       │
│                                                                  │
│  Email:    [_________________________]                          │
│  Password: [_________________________]                          │
│                                                                  │
│  [Create Account & Continue] (Click 2)                          │
│                                                                  │
│  (Skip email verification for speed - verify later)             │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│              ACCOUNT CONNECTION (Smart Onboarding)              │
│                                                                  │
│  "Connect your email account"                                   │
│                                                                  │
│  Your Email: [user@gmail.com] (pre-filled from signup)          │
│  Password:   [_________________________]                        │
│                                                                  │
│  OR                                                              │
│                                                                  │
│  [Continue with Google] [Continue with Microsoft]               │
│                                                                  │
│  (Settings auto-detected based on email domain)                 │
│                                                                  │
│  [Connect Account] (Click 3)                                    │
│                                                                  │
│  ↓ Background: Test IMAP/SMTP connection                        │
│  ↓ Background: Start initial email sync                         │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    INBOX (First View)                           │
│                                                                  │
│  ┌──────────────┐  "Your inbox is syncing..."                  │
│  │ [+ Compose]  │◄─ Prominent, always visible (Click 4)         │
│  └──────────────┘                                               │
│                                                                  │
│  📨 Inbox (Loading first 50 emails...)                          │
│     ↓ New emails appear in real-time                            │
│     ↓ User can start reading immediately                        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    COMPOSE EMAIL                                │
│                                                                  │
│  To:      [_________________________] (auto-suggest from inbox) │
│  Subject: [_________________________]                           │
│  Body:    [Rich text editor - ready to type]                    │
│                                                                  │
│  [Send] (Click 5) ◄─ GOAL ACHIEVED!                             │
│                                                                  │
│  ↓ Email queued for sending                                     │
│  ↓ Optimistic UI: Email appears in Sent immediately             │
│  ↓ Background: Send via SMTP                                    │
│  ↓ Toast notification: "Email sent!" ✅                          │
└─────────────────────────────────────────────────────────────────┘
```

### Alternative Path (Existing User)

```
LANDING PAGE
    │
    ▼
[Login] (Click 1)
    │
    ▼
LOGIN PAGE
    │
    ▼
[Enter credentials & Submit] (Click 2)
    │
    ▼
INBOX (immediately loads with cached data)
    │
    ▼
[Compose] (Click 3)
    │
    ▼
[Send] (Click 4)

Total: 4 clicks for returning users!
```

---

## Email Account Connection Flows

### Flow 1: Gmail Account (OAuth)

```
┌─────────────────────────────────────────────────────────────────┐
│              ADD EMAIL ACCOUNT PAGE                             │
│                                                                  │
│  Email: [myemail@gmail.com]                                     │
│                                                                  │
│  (Auto-detected: Gmail)                                         │
│                                                                  │
│  [Continue with Google OAuth] ◄─ Recommended                    │
│                                                                  │
│  OR                                                              │
│                                                                  │
│  [Use App Password] (for users who prefer it)                   │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│              GOOGLE OAUTH CONSENT SCREEN                        │
│  (Handled by Google)                                            │
│                                                                  │
│  "Maylbox wants to access your Gmail"                           │
│  - Read, compose, send emails                                   │
│  - Manage labels and settings                                   │
│                                                                  │
│  [Allow]  [Deny]                                                │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼ (if Allow)
┌─────────────────────────────────────────────────────────────────┐
│              ACCOUNT CONNECTED                                  │
│                                                                  │
│  ✅ Successfully connected myemail@gmail.com                    │
│                                                                  │
│  [Start Syncing] [Go to Inbox]                                  │
│                                                                  │
│  Background:                                                    │
│    ↓ Exchange OAuth code for tokens                             │
│    ↓ Store encrypted refresh token                              │
│    ↓ Queue SyncEmailAccountJob                                  │
│    ↓ Fetch folders and recent emails                            │
└─────────────────────────────────────────────────────────────────┘
```

### Flow 2: Generic IMAP Account

```
┌─────────────────────────────────────────────────────────────────┐
│              ADD EMAIL ACCOUNT PAGE                             │
│                                                                  │
│  Email:    [user@custom-domain.com]                             │
│                                                                  │
│  (Unknown provider - attempting auto-discovery...)              │
│                                                                  │
│  ↓ Tries Autoconfig (Mozilla Thunderbird protocol)              │
│  ↓ Tries Autodiscover (Microsoft Exchange protocol)             │
│  ↓ Falls back to common settings                                │
│                                                                  │
│  ✅ Settings found!                                              │
│                                                                  │
│  Password: [_________________________]                          │
│                                                                  │
│  [Advanced Settings] (collapsed by default)                     │
│    IMAP: mail.custom-domain.com:993 (SSL)                       │
│    SMTP: mail.custom-domain.com:465 (SSL)                       │
│                                                                  │
│  [Connect Account]                                              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│              TESTING CONNECTION                                 │
│                                                                  │
│  ⏳ Testing IMAP connection...  ✅                               │
│  ⏳ Testing SMTP connection...  ✅                               │
│  ⏳ Fetching folder list...     ✅                               │
│                                                                  │
│  All tests passed!                                              │
│                                                                  │
│  [Continue to Inbox]                                            │
└─────────────────────────────────────────────────────────────────┘
```

### Flow 3: Manual Configuration

```
┌─────────────────────────────────────────────────────────────────┐
│         MANUAL ACCOUNT CONFIGURATION                            │
│  (For advanced users or when auto-discovery fails)              │
│                                                                  │
│  Account Name: [Work Email]                                     │
│  Email:        [you@company.com]                                │
│                                                                  │
│  ━━━ IMAP Settings (Incoming) ━━━                               │
│  Host:       [imap.company.com]                                 │
│  Port:       [993]                                              │
│  Encryption: [SSL ▼]                                            │
│  Username:   [you@company.com]                                  │
│  Password:   [_________________________]                        │
│                                                                  │
│  ━━━ SMTP Settings (Outgoing) ━━━                               │
│  Host:       [smtp.company.com]                                 │
│  Port:       [465]                                              │
│  Encryption: [SSL ▼]                                            │
│  Username:   [you@company.com]                                  │
│  Password:   [_________________________]                        │
│                                                                  │
│  [Test Connection] [Save]                                       │
└─────────────────────────────────────────────────────────────────┘
```

---

## Email Reading Flow

### Flow: Open and Read Email

```
┌─────────────────────────────────────────────────────────────────┐
│                      INBOX VIEW                                 │
│                                                                  │
│  📥 Inbox (234 unread)                                          │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ ● John Doe                           2 minutes ago         │ │
│  │   RE: Project Update                                       │ │
│  │   Here are the latest changes to the design...             │ │
│  ├────────────────────────────────────────────────────────────┤ │
│  │   Sarah Smith                        1 hour ago            │ │◄─ Click to open
│  │   Meeting Tomorrow at 10am                                 │ │
│  │   Don't forget to bring the reports...                     │ │
│  ├────────────────────────────────────────────────────────────┤ │
│  │   Newsletter                         Yesterday             │ │
│  │   Weekly Digest: 5 Articles You Missed                     │ │
│  └────────────────────────────────────────────────────────────┘ │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                   EMAIL DETAIL VIEW                             │
│                                                                  │
│  ← Back to Inbox                                                │
│                                                                  │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│                                                                  │
│  From: Sarah Smith <sarah@company.com>                          │
│  To: Me                                                         │
│  Date: Jan 15, 2025 9:30 AM                                     │
│                                                                  │
│  Subject: Meeting Tomorrow at 10am                              │
│                                                                  │
│  [Reply] [Reply All] [Forward] [⋮ More]                         │
│                                                                  │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│                                                                  │
│  Hi there,                                                      │
│                                                                  │
│  Don't forget about our meeting tomorrow at 10am.               │
│  Please bring the Q4 reports.                                   │
│                                                                  │
│  Thanks!                                                        │
│  Sarah                                                          │
│                                                                  │
│  ↓ Automatically marked as "read" after 2 seconds               │
│  ↓ Background: IMAP STORE +FLAGS (\Seen)                        │
└─────────────────────────────────────────────────────────────────┘

User Actions Available:
    │
    ├─ [Reply] → Opens composer with "To: Sarah", quoted text
    │
    ├─ [Reply All] → Includes all recipients
    │
    ├─ [Forward] → Opens composer without recipients
    │
    ├─ [⋮ More] →
    │       ├─ Mark as Unread
    │       ├─ Star
    │       ├─ Move to Folder
    │       ├─ Add Label
    │       ├─ Delete
    │       └─ Print
    │
    └─ [← Back] → Returns to inbox
```

### Flow: Reading Threaded Conversation

```
┌─────────────────────────────────────────────────────────────────┐
│                  THREAD VIEW (4 messages)                       │
│                                                                  │
│  ← Back to Inbox                                                │
│                                                                  │
│  Subject: RE: Project Update                                    │
│  Participants: You, John Doe, Sarah Smith                       │
│                                                                  │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│  ▼ You • Jan 12, 2025 2:00 PM                                   │
│                                                                  │
│    Hey team, here's the initial project update...               │
│                                                                  │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│  ▶ John Doe • Jan 12, 2025 3:15 PM (Click to expand)            │
│                                                                  │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│  ▶ Sarah Smith • Jan 13, 2025 10:00 AM                          │
│                                                                  │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│  ▼ John Doe • Jan 15, 2025 9:00 AM (Most recent, auto-expanded)│
│                                                                  │
│    Thanks for the feedback. I've made the following changes:    │
│    1. Updated the design mockups                                │
│    2. Fixed the responsive layout issues                        │
│                                                                  │
│    📎 design_v2.pdf (2.4 MB)                                    │
│    📎 mockups.sketch (1.8 MB)                                   │
│                                                                  │
│  [Reply to Thread]                                              │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Email Composition Flow

### Flow: Compose New Email

```
┌─────────────────────────────────────────────────────────────────┐
│                      ANYWHERE IN APP                            │
│                                                                  │
│  [+ Compose] ◄─ Always visible in sidebar/header                │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                   COMPOSE WINDOW (Modal/Slide-up)               │
│                                                                  │
│  New Message                                    [−] [□] [×]      │
│                                                                  │
│  From: [myemail@gmail.com ▼] (if multiple accounts)             │
│                                                                  │
│  To:   [_________________________] ◄─ Autocomplete from contacts│
│        @sarah → Sarah Smith <sarah@company.com> ✓               │
│                                                                  │
│  Cc:   [Add Cc] [Add Bcc]                                       │
│                                                                  │
│  Subject: [_________________________]                           │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ [B] [I] [U] [Link] [Bullet] [Number] [Attach] [Emoji]   │   │
│  ├─────────────────────────────────────────────────────────┤   │
│  │                                                          │   │
│  │ (Rich text editor - Tiptap)                             │   │
│  │ Cursor ready to type...                                 │   │
│  │                                                          │   │
│  │                                                          │   │
│  │                                                          │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                  │
│  📎 No attachments                                              │
│                                                                  │
│  [Send] [Save Draft] [Discard]                                  │
│                                                                  │
│  ↓ Auto-saves draft every 10 seconds                            │
└─────────────────────────────────────────────────────────────────┘

User Actions:
    │
    ├─ Type content → Auto-saved as draft
    │
    ├─ [Attach] →
    │       ├─ Click to browse files
    │       ├─ Drag & drop anywhere in compose window
    │       └─ Paste from clipboard (images)
    │
    ├─ [Send] →
    │       ↓ Validate recipients (check format)
    │       ↓ Queue SendEmailJob
    │       ↓ Optimistic UI: Show "Sending..." toast
    │       ↓ Close compose window
    │       ↓ Background: Send via SMTP
    │       ↓ Success: "Email sent!" ✅
    │       ↓ Move to Sent folder
    │
    ├─ [Save Draft] →
    │       ↓ Save to Drafts folder (IMAP APPEND)
    │       ↓ Close compose window
    │
    ├─ [Discard] →
    │       ↓ Confirm: "Discard draft?"
    │       ↓ Delete draft if exists
    │       ↓ Close compose window
    │
    └─ [× Close] →
            ↓ If content exists: "Save draft?"
            ↓ [Save] [Discard] [Cancel]
```

### Flow: Reply to Email

```
EMAIL DETAIL VIEW
    │
    ▼
[Reply] clicked
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│                   COMPOSE WINDOW (Reply Mode)                   │
│                                                                  │
│  Reply to Sarah Smith                           [−] [□] [×]      │
│                                                                  │
│  From: [myemail@gmail.com ▼]                                    │
│                                                                  │
│  To:   sarah@company.com                                        │
│                                                                  │
│  Subject: RE: Meeting Tomorrow at 10am (auto-prefixed)          │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ [B] [I] [U] [Link] [Bullet] [Number] [Attach] [Emoji]   │   │
│  ├─────────────────────────────────────────────────────────┤   │
│  │                                                          │   │
│  │ (Cursor here, ready to type)                            │   │
│  │                                                          │   │
│  │                                                          │   │
│  │ ━━━ Original Message ━━━                                │   │
│  │ On Jan 15, 2025, Sarah Smith wrote:                     │   │
│  │ > Don't forget about our meeting tomorrow at 10am.      │   │
│  │ > Please bring the Q4 reports.                          │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                  │
│  [Send] [Save Draft] [Discard]                                  │
└─────────────────────────────────────────────────────────────────┘

Smart features:
    ↓ In-Reply-To header set to original Message-ID
    ↓ References header includes thread history
    ↓ Proper threading maintained
```

### Flow: Forward Email

```
EMAIL DETAIL VIEW
    │
    ▼
[Forward] clicked
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│                COMPOSE WINDOW (Forward Mode)                    │
│                                                                  │
│  Forward                                        [−] [□] [×]      │
│                                                                  │
│  From: [myemail@gmail.com ▼]                                    │
│                                                                  │
│  To:   [_________________________] (empty, user fills)          │
│                                                                  │
│  Subject: FWD: Meeting Tomorrow at 10am (auto-prefixed)         │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                                                          │   │
│  │ (Add your message here)                                 │   │
│  │                                                          │   │
│  │ ━━━ Forwarded Message ━━━                               │   │
│  │ From: Sarah Smith <sarah@company.com>                   │   │
│  │ Date: Jan 15, 2025 9:30 AM                              │   │
│  │ Subject: Meeting Tomorrow at 10am                       │   │
│  │                                                          │   │
│  │ Don't forget about our meeting tomorrow at 10am.        │   │
│  │ Please bring the Q4 reports.                            │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                  │
│  📎 Attachments from original email included                    │
│                                                                  │
│  [Send] [Save Draft] [Discard]                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Email Organization Flows

### Flow: Move Email to Folder

```
EMAIL DETAIL VIEW or INBOX LIST
    │
    ▼
Right-click email or [⋮ More] menu
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│  Context Menu                                                   │
│  ┌─────────────────────┐                                        │
│  │ Mark as Unread      │                                        │
│  │ Star                │                                        │
│  │ Move to Folder...  │◄─ Click                                 │
│  │ Add Label...        │                                        │
│  │ Delete              │                                        │
│  │ Print               │                                        │
│  └─────────────────────┘                                        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│  Move to Folder (Modal)                                         │
│                                                                  │
│  Search folders: [____________]                                 │
│                                                                  │
│  Common:                                                        │
│  ○ Inbox                                                        │
│  ○ Archive                                                      │
│  ○ Trash                                                        │
│                                                                  │
│  Custom:                                                        │
│  ○ Work                                                         │
│  ○ Personal                                                     │
│  ○ Important                                                    │
│                                                                  │
│  [+ Create New Folder]                                          │
│                                                                  │
│  [Move] [Cancel]                                                │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼ Select folder & click [Move]
┌─────────────────────────────────────────────────────────────────┐
│  Email moved to "Work" folder ✅                                │
│                                                                  │
│  Background:                                                    │
│    ↓ IMAP MOVE command to server                                │
│    ↓ Update local database                                      │
│    ↓ Update folder counts                                       │
│    ↓ Broadcast real-time event                                  │
│                                                                  │
│  [Undo] (available for 5 seconds)                               │
└─────────────────────────────────────────────────────────────────┘
```

### Flow: Add Label to Email

```
EMAIL DETAIL VIEW
    │
    ▼
[Add Label] or [⋮ More] → Add Label
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│  Add Labels (Modal)                                             │
│                                                                  │
│  Search labels: [____________]                                  │
│                                                                  │
│  ☑ Important (red)                                              │
│  ☐ Follow-up (orange)                                           │
│  ☐ Waiting (yellow)                                             │
│  ☑ Personal (blue)                                              │
│  ☐ Work (green)                                                 │
│                                                                  │
│  [+ Create New Label]                                           │
│                                                                  │
│  [Apply] [Cancel]                                               │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼ [Apply]
┌─────────────────────────────────────────────────────────────────┐
│  Labels applied ✅                                              │
│                                                                  │
│  Email now shows:                                               │
│    🏷️ Important | Personal                                     │
│                                                                  │
│  Background:                                                    │
│    ↓ Insert into email_label pivot table                        │
│    ↓ Update UI immediately (optimistic)                         │
└─────────────────────────────────────────────────────────────────┘
```

### Flow: Create Email Filter

```
SETTINGS → Filters
    │
    ▼
[+ Create Filter]
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CREATE FILTER                                │
│                                                                  │
│  Filter Name: [Newsletter Auto-Archive]                         │
│                                                                  │
│  ━━━ Conditions (Match ALL) ━━━                                 │
│                                                                  │
│  When email:                                                    │
│    [From ▼] [contains ▼] [newsletter@]                          │
│    [+ Add condition]                                            │
│                                                                  │
│  ━━━ Actions ━━━                                                │
│                                                                  │
│  Then:                                                          │
│    ☑ Mark as read                                               │
│    ☑ Move to folder: [Archive ▼]                                │
│    ☐ Add label: [Select label ▼]                                │
│    ☐ Star email                                                 │
│    ☐ Delete email                                               │
│    [+ Add action]                                               │
│                                                                  │
│  ☑ Apply to existing emails (last 30 days)                      │
│                                                                  │
│  [Create Filter] [Cancel]                                       │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│  Filter created ✅                                              │
│                                                                  │
│  Processing 47 existing emails...                               │
│    ↓ Queue job to apply filter retroactively                    │
│    ↓ Future emails auto-processed on sync                       │
└─────────────────────────────────────────────────────────────────┘
```

---

## Search Flow

### Flow: Basic Search

```
┌─────────────────────────────────────────────────────────────────┐
│                      INBOX VIEW                                 │
│                                                                  │
│  🔍 [Search emails...] ◄─ Click or press "/"                    │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    SEARCH INPUT (Expanded)                      │
│                                                                  │
│  🔍 [project update_____________]                               │
│      ↑ Type query                                               │
│                                                                  │
│  Suggestions (as you type):                                     │
│    📧 "project update" in Subject                               │
│    👤 from:john@company.com                                     │
│    📅 in last 7 days                                            │
│                                                                  │
│  [Advanced Filters ▼]                                           │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼ Press Enter
┌─────────────────────────────────────────────────────────────────┐
│                    SEARCH RESULTS                               │
│                                                                  │
│  Showing 12 results for "project update"                        │
│  Searched in: All Mail                                          │
│                                                                  │
│  Filters: [From ▼] [Date Range ▼] [Has Attachment ▼]           │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ ● John Doe                           Jan 15, 2025          │ │
│  │   RE: Project Update - Final Review                        │ │
│  │   ...the latest changes to the design and timeline...      │ │
│  ├────────────────────────────────────────────────────────────┤ │
│  │   Sarah Smith                        Jan 12, 2025          │ │
│  │   Project Update Meeting Notes                             │ │
│  │   ...discussed the project update schedule and...          │ │
│  ├────────────────────────────────────────────────────────────┤ │
│  │   You                                Jan 10, 2025          │ │
│  │   Project Update - Q4 Progress                             │ │
│  │   ...sending the project update for Q4 review...           │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                  │
│  [Load More Results]                                            │
└─────────────────────────────────────────────────────────────────┘
```

### Flow: Advanced Search

```
SEARCH INPUT
    │
    ▼
[Advanced Filters ▼]
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│                  ADVANCED SEARCH FILTERS                        │
│                                                                  │
│  Keywords: [project update]                                     │
│                                                                  │
│  From:     [john@company.com]                                   │
│  To:       [________________]                                   │
│                                                                  │
│  Date:     [Last 7 days ▼]                                      │
│            Custom: [Jan 1, 2025] to [Jan 15, 2025]              │
│                                                                  │
│  Has:      ☑ Attachments                                        │
│            ☐ Star                                               │
│                                                                  │
│  Folder:   [All Mail ▼]                                         │
│  Account:  [All Accounts ▼]                                     │
│                                                                  │
│  Size:     [Greater than ▼] [1 MB]                              │
│                                                                  │
│  [Search] [Reset] [Save as Saved Search]                        │
└─────────────────────────────────────────────────────────────────┘
```

---

## Settings & Management Flows

### Flow: Manage Multiple Accounts

```
SETTINGS → Email Accounts
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│                   EMAIL ACCOUNTS PAGE                           │
│                                                                  │
│  [+ Add Account]                                                │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ myemail@gmail.com (Default)              [Edit] [Remove]  │ │
│  │ Gmail • Last synced: 2 minutes ago                         │ │
│  │ ☑ Sync enabled                                             │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ work@company.com                         [Edit] [Remove]  │ │
│  │ Custom IMAP • Last synced: 5 minutes ago                   │ │
│  │ ☑ Sync enabled                                             │ │
│  │ [Set as Default]                                           │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ personal@outlook.com                     [Edit] [Remove]  │ │
│  │ Outlook • Last synced: 1 hour ago                          │ │
│  │ ⚠️ Sync failed: Invalid credentials                        │ │
│  │ [Reconnect]                                                │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘

Actions:
    │
    ├─ [+ Add Account] → Account connection flow
    │
    ├─ [Edit] → Update account settings
    │
    ├─ [Remove] →
    │       ↓ Confirm: "Remove account and delete local emails?"
    │       ↓ [Keep Emails] [Delete Everything] [Cancel]
    │       ↓ Soft delete or hard delete
    │
    ├─ [Set as Default] → Used for compose "From" field
    │
    └─ [Reconnect] → Re-authenticate with new credentials
```

### Flow: Account Switching in Sidebar

```
┌─────────────────────────────────────────────────────────────────┐
│                    SIDEBAR (Mail View)                          │
│                                                                  │
│  ┌──────────────────────────────────────┐                       │
│  │ [myemail@gmail.com ▼]                │◄─ Click to switch     │
│  └──────────────────────────────────────┘                       │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│  Account Switcher (Dropdown)                                    │
│                                                                  │
│  ○ myemail@gmail.com (Current)                                  │
│     234 unread                                                  │
│                                                                  │
│  ○ work@company.com                                             │
│     12 unread                                                   │
│                                                                  │
│  ○ personal@outlook.com                                         │
│     0 unread                                                    │
│                                                                  │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━                                      │
│                                                                  │
│  ☑ Unified Inbox (All Accounts)                                 │
│     246 unread total                                            │
│                                                                  │
│  [Manage Accounts →]                                            │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼ Select account
┌─────────────────────────────────────────────────────────────────┐
│  Switched to work@company.com                                   │
│                                                                  │
│  Inbox reloads with emails from selected account                │
│  Folders update to show account-specific folders                │
└─────────────────────────────────────────────────────────────────┘
```

---

## Edge Cases & Error Flows

### Flow: Handle IMAP Connection Failure

```
Background Sync Job Running...
    │
    ▼
IMAP connection fails (timeout, auth error, server down)
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│  System Response:                                               │
│    ↓ Log error with details                                     │
│    ↓ Retry with exponential backoff (3 attempts)                │
│    ↓ If all retries fail:                                       │
│        - Mark account sync status as "failed"                   │
│        - Queue notification job                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│  User sees toast notification:                                  │
│                                                                  │
│  ⚠️ Unable to sync work@company.com                             │
│     Connection failed. Check your credentials.                  │
│                                                                  │
│  [Reconnect] [Dismiss]                                          │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│  In sidebar, account shows warning:                             │
│                                                                  │
│  📧 work@company.com ⚠️                                          │
│      Sync failed • [Reconnect]                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Flow: Handle Email Send Failure

```
User clicks [Send]
    │
    ▼
Queue SendEmailJob
    │
    ▼
Optimistic UI: "Email sent!" ✅
    │
    ▼
Background: SMTP send fails (server down, auth error, recipient invalid)
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│  System Response:                                               │
│    ↓ Retry 3 times with backoff                                 │
│    ↓ If permanent failure (bad recipient):                      │
│        - Mark email as failed                                   │
│        - Move to Drafts                                         │
│        - Notify user                                            │
│    ↓ If temporary failure (server down):                        │
│        - Queue for later retry                                  │
│        - Notify user of delay                                   │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│  User sees notification:                                        │
│                                                                  │
│  ❌ Failed to send email to invalid@domain.com                  │
│     Recipient address rejected by server.                       │
│                                                                  │
│  Email saved in Drafts.                                         │
│                                                                  │
│  [Edit & Resend] [Delete] [Dismiss]                             │
└─────────────────────────────────────────────────────────────────┘
```

### Flow: OAuth Token Expired

```
Background job attempts to sync Gmail account
    │
    ▼
OAuth access token expired
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│  System automatically:                                          │
│    ↓ Check if refresh token exists                              │
│    ↓ Request new access token from Google                       │
│    ↓ Store new token (encrypted)                                │
│    ↓ Retry sync operation                                       │
│    ↓ User sees no interruption ✅                                │
└─────────────────────────────────────────────────────────────────┘

If refresh token also invalid/revoked:
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│  User sees notification:                                        │
│                                                                  │
│  ⚠️ Gmail account myemail@gmail.com needs reconnection          │
│     Please sign in again to continue syncing.                   │
│                                                                  │
│  [Reconnect with Google] [Dismiss]                              │
└─────────────────────────────────────────────────────────────────┘
```

### Flow: Attachment Too Large

```
User tries to attach 50 MB file
    │
    ▼
System checks file size limit (default: 25 MB)
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│  Warning Modal:                                                 │
│                                                                  │
│  ⚠️ File too large                                              │
│                                                                  │
│  "presentation.pptx" is 50 MB.                                  │
│  Maximum attachment size is 25 MB.                              │
│                                                                  │
│  Suggestions:                                                   │
│  • Compress the file                                            │
│  • Use a file sharing service (Dropbox, Google Drive)           │
│  • Send link instead                                            │
│                                                                  │
│  [OK]                                                           │
└─────────────────────────────────────────────────────────────────┘
```

---

## Summary: Key UX Principles

### Speed
- **5-click onboarding** from landing page to first email sent
- **Optimistic UI** updates for instant feedback
- **Background processing** for sync, send, indexing
- **Cached data** for sub-second page loads

### Simplicity
- **Auto-configuration** for popular email providers
- **Smart defaults** that work for 90% of users
- **Progressive disclosure** of advanced features
- **Inline help** where needed

### Delight
- **Smooth animations** and transitions
- **Real-time updates** via WebSockets
- **Keyboard shortcuts** for power users
- **Thoughtful error messages** with actionable solutions

### Reliability
- **Graceful error handling** with retry logic
- **Clear status indicators** (syncing, sending, failed)
- **Undo actions** where applicable
- **Auto-save** for drafts
