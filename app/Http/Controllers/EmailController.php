<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'account_id' => 'required|integer',
            'folder_id' => 'required|integer',
            'page' => 'integer|min:1',
        ]);

        // Verify account ownership
        $accountId = $request->input('account_id');
        $hasAccount = Auth::user()->emailAccounts()->where('id', $accountId)->exists();
        abort_unless($hasAccount, 403);

        $emails = Email::where('email_account_id', $accountId)
            ->where('folder_id', $request->input('folder_id'))
            ->select([
                'id', 'email_account_id', 'folder_id', 'uid', 'message_id',
                'from_email', 'from_name', 'to', 'subject', 'snippet', 'date',
                'is_read', 'is_starred', 'is_draft', 'has_attachments', 'attachments_meta',
            ])
            ->orderByDesc('date')
            ->paginate(20);

        return response()->json($emails);
    }

    public function show(Email $email)
    {
        abort_unless($email->emailAccount->user_id === Auth::id(), 403);

        return response()->json($email);
    }

    public function update(Request $request, Email $email)
    {
        abort_unless($email->emailAccount->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'is_read' => 'sometimes|boolean',
            'is_starred' => 'sometimes|boolean',
        ]);

        $wasRead = $email->is_read;
        $email->update($validated);

        // Recalculate folder unread count if read status changed
        if (isset($validated['is_read']) && $wasRead !== $validated['is_read']) {
            $folder = $email->folder;
            $folder->update([
                'unread_count' => $folder->emails()->where('is_read', false)->count(),
            ]);
        }

        return response()->json($email);
    }
}
