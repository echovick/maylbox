<?php

namespace App\Jobs;

use App\Models\EmailAccount;
use App\Services\ImapSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncEmailAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(
        public EmailAccount $account,
        public int $messageLimit = 50,
    ) {}

    public function handle(ImapSyncService $sync): void
    {
        $this->account->update(['sync_status' => 'syncing', 'sync_error' => null]);

        try {
            $sync->connect($this->account);

            $folders = $sync->syncFolders($this->account);

            // Priority folders get the full message limit
            $priorityTypes = ['inbox', 'sent', 'drafts'];

            foreach ($folders as $folder) {
                $limit = in_array($folder->type, $priorityTypes)
                    ? $this->messageLimit
                    : max(10, intdiv($this->messageLimit, 5));

                $sync->syncMessages($folder, $limit);
            }

            $sync->disconnect();

            $this->account->update([
                'sync_status' => 'synced',
                'last_synced_at' => now(),
                'sync_error' => null,
            ]);
        } catch (\Exception $e) {
            $sync->disconnect();

            $this->account->update([
                'sync_status' => 'failed',
                'sync_error' => $this->friendlyError($e),
            ]);

            throw $e;
        }
    }

    private function friendlyError(\Exception $e): string
    {
        $message = $e->getMessage();

        if (stripos($message, 'AUTHENTICATIONFAILED') !== false || stripos($message, 'Authentication failed') !== false) {
            return 'Authentication failed. Please check your email password and try again.';
        }

        if (stripos($message, 'Connection refused') !== false || stripos($message, 'Connection timed out') !== false) {
            return 'Could not connect to the mail server. Please check your IMAP host and port settings.';
        }

        if (stripos($message, 'certificate') !== false || stripos($message, 'SSL') !== false) {
            return 'SSL/TLS error connecting to the mail server. Please check your encryption settings.';
        }

        return 'Sync failed: ' . \Illuminate\Support\Str::limit($message, 120);
    }
}
