<?php

namespace App\Services;

use App\Models\Email;
use App\Models\EmailAccount;
use App\Models\Folder;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Client as ImapClient;
use Webklex\PHPIMAP\Folder as ImapFolder;
use Webklex\PHPIMAP\Message;

class ImapSyncService
{
    private ?ImapClient $client = null;

    public function connect(EmailAccount $account): void
    {
        $this->client = Client::make([
            'host' => $account->imap_host,
            'port' => $account->imap_port,
            'encryption' => $account->imap_encryption === 'none' ? false : $account->imap_encryption,
            'validate_cert' => true,
            'username' => $account->imap_username ?: $account->email,
            'password' => $account->imap_password,
            'protocol' => 'imap',
            'authentication' => null,
        ]);

        $this->client->connect();
    }

    public function disconnect(): void
    {
        if ($this->client) {
            try {
                $this->client->disconnect();
            } catch (\Exception $e) {
                // Ignore disconnect errors
            }
            $this->client = null;
        }
    }

    public function syncFolders(EmailAccount $account): array
    {
        $imapFolders = $this->client->getFolders();
        $syncedFolders = [];

        foreach ($imapFolders as $imapFolder) {
            $remoteName = $imapFolder->path;
            $type = $this->classifyFolder($remoteName);
            $displayName = $this->getDisplayName($remoteName);

            $folder = Folder::updateOrCreate(
                [
                    'email_account_id' => $account->id,
                    'remote_name' => $remoteName,
                ],
                [
                    'name' => $displayName,
                    'type' => $type,
                ]
            );

            $syncedFolders[] = $folder;
        }

        return $syncedFolders;
    }

