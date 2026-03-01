<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function index(EmailAccount $emailAccount)
    {
        abort_unless($emailAccount->user_id === Auth::id(), 403);

        $typePriority = ['inbox', 'sent', 'drafts', 'trash', 'spam', 'archive', 'custom'];

        $folders = $emailAccount->folders()
            ->get()
            ->sortBy(function ($folder) use ($typePriority) {
                $index = array_search($folder->type, $typePriority);
                return $index === false ? 999 : $index;
            })
            ->values();

        return response()->json($folders);
    }
}
