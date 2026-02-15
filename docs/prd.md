Alright. No fluff. No startup cosplay. Let’s write something that could actually guide engineering.

Below is a clean, execution-ready Product Requirements Document for **Maylbox**.

---

# Product Requirements Document (PRD)

## Product Name

Maylbox

## 1. Product Overview

Maylbox is a modern, AI-ready email client that allows users to connect existing email accounts (custom domains, Gmail, Microsoft, etc.) and access them through a fast, clean, intelligent interface.

Maylbox does not provide email hosting. It acts as a secure sync and control layer on top of existing providers using OAuth (where available) and IMAP/SMTP.

Core promise:
Sign up → connect inbox → send email in under 60 seconds.

---

## 2. Problem Statement

Many users purchase custom domain emails through providers like shared hosting platforms. While functional, the default webmail interfaces are outdated, slow, and unintuitive.

Users face:

- Poor UI/UX
- Complicated forwarding setup
- Manual SMTP/IMAP configuration
- Difficulty consolidating multiple inboxes
- Lack of intelligent workflow features

Switching to premium providers like Google Workspace solves UX but increases cost and vendor lock-in.

There is a gap for a seamless, intelligent, provider-agnostic email client.

---

## 3. Target Users

Primary:

- Founders and SMB owners using custom domain email via shared hosting
- Agencies managing multiple client inboxes
- Freelancers with multiple domain emails

Secondary:

- Power users wanting AI-first inbox management
- Users who dislike Google Workspace pricing or ecosystem

---

## 4. Product Goals

1. Enable inbox connection in ≤ 3 steps.
2. Hide all technical configuration unless required.
3. Deliver sub-2 second perceived load time.
4. Provide modern UI comparable to Gmail-level usability.
5. Enable extensibility for AI automation and workflow features.

---

## 5. Non-Goals (Phase 1)

- Running mail servers
- Managing IP reputation
- Email hosting infrastructure
- Competing on delivery infrastructure

Maylbox is a client + sync layer, not a provider.

---

## 6. Core Features (Phase 1 – MVP)

### 6.1 Seamless Account Connection

#### Supported Methods

1. OAuth (Priority)
    - Gmail
    - Microsoft 365 / Outlook
    - Yahoo

2. Smart IMAP Auto-Config
    - User enters email + password
    - System performs:
        - MX record lookup
        - Provider preset detection
        - Port auto-detection

    - Attempts secure connection
    - Falls back to advanced settings only if auto-config fails

Users never see IMAP/SMTP fields unless connection fails.

---

### 6.2 Mail Sync Engine

Architecture:

IMAP Provider → Maylbox Sync Engine → Internal Mail Store → API → Frontend

Responsibilities:

- Fetch last 30 days immediately
- Background sync older mail
- Track UID validity
- Sync flags (read/unread, starred, etc.)
- Send mail via SMTP or provider API
- Maintain connection pooling

System must:

- Retry gracefully
- Handle provider rate limits
- Log sync errors without exposing credentials

---

### 6.3 Secure Credential Handling

- Encrypt credentials at rest (AES-256 or equivalent)
- Key management separate from database
- No plaintext logging
- OAuth tokens stored securely
- Credential rotation supported
- Strict access control policies

Security breach impact must be minimized by architecture.

---

### 6.4 Inbox Experience

Core Requirements:

- Minimal, fast interface
- Multi-account support
- Unified inbox (optional toggle)
- Categories (Primary, Clients, Finance, etc.)
- Fast search (indexed locally in Maylbox store)
- Threaded conversations
- Real-time unread indicators

Perceived performance target:
Inbox visible within 2 seconds after login.

---

### 6.5 Send & Compose

- Rich text compose
- Attachments
- Multiple identities per account
- SMTP sending with delivery feedback
- Draft autosave
- Reply / Reply All / Forward

Future support for AI-generated replies.

---

## 7. UX Principles

1. No configuration vocabulary on onboarding.
2. No settings before inbox.
3. Progressive disclosure for advanced users.
4. Fewer decisions = better experience.
5. Modern, calm UI (Notion-like clarity, Gmail-level practicality).

First screen after connection = Inbox.

---

## 8. Performance Requirements

- First inbox render: ≤ 2 seconds
- Account connection flow: ≤ 60 seconds
- Sync engine must handle:
    - 10k emails per account
    - Background indexing

- Zero UI blocking during full mailbox sync

---

## 9. System Architecture

### Components

1. Authentication Layer
2. Provider Detection Engine
    - MX lookup service
    - Provider preset database

3. Sync Engine Workers
4. Mail Storage Database
5. Search Index (e.g., full-text engine)
6. API Layer
7. Frontend App

Background jobs required for:

- Incremental sync
- Attachment processing
- Indexing
- Retry queues

---

## 10. Risks

1. IMAP edge case handling complexity
2. Provider throttling or blocking
3. Credential breach risk
4. Performance bottlenecks with large mailboxes
5. User expectation comparison to Gmail

Mitigation:

- Build robust retry logic
- Strict encryption policy
- Start with limited provider support
- Test heavily on shared hosting providers

---

## 11. Future Phases

### Phase 2 – Intelligence Layer

- AI email summarization
- Smart reply suggestions
- Auto-categorization
- Follow-up reminders
- Snooze functionality

### Phase 3 – Workflow Layer

- Email → Task conversion
- Email → CRM record
- Kanban-style inbox
- Shared team inbox
- Automation rules engine

At this stage, Maylbox becomes more than a client — it becomes a communication operating layer.

---

## 12. Success Metrics

- Time to first sent email
- Inbox connection success rate
- Weekly active users
- Sync error rate
- Retention after 30 days

---

## 13. Strategic Positioning

Maylbox is not:

- An email hosting provider
- A Gmail clone

Maylbox is:
A seamless, intelligent inbox for custom-domain users who want modern UX without switching providers.

Long-term vision:
Turn email from a passive inbox into an actionable workflow surface.

---

This is buildable.

Hard? Yes.
Infrastructure nightmare? No.
Execution-sensitive? Absolutely.

If you want next, we can break this into a 12-week build roadmap with technical stack recommendations and infra sizing assumptions.
