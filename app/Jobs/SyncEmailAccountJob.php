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
                'sync_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
