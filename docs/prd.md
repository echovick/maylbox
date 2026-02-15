# Maylbox.co - Product Requirements Document

## Executive Summary

**Maylbox.co** is a modern webmail client designed to be a "sexier version of webmail" - combining the power of traditional email with a delightful, streamlined user experience. The core mission is to minimize friction: users should go from signup to sending their first email in fewer than 5 clicks.

## Product Vision

### Problem Statement
Traditional webmail clients are cluttered, slow, and require too many steps to accomplish basic tasks. Users face:
- Complex onboarding processes
- Unintuitive interfaces
- Slow performance
- Overwhelming feature sets that obscure core functionality

### Solution
Maylbox.co provides a clean, fast, and intuitive email experience that:
- Streamlines onboarding (signup ’ first email in <5 clicks)
- Offers a modern, beautiful interface
- Delivers instant performance through smart caching and background sync
- Focuses on core email workflows while keeping advanced features accessible

## Target Users

### Primary Personas

**1. The Switcher**
- Currently uses Gmail, Outlook, or other webmail
- Frustrated with cluttered interfaces and privacy concerns
- Values simplicity and speed
- Willing to try new tools that improve their workflow

**2. The Professional**
- Manages multiple email accounts
- Needs reliable, fast email access
- Values clean, distraction-free interface
- Wants advanced features without complexity

**3. The Privacy-Conscious**
- Concerned about data mining in free email services
- Wants control over their data
- Prefers self-hosted or privacy-first solutions
- Technical enough to appreciate IMAP/SMTP standards

## Core Features

### MVP (Phase 1)

#### 1. Authentication & Onboarding
- **Quick signup** with email/password
- **Instant email account connection** via IMAP/SMTP
- **Auto-configuration** for popular providers (Gmail, Outlook, etc.)
- **Email verification** (optional, skippable for speed)

#### 2. Email Management
- **Inbox view** with threaded conversations
- **Email composition** with rich text editor
- **Send/receive** via SMTP/IMAP
- **Attachments** support (send/receive)
- **Basic folders**: Inbox, Sent, Drafts, Trash, Spam
- **Search** across all emails

#### 3. Multiple Accounts
- **Add multiple email accounts**
- **Unified inbox** (optional)
- **Per-account views**
- **Account switching**

#### 4. Core UI/UX
- **Responsive design** (desktop-first, mobile-friendly)
- **Dark/light mode**
- **Keyboard shortcuts**
- **Real-time updates** for new emails
- **Optimistic UI** updates

### Phase 2 (Post-MVP)

#### 1. Advanced Email Features
- **Labels/tags** (in addition to folders)
- **Email filters/rules**
- **Snooze emails**
- **Templates**
- **Email scheduling**
- **Read receipts**

#### 2. Productivity Features
- **Contact management**
- **Signatures** (per account)
- **Quick replies**
- **Email reminders**
- **Calendar integration** (view only)

#### 3. Collaboration
- **Shared inboxes** (team accounts)
- **Email delegation**
- **Notes on conversations**
- **Internal email sharing**

### Phase 3 (Future)

- **Mobile apps** (iOS/Android)
- **Desktop apps** (Electron/Tauri)
- **Browser extensions**
- **Advanced search** (natural language)
- **AI features** (smart compose, summarization)
- **Custom domains**
- **Email hosting** (optional paid service)

## User Experience Goals

### The "5-Click Rule"
From landing page to sending first email in 5 clicks:
1. Click "Sign Up"
2. Enter credentials & submit
3. Connect email account (auto-detected)
4. Click "Compose"
5. Send email

### Performance Benchmarks
- **Page load**: <1s (initial)
- **Inbox load**: <500ms
- **Email open**: <200ms
- **Compose open**: <100ms
- **Search results**: <300ms

### Design Principles
1. **Speed First**: Every interaction should feel instant
2. **Clarity Over Features**: Hide complexity, surface what matters
3. **Beautiful Defaults**: Works great out of the box
4. **Progressive Enhancement**: Advanced features don't clutter core experience
5. **Accessibility**: WCAG 2.1 AA compliance

## Technical Requirements

