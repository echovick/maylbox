<?php

namespace App\Http\Controllers;

use App\Jobs\SyncEmailAccountJob;
use App\Models\EmailAccount;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    public function sync(EmailAccount $emailAccount)
    {
        abort_unless($emailAccount->user_id === Auth::id(), 403);

        if ($emailAccount->sync_status === 'syncing') {
            return response()->json([
                'message' => 'Sync already in progress',
                'sync_status' => $emailAccount->sync_status,
            ]);
        }

        SyncEmailAccountJob::dispatch($emailAccount);

        $emailAccount->update(['sync_status' => 'pending']);

        return response()->json([
            'message' => 'Sync started',
            'sync_status' => 'pending',
        ]);
    }

    public function status(EmailAccount $emailAccount)
    {
        abort_unless($emailAccount->user_id === Auth::id(), 403);

        return response()->json([
            'sync_status' => $emailAccount->sync_status,
            'last_synced_at' => $emailAccount->last_synced_at,
            'sync_error' => $emailAccount->sync_error,
        ]);
    }
}