    public function syncMessages(Folder $folder, int $limit = 50): int
    {
        $imapFolder = $this->client->getFolderByPath($folder->remote_name);

        if (!$imapFolder) {
            return 0;
        }

        $maxStoredUid = Email::where('folder_id', $folder->id)->max('uid');

        $query = $imapFolder->messages();

        if ($maxStoredUid) {
            // Incremental sync: fetch messages with UID > max stored
            $messages = $query->setFetchOrder('desc')
                ->limit($limit)
                ->get();

            // Filter to only new messages
            $messages = $messages->filter(function (Message $message) use ($maxStoredUid) {
                return $message->getUid() > $maxStoredUid;
            });
        } else {
            // Initial sync: fetch last N messages
            $messages = $query->setFetchOrder('desc')
                ->limit($limit)
                ->get();
        }

        $synced = 0;

        foreach ($messages as $message) {
            try {
                $this->storeMessage($message, $folder);
                $synced++;
            } catch (\Exception $e) {
                logger()->warning('Failed to sync message', [
                    'folder_id' => $folder->id,
                    'uid' => $message->getUid(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Update folder counts
        $this->updateFolderCounts($folder, $imapFolder);

        return $synced;
    }

    private function storeMessage(Message $message, Folder $folder): Email
    {
        $from = $this->parseAddresses($message->getFrom());
        $to = $this->parseAddresses($message->getTo());
        $cc = $this->parseAddresses($message->getCc());
        $bcc = $this->parseAddresses($message->getBcc());
        $replyTo = $this->parseAddresses($message->getReplyTo());

        $bodyText = $message->getTextBody();
        $bodyHtml = $message->getHTMLBody();
        $snippet = Email::makeSnippet($bodyText ?: strip_tags($bodyHtml ?: ''));

        $attachments = $message->getAttachments();
        $hasAttachments = $attachments->count() > 0;
        $attachmentsMeta = [];

        foreach ($attachments as $attachment) {
            $attachmentsMeta[] = [
                'filename' => $attachment->getName() ?: 'unnamed',
                'contentType' => $attachment->getContentType() ?: 'application/octet-stream',
                'size' => $attachment->getSize() ?: 0,
                'isInline' => $attachment->getId() !== null,
            ];
        }

        $flags = $message->getFlags();
        $isRead = $flags->contains('Seen') || $flags->contains('\Seen');
        $isStarred = $flags->contains('Flagged') || $flags->contains('\Flagged');
        $isDraft = $flags->contains('Draft') || $flags->contains('\Draft');

        $messageId = $message->getMessageId()?->toString();
        $inReplyTo = $message->getInReplyTo()?->toString();
        $subject = $message->getSubject()?->toString() ?: '(No Subject)';
        $date = $message->getDate()?->toDate();
        $size = $message->getSize() ?? 0;

        return Email::updateOrCreate(
            [
                'folder_id' => $folder->id,
                'uid' => $message->getUid(),
            ],
            [
                'email_account_id' => $folder->email_account_id,
                'message_id' => $messageId ? trim($messageId, '<>') : null,
                'in_reply_to' => $inReplyTo ? trim($inReplyTo, '<>') : null,
                'from_email' => $from[0]['email'] ?? '',
                'from_name' => $from[0]['name'] ?? null,
                'to' => $to,
                'cc' => !empty($cc) ? $cc : null,
                'bcc' => !empty($bcc) ? $bcc : null,
                'reply_to' => !empty($replyTo) ? $replyTo : null,
                'subject' => $subject,
                'body_text' => $bodyText,
                'body_html' => $bodyHtml,
                'snippet' => $snippet,
                'date' => $date,
                'size' => $size,
                'is_read' => $isRead,
                'is_starred' => $isStarred,
                'is_draft' => $isDraft,
                'has_attachments' => $hasAttachments,
                'attachments_meta' => !empty($attachmentsMeta) ? $attachmentsMeta : null,
            ]
        );
    }

    private function updateFolderCounts(Folder $folder, ImapFolder $imapFolder): void
    {
        $status = $imapFolder->examine();

        $folder->update([
            'total_count' => $status['exists'] ?? Email::where('folder_id', $folder->id)->count(),
            'unread_count' => Email::where('folder_id', $folder->id)->where('is_read', false)->count(),
        ]);
    }

    public function classifyFolder(string $remoteName): string
    {
        $lower = mb_strtolower($remoteName);

        // Gmail-specific folders
        if (str_contains($lower, '[gmail]') || str_contains($lower, '[google mail]')) {
            if (str_contains($lower, 'sent')) return 'sent';
            if (str_contains($lower, 'draft')) return 'drafts';
            if (str_contains($lower, 'trash') || str_contains($lower, 'bin')) return 'trash';
            if (str_contains($lower, 'spam') || str_contains($lower, 'junk')) return 'spam';
            if (str_contains($lower, 'starred')) return 'starred';
            if (str_contains($lower, 'all mail')) return 'archive';
            if (str_contains($lower, 'important')) return 'custom';
        }

        // Standard folder names
        if ($lower === 'inbox') return 'inbox';
        if (str_contains($lower, 'sent')) return 'sent';
        if (str_contains($lower, 'draft')) return 'drafts';
        if (str_contains($lower, 'trash') || str_contains($lower, 'deleted')) return 'trash';
        if (str_contains($lower, 'spam') || str_contains($lower, 'junk')) return 'spam';
        if (str_contains($lower, 'archive')) return 'archive';

        return 'custom';
    }

    private function getDisplayName(string $remoteName): string
    {
        // For Gmail folders, use friendly names
        $lower = mb_strtolower($remoteName);

        if ($lower === 'inbox') return 'Inbox';

        // Extract the last part of the path for nested folders
        $parts = explode('/', $remoteName);
        $name = end($parts);

        // Also handle dot-separated paths
        if (str_contains($name, '.')) {
            $parts = explode('.', $name);
            $name = end($parts);
        }

        // Clean up Gmail prefix
        $name = preg_replace('/^\[Gmail\]\/?/i', '', $name);
        $name = preg_replace('/^\[Google Mail\]\/?/i', '', $name);

        return $name ?: $remoteName;
    }

    public function parseAddresses($addressCollection): array
    {
        $addresses = [];

        if (!$addressCollection) {
            return $addresses;
        }

        try {
            foreach ($addressCollection->toArray() as $address) {
                if (is_object($address)) {
                    $addresses[] = [
                        'email' => $address->mail ?? '',
                        'name' => $address->personal ?? null,
                    ];
                } elseif (is_array($address)) {
                    $addresses[] = [
                        'email' => $address['mail'] ?? $address['email'] ?? '',
                        'name' => $address['personal'] ?? $address['name'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Some messages have malformed address headers
        }

        return $addresses;
    }
}
