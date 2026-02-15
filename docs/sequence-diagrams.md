# Maylbox.co - Sequence Diagrams

## Table of Contents
1. [User Registration & Onboarding](#1-user-registration--onboarding)
2. [Email Account Connection (OAuth)](#2-email-account-connection-oauth)
3. [Email Account Connection (IMAP)](#3-email-account-connection-imap)
4. [Email Syncing (IMAP)](#4-email-syncing-imap)
5. [Sending Email (SMTP)](#5-sending-email-smtp)
6. [Reading Email](#6-reading-email)
7. [Composing & Saving Draft](#7-composing--saving-draft)
8. [Real-Time Email Notification](#8-real-time-email-notification)
9. [Email Search](#9-email-search)
10. [Moving Email to Folder](#10-moving-email-to-folder)
11. [OAuth Token Refresh](#11-oauth-token-refresh)
12. [Error Handling - SMTP Failure](#12-error-handling---smtp-failure)

---

## 1. User Registration & Onboarding

**Goal**: Sign up and connect email account in minimal steps.

```
┌──────┐          ┌───────┐          ┌────────┐          ┌──────────┐          ┌──────────┐
│ User │          │Browser│          │Laravel │          │ Database │          │   Queue  │
└──┬───┘          └───┬───┘          └───┬────┘          └────┬─────┘          └────┬─────┘
   │                  │                  │                      │                     │
   │ 1. Visit /signup │                  │                      │                     │
   ├─────────────────>│                  │                      │                     │
   │                  │                  │                      │                     │
   │                  │ 2. Render signup │                      │                     │
   │                  │      page (Vue)  │                      │                     │
   │<─────────────────┤                  │                      │                     │
   │                  │                  │                      │                     │
   │ 3. Fill form &   │                  │                      │                     │
   │    submit        │                  │                      │                     │
   ├─────────────────>│                  │                      │                     │
   │                  │                  │                      │                     │
   │                  │ 4. POST /register│                      │                     │
   │                  ├─────────────────>│                      │                     │
   │                  │                  │                      │                     │
   │                  │                  │ 5. Validate data     │                     │
   │                  │                  ├──────┐               │                     │
   │                  │                  │      │               │                     │
   │                  │                  │<─────┘               │                     │
   │                  │                  │                      │                     │
   │                  │                  │ 6. Hash password     │                     │
   │                  │                  ├──────┐               │                     │
   │                  │                  │      │               │                     │
   │                  │                  │<─────┘               │                     │
   │                  │                  │                      │                     │
   │                  │                  │ 7. Create user       │                     │
   │                  │                  ├─────────────────────>│                     │
   │                  │                  │                      │                     │
   │                  │                  │    8. User created   │                     │
   │                  │                  │<─────────────────────┤                     │
   │                  │                  │                      │                     │
   │                  │                  │ 9. Create session    │                     │
   │                  │                  ├──────┐               │                     │
   │                  │                  │      │               │                     │
   │                  │                  │<─────┘               │                     │
   │                  │                  │                      │                     │
   │                  │ 10. Redirect to  │                      │                     │
   │                  │ /accounts/create │                      │                     │
   │<─────────────────┤<─────────────────┤                      │                     │
   │                  │                  │                      │                     │
   │ 11. Load account │                  │                      │                     │
   │     connection   │                  │                      │                     │
   │<─────────────────┤                  │                      │                     │
   │                  │                  │                      │                     │
   │ 12. Enter email  │                  │                      │                     │
   │    credentials   │                  │                      │                     │
   ├─────────────────>│                  │                      │                     │
   │                  │                  │                      │                     │
   │                  │ 13. POST         │                      │                     │
   │                  │ /accounts        │                      │                     │
   │                  ├─────────────────>│                      │                     │
   │                  │                  │                      │                     │
   │                  │                  │ 14. Auto-detect      │                     │
   │                  │                  │     provider         │                     │
   │                  │                  ├──────┐               │                     │
   │                  │                  │      │               │                     │
   │                  │                  │<─────┘               │                     │
   │                  │                  │                      │                     │
   │                  │                  │ 15. Test IMAP conn   │                     │
   │                  │                  ├──────┐               │                     │
   │                  │                  │      │               │                     │
   │                  │                  │<─────┘               │                     │
   │                  │                  │                      │                     │
   │                  │                  │ 16. Encrypt & store  │                     │
   │                  │                  ├─────────────────────>│                     │
   │                  │                  │                      │                     │
   │                  │                  │ 17. Queue sync job   │                     │
   │                  │                  ├─────────────────────────────────────────>│
   │                  │                  │                      │                     │
   │                  │ 18. Redirect to  │                      │                     │
   │                  │     /mail        │                      │                     │
   │<─────────────────┤<─────────────────┤                      │                     │
   │                  │                  │                      │                     │
   │ 19. Load inbox   │                  │                      │                     │
   │    (syncing...)  │                  │                      │                     │
   │<─────────────────┤                  │                      │                     │
   │                  │                  │                      │                     │
```

**Key Points**:
- Email verification skipped for speed (can verify later)
- Account connection immediately after signup
- Background sync starts automatically

---

## 2. Email Account Connection (OAuth)

**Use Case**: Connecting Gmail via OAuth 2.0

```
┌──────┐     ┌───────┐     ┌────────┐     ┌──────────┐     ┌────────────┐     ┌──────────┐
│ User │     │Browser│     │Laravel │     │ Database │     │   Google   │     │   Queue  │
└──┬───┘     └───┬───┘     └───┬────┘     └────┬─────┘     │   OAuth    │     └────┬─────┘
   │             │             │                │           └──────┬─────┘          │
   │ 1. Click    │             │                │                  │                │
   │ "Connect    │             │                │                  │                │
   │  Gmail"     │             │                │                  │                │
   ├────────────>│             │                │                  │                │
   │             │             │                │                  │                │
   │             │ 2. GET      │                │                  │                │
   │             │ /oauth/     │                │                  │                │
   │             │  google     │                │                  │                │
   │             ├────────────>│                │                  │                │
   │             │             │                │                  │                │
   │             │             │ 3. Generate    │                  │                │
   │             │             │    state token │                  │                │
   │             │             ├────┐           │                  │                │
   │             │             │    │           │                  │                │
   │             │             │<───┘           │                  │                │
   │             │             │                │                  │                │
   │             │             │ 4. Store state │                  │                │
   │             │             ├───────────────>│                  │                │
   │             │             │                │                  │                │
   │             │ 5. Redirect │                │                  │                │
   │             │    to Google│                │                  │                │
   │             │    OAuth    │                │                  │                │
   │<────────────┤<────────────┤                │                  │                │
   │             │             │                │                  │                │
   │ 6. Redirected to Google   │                │                  │                │
   ├──────────────────────────────────────────────────────────────>│                │
   │             │             │                │                  │                │
   │             │             │                │   7. Show consent│                │
   │             │             │                │      screen      │                │
   │<──────────────────────────────────────────────────────────────┤                │
   │             │             │                │                  │                │
   │ 8. User     │             │                │                  │                │
   │    approves │             │                │                  │                │
   ├────────────────────────────────────────────────────────────>│                │
   │             │             │                │                  │                │
   │             │             │                │  9. Redirect back│                │
   │             │             │                │     with code    │                │
   │<──────────────────────────────────────────────────────────────┤                │
   │             │             │                │                  │                │
   │ 10. Callback to Laravel   │                │                  │                │
   │    /oauth/callback?code=  │                │                  │                │
   ├────────────>│             │                │                  │                │
   │             │             │                │                  │                │
   │             │ 11. POST    │                │                  │                │
   │             │  /oauth/    │                │                  │                │
   │             │  callback   │                │                  │                │
   │             ├────────────>│                │                  │                │
   │             │             │                │                  │                │
   │             │             │ 12. Verify     │                  │                │
   │             │             │     state      │                  │                │
   │             │             ├───────────────>│                  │                │
   │             │             │                │                  │                │
   │             │             │ 13. Exchange   │                  │                │
   │             │             │     code for   │                  │                │
   │             │             │     tokens     │                  │                │
   │             │             ├───────────────────────────────────>│                │
   │             │             │                │                  │                │
   │             │             │                │  14. Return      │                │
   │             │             │                │      tokens      │                │
   │             │             │<───────────────────────────────────┤                │
   │             │             │                │                  │                │
   │             │             │ 15. Fetch user │                  │                │
   │             │             │     email via  │                  │                │
   │             │             │     Google API │                  │                │
   │             │             ├───────────────────────────────────>│                │
   │             │             │<───────────────────────────────────┤                │
   │             │             │                │                  │                │
   │             │             │ 16. Encrypt &  │                  │                │
   │             │             │     store      │                  │                │
   │             │             │     tokens     │                  │                │
   │             │             ├───────────────>│                  │                │
   │             │             │                │                  │                │
   │             │             │ 17. Create     │                  │                │
   │             │             │     account    │                  │                │
   │             │             │     record     │                  │                │
   │             │             ├───────────────>│                  │                │
   │             │             │                │                  │                │
   │             │             │ 18. Queue sync │                  │                │
   │             │             ├───────────────────────────────────────────────────>│
   │             │             │                │                  │                │
   │             │ 19. Success │                │                  │                │
   │<────────────┤<────────────┤                │                  │                │
   │             │             │                │                  │                │
```

**Key Points**:
- OAuth state token prevents CSRF attacks
- Tokens encrypted before storage
- Sync job queued immediately after connection

---

## 3. Email Account Connection (IMAP)

**Use Case**: Connecting custom domain email via IMAP/SMTP

```
┌──────┐     ┌───────┐     ┌────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│ User │     │Browser│     │Laravel │     │ Database │     │   IMAP   │     │   SMTP   │
└──┬───┘     └───┬───┘     └───┬────┘     └────┬─────┘     │  Server  │     │  Server  │
   │             │             │                │           └────┬─────┘     └────┬─────┘
   │             │             │                │                │                │
   │ 1. Enter    │             │                │                │                │
   │    email &  │             │                │                │                │
   │    password │             │                │                │                │
   ├────────────>│             │                │                │                │
   │             │             │                │                │                │
   │             │ 2. POST     │                │                │                │
   │             │ /accounts   │                │                │                │
   │             ├────────────>│                │                │                │
   │             │             │                │                │                │
   │             │             │ 3. Auto-detect │                │                │
   │             │             │    provider    │                │                │
   │             │             │    settings    │                │                │
   │             │             ├────┐           │                │                │
   │             │             │    │ Based on  │                │                │
   │             │             │    │ email     │                │                │
   │             │             │    │ domain    │                │                │
   │             │             │<───┘           │                │                │
   │             │             │                │                │                │
   │             │             │ 4. Test IMAP   │                │                │
   │             │             │    connection  │                │                │
   │             │             ├───────────────────────────────>│                │
   │             │             │                │                │                │
   │             │             │                │   5. CONNECT   │                │
   │             │             │                │      LOGIN     │                │
   │             │             │                │      SELECT    │                │
   │             │             │                │      INBOX     │                │
   │             │             │                │                │                │
   │             │             │                │   6. Success   │                │
   │             │             │<───────────────────────────────┤                │
   │             │             │                │                │                │
   │             │             │ 7. Test SMTP   │                │                │
   │             │             │    connection  │                │                │
   │             │             ├─────────────────────────────────────────────────>│
   │             │             │                │                │                │
   │             │             │                │                │   8. CONNECT   │
   │             │             │                │                │      EHLO      │
   │             │             │                │                │      AUTH      │
   │             │             │                │                │                │
   │             │             │                │                │   9. Success   │
   │             │             │<─────────────────────────────────────────────────┤
   │             │             │                │                │                │
   │             │             │ 10. Encrypt    │                │                │
   │             │             │     credentials│                │                │
   │             │             ├────┐           │                │                │
   │             │             │    │           │                │                │
   │             │             │<───┘           │                │                │
   │             │             │                │                │                │
   │             │             │ 11. Store      │                │                │
   │             │             │     account    │                │                │
   │             │             ├───────────────>│                │                │
   │             │             │                │                │                │
   │             │             │ 12. Queue      │                │                │
   │             │             │  SyncEmailJob  │                │                │
   │             │             ├────────────────────────┐        │                │
   │             │             │                │       │        │                │
   │             │             │                │       │        │                │
   │             │ 13. Success │                │       │        │                │
   │<────────────┤<────────────┤                │       │        │                │
   │             │             │                │       │        │                │
   │             │             │                │       │        │                │
   │             │             │                ▼       ▼        │                │
   │             │             │         [Queue Worker]          │                │
   │             │             │                │                │                │
```

**Key Points**:
- Tests both IMAP and SMTP before saving
- Credentials encrypted with Laravel's encryption
- Connection test happens synchronously for immediate feedback

---

## 4. Email Syncing (IMAP)

**Use Case**: Background job fetches emails from IMAP server

```
┌────────────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐     ┌─────────┐
│ Queue Worker   │     │ Laravel  │     │ Database │     │   IMAP   │     │  Redis  │
│(SyncEmailJob)  │     │ Services │     │          │     │  Server  │     │ Cache   │
└──────┬─────────┘     └────┬─────┘     └────┬─────┘     └────┬─────┘     └────┬────┘
       │                    │                 │                │                │
       │ 1. Job starts      │                 │                │                │
       ├───────────────────>│                 │                │                │
       │                    │                 │                │                │
       │                    │ 2. Get account  │                │                │
       │                    ├────────────────>│                │                │
       │                    │                 │                │                │
       │                    │ 3. Return acct  │                │                │
       │                    │<────────────────┤                │                │
       │                    │                 │                │                │
       │                    │ 4. Decrypt      │                │                │
       │                    │    credentials  │                │                │
       │                    ├────┐            │                │                │
       │                    │    │            │                │                │
       │                    │<───┘            │                │                │
       │                    │                 │                │                │
       │                    │ 5. Connect IMAP │                │                │
       │                    ├────────────────────────────────>│                │
       │                    │                 │                │                │
       │                    │                 │   6. Connected │                │
       │                    │<────────────────────────────────┤                │
       │                    │                 │                │                │
       │                    │ 7. Get last UID │                │                │
       │                    ├────────────────>│                │                │
       │                    │<────────────────┤                │                │
       │                    │                 │                │                │
       │                    │ 8. SEARCH new   │                │                │
       │                    │    UIDs since   │                │                │
       │                    │    last sync    │                │                │
       │                    ├────────────────────────────────>│                │
       │                    │                 │                │                │
       │                    │                 │   9. Return    │                │
       │                    │                 │      UIDs      │                │
       │                    │<────────────────────────────────┤                │
       │                    │                 │                │                │
       │ 10. Loop through   │                 │                │                │
       │     new UIDs       │                 │                │                │
       │     (batch of 50)  │                 │                │                │
       │                    │                 │                │                │
       │                    │ 11. FETCH       │                │                │
       │                    │     headers &   │                │                │
       │                    │     body        │                │                │
       │                    ├────────────────────────────────>│                │
       │                    │                 │                │                │
       │                    │                 │ 12. Return data│                │
       │                    │<────────────────────────────────┤                │
       │                    │                 │                │                │
       │                    │ 13. Parse email │                │                │
       │                    ├────┐            │                │                │
       │                    │    │ Extract:   │                │                │
       │                    │    │ - Headers  │                │                │
       │                    │    │ - Body     │                │                │
       │                    │    │ - Attachs  │                │                │
       │                    │<───┘            │                │                │
       │                    │                 │                │                │
       │                    │ 14. Build thread│                │                │
       │                    │     based on    │                │                │
       │                    │     References  │                │                │
       │                    ├────┐            │                │                │
       │                    │<───┘            │                │                │
       │                    │                 │                │                │
       │                    │ 15. Store email │                │                │
       │                    ├────────────────>│                │                │
       │                    │                 │                │                │
       │                    │ 16. Cache       │                │                │
       │                    │     metadata    │                │                │
       │                    ├───────────────────────────────────────────────────>│
       │                    │                 │                │                │
       │                    │ 17. Queue       │                │                │
       │                    │ IndexEmailJob   │                │                │
       │                    ├────────────────────────┐         │                │
       │                    │                 │      │         │                │
       │                    │                 │      │         │                │
       │ 18. Next UID...    │                 │      │         │                │
       │                    │                 │      │         │                │
       │ (repeat 11-17)     │                 │      │         │                │
       │                    │                 │      │         │                │
       │                    │ 19. All fetched │      │         │                │
       │                    │                 │      │         │                │
       │                    │ 20. Update      │      │         │                │
       │                    │     last_synced │      │         │                │
       │                    ├────────────────>│      │         │                │
       │                    │                 │      │         │                │
       │                    │ 21. Broadcast   │      │         │                │
       │                    │  NewEmailEvent  │      │         │                │
       │                    ├────────────────────────────┐     │                │
       │                    │                 │      │   │     │                │
       │ 22. Job complete   │                 │      │   │     │                │
       │<───────────────────┤                 │      │   │     │                │
       │                    │                 │      │   │     │                │
```

**Key Points**:
- Incremental sync (only new emails)
- Batch processing (50 emails at a time)
- Threading based on In-Reply-To and References headers
- Real-time broadcast when new emails arrive

---

## 5. Sending Email (SMTP)

**Use Case**: User composes and sends an email

```
┌──────┐     ┌───────┐     ┌────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│ User │     │Browser│     │Laravel │     │   Queue  │     │   SMTP   │     │   IMAP   │
└──┬───┘     └───┬───┘     └───┬────┘     │  Worker  │     │  Server  │     │  Server  │
   │             │             │           └────┬─────┘     └────┬─────┘     └────┬─────┘
   │             │             │                │                │                │
   │ 1. Click    │             │                │                │                │
   │    [Send]   │             │                │                │                │
   ├────────────>│             │                │                │                │
   │             │             │                │                │                │
   │             │ 2. POST     │                │                │                │
   │             │ /compose/   │                │                │                │
   │             │    send     │                │                │                │
   │             ├────────────>│                │                │                │
   │             │             │                │                │                │
   │             │             │ 3. Validate    │                │                │
   │             │             │    recipients  │                │                │
   │             │             ├────┐           │                │                │
   │             │             │    │           │                │                │
   │             │             │<───┘           │                │                │
   │             │             │                │                │                │
   │             │             │ 4. Create draft│                │                │
   │             │             │    record      │                │                │
   │             │             ├────────────────────┐            │                │
   │             │             │                │   │            │                │
   │             │             │                │   │            │                │
   │             │             │ 5. Queue       │   │            │                │
   │             │             │  SendEmailJob  │   │            │                │
   │             │             ├───────────────>│   │            │                │
   │             │             │                │   │            │                │
   │             │ 6. Return   │                │   │            │                │
   │             │    success  │                │   │            │                │
   │             │    (optimis │                │   │            │                │
   │             │     tic UI) │                │   │            │                │
   │<────────────┤<────────────┤                │   │            │                │
   │             │             │                │   │            │                │
   │ 7. Show     │             │                │   │            │                │
   │  "Sending..." toast       │                │   │            │                │
   │<────────────┤             │                │   │            │                │
   │             │             │                │   │            │                │
   │             │             │                ▼   ▼            │                │
   │             │             │         [Queue Worker]          │                │
   │             │             │                │                │                │
   │             │             │ 8. Job starts  │                │                │
   │             │             │<───────────────┤                │                │
   │             │             │                │                │                │
   │             │             │ 9. Get account │                │                │
   │             │             │    & decrypt   │                │                │
   │             │             ├────┐           │                │                │
   │             │             │<───┘           │                │                │
   │             │             │                │                │                │
   │             │             │ 10. Connect    │                │                │
   │             │             │     to SMTP    │                │                │
   │             │             ├───────────────────────────────>│                │
   │             │             │                │                │                │
   │             │             │                │  11. Connected │                │
   │             │             │<───────────────────────────────┤                │
   │             │             │                │                │                │
   │             │             │ 12. Send email │                │                │
   │             │             │     (MAIL FROM │                │                │
   │             │             │      RCPT TO,  │                │                │
   │             │             │      DATA)     │                │                │
   │             │             ├───────────────────────────────>│                │
   │             │             │                │                │                │
   │             │             │                │  13. 250 OK    │                │
   │             │             │<───────────────────────────────┤                │
   │             │             │                │                │                │
   │             │             │ 14. Append to  │                │                │
   │             │             │     Sent folder│                │                │
   │             │             │     via IMAP   │                │                │
   │             │             ├───────────────────────────────────────────────>│
   │             │             │                │                │                │
   │             │             │                │                │  15. Appended  │
   │             │             │<───────────────────────────────────────────────┤
   │             │             │                │                │                │
   │             │             │ 16. Update     │                │                │
   │             │             │     email      │                │                │
   │             │             │     status     │                │                │
   │             │             ├────────────────────┐            │                │
   │             │             │                │   │            │                │
   │             │             │                │   │            │                │
   │             │             │ 17. Broadcast  │   │            │                │
   │             │             │   EmailSent    │   │            │                │
   │             │             │     event      │   │            │                │
   │             │             ├────────────────────────┐        │                │
   │             │             │                │   │   │        │                │
   │             │             │                │   │   │        │                │
   │ 18. Real-time notification│                │   │   │        │                │
   │    "Email sent!" ✅       │                │   │   │        │                │
   │<────────────┤<────────────┤                │   │   │        │                │
   │             │             │                │   │   │        │                │
```

**Key Points**:
- Optimistic UI (immediate feedback)
- Asynchronous sending via queue
- Email saved to Sent folder via IMAP APPEND
- Real-time notification on success

---

## 6. Reading Email

**Use Case**: User opens an email in their inbox

```
┌──────┐     ┌───────┐     ┌────────┐     ┌──────────┐     ┌─────────┐     ┌──────────┐
│ User │     │Browser│     │Laravel │     │ Database │     │  Redis  │     │   IMAP   │
└──┬───┘     └───┬───┘     └───┬────┘     └────┬─────┘     │  Cache  │     │  Server  │
   │             │             │                │           └────┬────┘     └────┬─────┘
   │             │             │                │                │                │
   │ 1. Click    │             │                │                │                │
   │    email in │             │                │                │                │
   │    list     │             │                │                │                │
   ├────────────>│             │                │                │                │
   │             │             │                │                │                │
   │             │ 2. GET      │                │                │                │
   │             │ /mail/{id}  │                │                │                │
   │             ├────────────>│                │                │                │
   │             │             │                │                │                │
   │             │             │ 3. Check cache │                │                │
   │             │             ├───────────────────────────────>│                │
   │             │             │                │                │                │
   │             │             │ 4. Cache miss  │                │                │
   │             │             │<───────────────────────────────┤                │
   │             │             │                │                │                │
   │             │             │ 5. Query email │                │                │
   │             │             ├───────────────>│                │                │
   │             │             │                │                │                │
   │             │             │ 6. Return email│                │                │
   │             │             │<───────────────┤                │                │
   │             │             │                │                │                │
   │             │             │ 7. If threaded,│                │                │
   │             │             │    get thread  │                │                │
   │             │             ├───────────────>│                │                │
   │             │             │<───────────────┤                │                │
   │             │             │                │                │                │
   │             │             │ 8. Cache result│                │                │
   │             │             ├───────────────────────────────>│                │
   │             │             │                │                │                │
   │             │ 9. Return   │                │                │                │
   │             │    email    │                │                │                │
   │             │    (Inertia)│                │                │                │
   │<────────────┤<────────────┤                │                │                │
   │             │             │                │                │                │
   │ 10. Display │             │                │                │                │
   │     email   │             │                │                │                │
   │<────────────┤             │                │                │                │
   │             │             │                │                │                │
   │ 11. After 2s│             │                │                │                │
   │    visible, │             │                │                │                │
   │    mark as  │             │                │                │                │
   │    read     │             │                │                │                │
   ├────────────>│             │                │                │                │
   │             │             │                │                │                │
   │             │ 12. PATCH   │                │                │                │
   │             │ /emails/{id}│                │                │                │
   │             │    /read    │                │                │                │
   │             ├────────────>│                │                │                │
   │             │             │                │                │                │
   │             │             │ 13. Update DB  │                │                │
   │             │             ├───────────────>│                │                │
   │             │             │                │                │                │
   │             │             │ 14. Queue job  │                │                │
   │             │             │   to mark on   │                │                │
   │             │             │   IMAP server  │                │                │
   │             │             ├────────────────────────┐        │                │
   │             │             │                │       │        │                │
   │             │             │                │       │        │                │
   │             │             │                ▼       ▼        │                │
   │             │             │         [Queue Worker]          │                │
   │             │             │                │                │                │
   │             │             │ 15. STORE      │                │                │
   │             │             │   +FLAGS \Seen │                │                │
   │             │             ├───────────────────────────────────────────────>│
   │             │             │                │                │                │
   │             │             │                │                │  16. Marked    │
   │             │             │<───────────────────────────────────────────────┤
   │             │             │                │                │                │
```

**Key Points**:
- Email metadata cached in Redis
- Thread context loaded if available
- Auto-mark as read after 2 seconds
- IMAP flag updated asynchronously

---

## 7. Composing & Saving Draft

**Use Case**: User writes email and auto-saves draft

```
┌──────┐     ┌───────┐     ┌────────┐     ┌──────────┐     ┌──────────┐
│ User │     │Browser│     │Laravel │     │ Database │     │   IMAP   │
└──┬───┘     └───┬───┘     └───┬────┘     └────┬─────┘     │  Server  │
   │             │             │                │           └────┬─────┘
   │             │             │                │                │
   │ 1. Click    │             │                │                │
   │  [Compose]  │             │                │                │
   ├────────────>│             │                │                │
   │             │             │                │                │
   │             │ 2. Open     │                │                │
   │             │    compose  │                │                │
   │             │    modal    │                │                │
   │<────────────┤             │                │                │
   │             │             │                │                │
   │ 3. Type     │             │                │                │
   │    content  │             │                │                │
   ├────────────>│             │                │                │
   │             │             │                │                │
   │ ... (10 seconds pass)     │                │                │
   │             │             │                │                │
   │             │ 4. Auto-save│                │                │
   │             │    draft    │                │                │
   │             │    (POST    │                │                │
   │             │    /compose)│                │                │
   │             ├────────────>│                │                │
   │             │             │                │                │
   │             │             │ 5. Create/     │                │
   │             │             │    update      │                │
   │             │             │    draft       │                │
   │             │             ├───────────────>│                │
   │             │             │                │                │
   │             │             │ 6. Draft saved │                │
   │             │             │<───────────────┤                │
   │             │             │                │                │
   │             │ 7. Return   │                │                │
   │             │    draft_id │                │                │
   │<────────────┤<────────────┤                │                │
   │             │             │                │                │
   │ 8. Subtle   │             │                │                │
   │   "Saved"   │             │                │                │
   │   indicator │             │                │                │
   │<────────────┤             │                │                │
   │             │             │                │                │
   │ 9. Continue │             │                │                │
   │    typing...│             │                │                │
   │             │             │                │                │
   │ ... (10 seconds pass)     │                │                │
   │             │             │                │                │
   │             │ 10. Auto-   │                │                │
   │             │     save    │                │                │
   │             │     again   │                │                │
   │             ├────────────>│                │                │
   │             │             │                │                │
   │             │             │ 11. Update     │                │
   │             │             │     existing   │                │
   │             │             │     draft      │                │
   │             │             ├───────────────>│                │
   │             │             │                │                │
   │             │             │                │                │
   │ 12. User clicks [Save Draft]                │                │
   │              manually     │                │                │
   ├────────────>│             │                │                │
   │             │             │                │                │
   │             │ 13. Save &  │                │                │
   │             │     append  │                │                │
   │             │     to IMAP │                │                │
   │             ├────────────>│                │                │
   │             │             │                │                │
   │             │             │ 14. Update DB  │                │
   │             │             ├───────────────>│                │
   │             │             │                │                │
   │             │             │ 15. Queue job  │                │
   │             │             │    to save on  │                │
   │             │             │    IMAP server │                │
   │             │             ├────────────────────────┐        │
   │             │             │                │       │        │
   │             │             │                ▼       ▼        │
   │             │             │         [Queue Worker]          │
   │             │             │                │                │
   │             │             │ 16. IMAP       │                │
   │             │             │     APPEND to  │                │
   │             │             │     Drafts     │                │
   │             │             ├───────────────────────────────>│
   │             │             │                │                │
   │             │             │                │   17. Appended │
   │             │             │<───────────────────────────────┤
   │             │             │                │                │
   │ 18. "Draft saved" toast   │                │                │
   │<────────────┤<────────────┤                │                │
   │             │             │                │                │
```

**Key Points**:
- Auto-save every 10 seconds
- Local draft until manual save
- Manual save syncs to IMAP Drafts folder
- Draft ID tracked for updates

---

## 8. Real-Time Email Notification

**Use Case**: New email arrives and user is notified instantly

```
┌───────────┐   ┌──────┐   ┌────────┐   ┌─────────┐   ┌──────────┐   ┌───────┐
│   IMAP    │   │Queue │   │Laravel │   │  Redis  │   │WebSocket │   │Browser│
│  Server   │   │Worker│   │ Events │   │  Pub/Sub│   │  Server  │   │(User) │
└─────┬─────┘   └──┬───┘   └───┬────┘   └────┬────┘   │(Reverb)  │   └───┬───┘
      │            │            │             │        └────┬─────┘       │
      │ 1. New     │            │             │             │             │
      │   email    │            │             │             │             │
      │   arrives  │            │             │             │             │
      │            │            │             │             │             │
      │ 2. Sync job│            │             │             │             │
      │   runs     │            │             │             │             │
      │   (every   │            │             │             │             │
      │   5 min or │            │             │             │             │
      │   IDLE)    │            │             │             │             │
      │            │            │             │             │             │
      │ 3. SEARCH  │            │             │             │             │
      │   UNSEEN   │            │             │             │             │
      ├───────────>│            │             │             │             │
      │            │            │             │             │             │
      │ 4. Return  │            │             │             │             │
      │    new UIDs│            │             │             │             │
      │<───────────┤            │             │             │             │
      │            │            │             │             │             │
      │ 5. FETCH   │            │             │             │             │
      │    email   │            │             │             │             │
      ├───────────>│            │             │             │             │
      │            │            │             │             │             │
      │ 6. Email   │            │             │             │             │
      │    data    │            │             │             │             │
      │<───────────┤            │             │             │             │
      │            │            │             │             │             │
      │            │ 7. Parse & │             │             │             │
      │            │    store   │             │             │             │
      │            │    email   │             │             │             │
      │            ├───────────────────────────────┐         │             │
      │            │            │             │    │         │             │
      │            │            │             │    │         │             │
      │            │ 8. Fire    │             │    │         │             │
      │            │  NewEmail  │             │    │         │             │
      │            │    Event   │             │    │         │             │
      │            ├───────────>│             │    │         │             │
      │            │            │             │    │         │             │
      │            │            │ 9. Broadcast │   │         │             │
      │            │            │    to Redis │    │         │             │
      │            │            ├────────────>│    │         │             │
      │            │            │             │    │         │             │
      │            │            │             │ 10. Publish │             │
      │            │            │             │    to channel│             │
      │            │            │             ├─────────────>│             │
      │            │            │             │    │         │             │
      │            │            │             │    │   11. Broadcast       │
      │            │            │             │    │       to user         │
      │            │            │             │    │       via WSS         │
      │            │            │             │    ├────────────────────>│
      │            │            │             │    │         │             │
      │            │            │             │    │         │ 12. Receive │
      │            │            │             │    │         │     event   │
      │            │            │             │    │         │<────────────┤
      │            │            │             │    │         │             │
      │            │            │             │    │         │ 13. Update  │
      │            │            │             │    │         │     inbox   │
      │            │            │             │    │         │     count   │
      │            │            │             │    │         │<────────────┤
      │            │            │             │    │         │             │
      │            │            │             │    │         │ 14. Show    │
      │            │            │             │    │         │     toast   │
      │            │            │             │    │         │     "New    │
      │            │            │             │    │         │      email!"│
      │            │            │             │    │         │<────────────┤
      │            │            │             │    │         │             │
      │            │            │             │    │         │ 15. Play    │
      │            │            │             │    │         │     sound   │
      │            │            │             │    │         │     (opt)   │
      │            │            │             │    │         │<────────────┤
      │            │            │             │    │         │             │
```

**Key Points**:
- Uses Laravel Broadcasting + WebSockets
- Redis pub/sub for scalability
- Private channels per user
- Instant UI updates without page refresh

---

## 9. Email Search

**Use Case**: User searches for emails

```
┌──────┐     ┌───────┐     ┌────────┐     ┌──────────┐     ┌────────────┐
│ User │     │Browser│     │Laravel │     │ Database │     │Meilisearch │
└──┬───┘     └───┬───┘     └───┬────┘     └────┬─────┘     │  Index     │
   │             │             │                │           └──────┬─────┘
   │             │             │                │                  │
   │ 1. Type in  │             │                │                  │
   │    search:  │             │                │                  │
   │   "project" │             │                │                  │
   ├────────────>│             │                │                  │
   │             │             │                │                  │
   │ ... (300ms debounce)      │                │                  │
   │             │             │                │                  │
   │             │ 2. GET      │                │                  │
   │             │ /search?q=  │                │                  │
   │             │   project   │                │                  │
   │             ├────────────>│                │                  │
   │             │             │                │                  │
   │             │             │ 3. Search      │                  │
   │             │             │    Meilisearch │                  │
   │             │             ├───────────────────────────────────>│
   │             │             │                │                  │
   │             │             │                │  4. Full-text    │
   │             │             │                │     search       │
   │             │             │                │     across       │
   │             │             │                │     indexed      │
   │             │             │                │     emails       │
   │             │             │                │                  │
   │             │             │                │  5. Return IDs   │
   │             │             │                │     & scores     │
   │             │             │<───────────────────────────────────┤
   │             │             │                │                  │
   │             │             │ 6. Fetch email │                  │
   │             │             │    metadata    │                  │
   │             │             │    from DB     │                  │
   │             │             ├───────────────>│                  │
   │             │             │                │                  │
   │             │             │ 7. Return      │                  │
   │             │             │    emails      │                  │
   │             │             │<───────────────┤                  │
   │             │             │                │                  │
   │             │ 8. Return   │                │                  │
   │             │    results  │                │                  │
   │             │    (Inertia)│                │                  │
   │<────────────┤<────────────┤                │                  │
   │             │             │                │                  │
   │ 9. Display  │             │                │                  │
   │    results  │             │                │                  │
   │    with     │             │                │                  │
   │    snippet  │             │                │                  │
   │    highlight│             │                │                  │
   │<────────────┤             │                │                  │
   │             │             │                │                  │
```

**Key Points**:
- Debounced search (300ms delay)
- Full-text search via Meilisearch
- Fallback to PostgreSQL tsvector if no Meilisearch
- Results include highlighted snippets

---

## 10. Moving Email to Folder

**Use Case**: User moves email to a different folder

```
┌──────┐     ┌───────┐     ┌────────┐     ┌──────────┐     ┌──────────┐
│ User │     │Browser│     │Laravel │     │ Database │     │   IMAP   │
└──┬───┘     └───┬───┘     └───┬────┘     └────┬─────┘     │  Server  │
   │             │             │                │           └────┬─────┘
   │             │             │                │                │
   │ 1. Click    │             │                │                │
   │  "Move to   │             │                │                │
   │   Work"     │             │                │                │
   ├────────────>│             │                │                │
   │             │             │                │                │
   │             │ 2. POST     │                │                │
   │             │ /emails/{id}│                │                │
   │             │    /move    │                │                │
   │             ├────────────>│                │                │
   │             │             │                │                │
   │             │             │ 3. Update DB   │                │
   │             │             │    (optimistic)│                │
   │             │             ├───────────────>│                │
   │             │             │                │                │
   │             │             │ 4. Queue job   │                │
   │             │             ├────────────────────────┐        │
   │             │             │                │       │        │
   │             │ 5. Return   │                │       │        │
   │             │    success  │                │       │        │
   │<────────────┤<────────────┤                │       │        │
   │             │             │                │       │        │
   │ 6. Update UI│             │                │       │        │
   │  immediately│             │                │       │        │
   │<────────────┤             │                │       │        │
   │             │             │                │       │        │
   │ 7. Show     │             │                ▼       ▼        │
   │   "Moved to │             │         [Queue Worker]          │
   │    Work" +  │             │                │                │
   │   [Undo]    │             │                │                │
   │<────────────┤             │                │                │
   │             │             │                │                │
   │             │             │ 8. IMAP MOVE   │                │
   │             │             │    command     │                │
   │             │             ├───────────────────────────────>│
   │             │             │                │                │
   │             │             │                │   9. Moved     │
   │             │             │<───────────────────────────────┤
   │             │             │                │                │
   │             │             │ 10. Update     │                │
   │             │             │     folder     │                │
   │             │             │     counts     │                │
   │             │             ├───────────────>│                │
   │             │             │                │                │
   │             │             │ 11. Broadcast  │                │
   │             │             │     update     │                │
   │             │             ├────────────────────────┐        │
   │             │             │                │       │        │
   │             │             │                │       │        │
   │ 12. Real-time update of   │                │       │        │
   │     folder counts         │                │       │        │
   │<────────────┤<────────────┤                │       │        │
   │             │             │                │       │        │
   │             │             │                │       │        │
   │ (If user clicks [Undo] within 5 seconds)   │       │        │
   │             │             │                │       │        │
   │ 13. POST    │             │                │       │        │
   │  /emails/{id}/undo-move   │                │       │        │
   ├────────────>│             │                │       │        │
   │             ├────────────>│                │       │        │
   │             │             │                │       │        │
   │             │             │ 14. Reverse    │       │        │
   │             │             │     move       │       │        │
   │             │             ├───────────────────────────────>│
   │             │             │                │       │        │
```

**Key Points**:
- Optimistic UI update
- Undo feature (5-second window)
- Background IMAP sync
- Real-time folder count updates

---

## 11. OAuth Token Refresh

**Use Case**: Access token expired, refresh automatically

```
┌────────────┐     ┌────────┐     ┌──────────┐     ┌────────────┐
│Queue Worker│     │Laravel │     │ Database │     │   Google   │
│(Sync Job)  │     │ OAuth  │     │          │     │   OAuth    │
└─────┬──────┘     │ Service│     └────┬─────┘     └──────┬─────┘
      │            └───┬────┘          │                  │
      │                │                │                  │
      │ 1. Try to sync │                │                  │
      │    Gmail       │                │                  │
      │    account     │                │                  │
      ├───────────────>│                │                  │
      │                │                │                  │
      │                │ 2. Get OAuth   │                  │
      │                │    token       │                  │
      │                ├───────────────>│                  │
      │                │                │                  │
      │                │ 3. Return token│                  │
      │                │<───────────────┤                  │
      │                │                │                  │
      │                │ 4. Connect to  │                  │
      │                │    IMAP with   │                  │
      │                │    OAuth       │                  │
      │                ├────────────────────────┐          │
      │                │                │       │          │
      │                │                │       │          │
      │                │                │  5. 401 Unauth   │
      │                │                │     (token       │
      │                │                │      expired)    │
      │                │<────────────────────────┘          │
      │                │                │                  │
      │                │ 6. Detect      │                  │
      │                │    expired     │                  │
      │                │    token       │                  │
      │                ├────┐           │                  │
      │                │    │           │                  │
      │                │<───┘           │                  │
      │                │                │                  │
      │                │ 7. Get refresh │                  │
      │                │    token       │                  │
      │                ├───────────────>│                  │
      │                │<───────────────┤                  │
      │                │                │                  │
      │                │ 8. Request new │                  │
      │                │    access token│                  │
      │                ├───────────────────────────────────>│
      │                │                │                  │
      │                │                │  9. Validate     │
      │                │                │     refresh token│
      │                │                │                  │
      │                │                │ 10. Return new   │
      │                │                │     access token │
      │                │<───────────────────────────────────┤
      │                │                │                  │
      │                │ 11. Encrypt &  │                  │
      │                │     store new  │                  │
      │                │     token      │                  │
      │                ├───────────────>│                  │
      │                │                │                  │
      │                │ 12. Retry IMAP │                  │
      │                │     connection │                  │
      │                ├────────────────────────┐          │
      │                │                │       │          │
      │                │                │       │          │
      │                │                │  13. Success     │
      │                │<────────────────────────┘          │
      │                │                │                  │
      │ 14. Continue   │                │                  │
      │     sync       │                │                  │
      │<───────────────┤                │                  │
      │                │                │                  │
```

**Key Points**:
- Automatic token refresh (invisible to user)
- Uses refresh token to get new access token
- Re-attempts operation after refresh
- If refresh token invalid, notify user to re-authenticate

---

## 12. Error Handling - SMTP Failure

**Use Case**: Email send fails, retry and notify user

```
┌────────────┐     ┌────────┐     ┌──────────┐     ┌──────────┐     ┌───────┐
│Queue Worker│     │Laravel │     │ Database │     │   SMTP   │     │Browser│
│(SendEmail) │     │ Events │     │          │     │  Server  │     │(User) │
└─────┬──────┘     └───┬────┘     └────┬─────┘     └────┬─────┘     └───┬───┘
      │                │                │                │               │
      │ 1. Process     │                │                │               │
      │    SendEmailJob│                │                │               │
      ├────┐           │                │                │               │
      │    │           │                │                │               │
      │<───┘           │                │                │               │
      │                │                │                │               │
      │ 2. Connect to  │                │                │               │
      │    SMTP        │                │                │               │
      ├───────────────────────────────────────────────>│               │
      │                │                │                │               │
      │                │                │   3. Connection│               │
      │                │                │      timeout   │               │
      │<───────────────────────────────────────────────┤               │
      │                │                │                │               │
      │ 4. Retry #1    │                │                │               │
      │   (after 5s)   │                │                │               │
      ├────┐           │                │                │               │
      │    │           │                │                │               │
      │<───┘           │                │                │               │
      │                │                │                │               │
      │ 5. Connect     │                │                │               │
      │    again       │                │                │               │
      ├───────────────────────────────────────────────>│               │
      │                │                │                │               │
      │                │                │   6. Connection│               │
      │                │                │      timeout   │               │
      │<───────────────────────────────────────────────┤               │
      │                │                │                │               │
      │ 7. Retry #2    │                │                │               │
      │   (after 15s)  │                │                │               │
      ├────┐           │                │                │               │
      │    │           │                │                │               │
      │<───┘           │                │                │               │
      │                │                │                │               │
      │ 8. Connect     │                │                │               │
      ├───────────────────────────────────────────────>│               │
      │                │                │                │               │
      │                │                │   9. Connection│               │
      │                │                │      timeout   │               │
      │<───────────────────────────────────────────────┤               │
      │                │                │                │               │
      │ 10. All retries│                │                │               │
      │     exhausted  │                │                │               │
      ├────┐           │                │                │               │
      │    │           │                │                │               │
      │<───┘           │                │                │               │
      │                │                │                │               │
      │ 11. Mark email │                │                │               │
      │     as failed  │                │                │               │
      ├───────────────────────────────>│                │               │
      │                │                │                │               │
      │ 12. Move to    │                │                │               │
      │     Drafts     │                │                │               │
      ├───────────────────────────────>│                │               │
      │                │                │                │               │
      │ 13. Fire       │                │                │               │
      │   EmailSend    │                │                │               │
      │   Failed event │                │                │               │
      ├───────────────>│                │                │               │
      │                │                │                │               │
      │                │ 14. Broadcast  │                │               │
      │                │     to user    │                │               │
      │                ├────────────────────────────────────────────────>│
      │                │                │                │               │
      │                │                │                │   15. Show    │
      │                │                │                │       error   │
      │                │                │                │       toast   │
      │                │                │                │<──────────────┤
      │                │                │                │               │
      │                │                │                │   ❌ Failed   │
      │                │                │                │      to send  │
      │                │                │                │      email.   │
      │                │                │                │      SMTP     │
      │                │                │                │      server   │
      │                │                │                │      timeout. │
      │                │                │                │               │
      │                │                │                │   [Retry]     │
      │                │                │                │   [Edit]      │
      │                │                │                │               │
```

**Key Points**:
- Exponential backoff (5s, 15s, 45s)
- Max 3 retries
- On failure: move to Drafts, notify user
- User can retry or edit and resend

---

## Summary

These sequence diagrams cover the critical flows in Maylbox.co:

1. ✅ **Onboarding**: Fast signup and account connection
2. ✅ **Account Connection**: OAuth and IMAP flows
3. ✅ **Email Syncing**: Background IMAP sync
4. ✅ **Sending**: SMTP with queue and retry
5. ✅ **Reading**: Cached delivery with auto-mark-read
6. ✅ **Drafts**: Auto-save with IMAP sync
7. ✅ **Real-time**: WebSocket notifications
8. ✅ **Search**: Full-text via Meilisearch
9. ✅ **Organization**: Move with undo
10. ✅ **OAuth**: Automatic token refresh
11. ✅ **Error Handling**: Retry logic and user notifications

Each flow is designed for:
- **Performance**: Caching, queues, optimistic UI
- **Reliability**: Retries, error handling, rollback
- **User Experience**: Real-time updates, clear feedback, undo actions