### Frontend
- **Framework**: Vue 3 + Inertia.js (current stack)
- **UI Library**: Reka UI + Tailwind CSS (current)
- **State Management**: Vue Composables + useVueuse
- **Rich Text**: Tiptap or Lexical
- **File Uploads**: Drag-and-drop, paste from clipboard
- **Real-time**: Laravel Echo + WebSockets (or Server-Sent Events)

### Backend
- **Framework**: Laravel 12 (current)
- **Mail Protocol**: IMAP (receive), SMTP (send)
- **Queue System**: Laravel Queues (Redis/Database)
- **Cache**: Redis (email metadata, user sessions)
- **Storage**: S3-compatible (attachments)
- **Database**: PostgreSQL (production), SQLite (dev)

### Security
- **OAuth 2.0**: For Gmail, Outlook account connections
- **Encrypted Storage**: Email credentials encrypted at rest
- **HTTPS Only**: All traffic encrypted
- **CSRF Protection**: Laravel built-in
- **Rate Limiting**: API and email sending
- **2FA**: TOTP support (already implemented)

### Email Protocol Support
- **IMAP**: For receiving/syncing emails
- **SMTP**: For sending emails
- **OAuth 2.0**: For Gmail, Microsoft, Yahoo
- **App Passwords**: For providers requiring them
- **Auto-Discovery**: Autoconfig/Autodiscover for provider settings

## Database Schema (High-Level)

### Core Entities
- **Users**: Authentication & profile
- **EmailAccounts**: IMAP/SMTP credentials, settings
- **Emails**: Message content, metadata
- **EmailThreads**: Conversation grouping
- **Folders**: Inbox, Sent, custom folders
- **Attachments**: File metadata & storage links
- **Contacts**: Email addresses, names
- **Labels**: User-defined tags
- **Filters**: Email automation rules

## Success Metrics

### Primary KPIs
- **Time to First Email Sent**: <2 minutes from signup
- **Daily Active Users (DAU)**
- **Email Send Success Rate**: >99%
- **Email Sync Latency**: <30 seconds for new emails
- **User Retention**: 7-day, 30-day cohorts

### Secondary Metrics
- **Average Emails Sent per User per Day**
- **Email Account Connections per User**
- **Search Usage Rate**
- **Feature Adoption**: Filters, labels, shortcuts
- **Performance**: P95 load times

## Risk & Mitigation

### Technical Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| IMAP provider rate limits | High | Implement smart polling, respect provider limits |
| Email storage costs | Medium | Metadata-only storage, attachment compression |
| OAuth token expiration | Medium | Automatic refresh token handling |
| Spam/abuse | High | Rate limiting, email verification, abuse detection |

### Product Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| User trust (new product) | High | Clear privacy policy, open-source consideration |
| Provider blocking | High | Respect RFC standards, implement backoff |
| Feature bloat | Medium | Strict adherence to "5-click rule" philosophy |

## Launch Plan

### Beta Phase (Week 1-4)
- Invite-only signups
- Support for Gmail, Outlook, generic IMAP
- Core email features only
- Gather feedback, iterate quickly

### Public Launch (Week 5-8)
- Open signups
- Marketing campaign
- Documentation & tutorials
- Support for all major providers

### Growth Phase (Week 9+)
- Phase 2 features rollout
- Performance optimizations
- Mobile-responsive refinements
- Community building

## Open Questions

1. **Monetization**: Free tier vs. paid plans? Storage limits?
2. **Email Hosting**: Should we offer @maylbox.co addresses?
3. **Spam Filtering**: Client-side ML vs. server-side processing?
4. **Calendar Integration**: Full feature or read-only?
5. **Branding**: Color palette, logo finalization?

## Appendix

### Competitive Analysis
- **Gmail**: Most popular, but cluttered and privacy concerns
- **Outlook**: Enterprise-focused, heavy interface
- **Superhuman**: Fast but expensive ($30/month), keyboard-centric
- **Hey**: Opinionated workflows, requires Hey email address
- **Fastmail**: Solid but dated UI
- **ProtonMail**: Privacy-focused but slow

### Inspiration
- **Superhuman**: Speed and keyboard shortcuts
- **Linear**: Clean, fast, delightful UX
- **Vercel Dashboard**: Modern, minimalist design
- **Hey**: Bold opinions on email workflows
