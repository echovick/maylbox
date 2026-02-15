<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmailAccountController extends Controller
{
    /**
     * Get all email accounts for the authenticated user.
     */
    public function index()
    {
        $accounts = Auth::user()->emailAccounts()
            ->select(['id', 'name', 'email', 'type', 'provider', 'is_default', 'is_active', 'sync_status', 'last_synced_at'])
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($accounts);
    }

    /**
     * Store a new email account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('email_accounts')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'type' => 'required|in:oauth,imap',
            'provider' => 'nullable|string|in:gmail,outlook',

            // OAuth fields
            'access_token' => 'nullable|string',
            'refresh_token' => 'nullable|string',

            // IMAP fields
            'imap_host' => 'required_if:type,imap|nullable|string',
            'imap_port' => 'required_if:type,imap|nullable|integer',
            'imap_encryption' => 'nullable|string|in:ssl,tls,none',
            'imap_username' => 'nullable|string',
            'imap_password' => 'required_if:type,imap|nullable|string',

            // SMTP fields
            'smtp_host' => 'required_if:type,imap|nullable|string',
            'smtp_port' => 'required_if:type,imap|nullable|integer',
            'smtp_encryption' => 'nullable|string|in:ssl,tls,none',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'required_if:type,imap|nullable|string',
        ]);

        // Set as default if this is the user's first account
        $isFirstAccount = Auth::user()->emailAccounts()->count() === 0;

        $account = Auth::user()->emailAccounts()->create([
            ...$validated,
            'is_default' => $isFirstAccount,
            'sync_status' => 'pending',
            'imap_username' => $validated['imap_username'] ?? $validated['email'],
            'smtp_username' => $validated['smtp_username'] ?? $validated['email'],
        ]);

        // TODO: Queue email sync job
        // SyncEmailAccountJob::dispatch($account);

        return response()->json([
            'message' => 'Email account connected successfully',
            'account' => $account->only(['id', 'name', 'email', 'type', 'provider', 'is_default', 'sync_status']),
        ], 201);
    }

    /**
     * Update an email account.
     */
    public function update(Request $request, EmailAccount $emailAccount)
    {
        $this->authorize('update', $emailAccount);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        // If setting as default, unset other defaults
        if (isset($validated['is_default']) && $validated['is_default']) {
            Auth::user()->emailAccounts()
                ->where('id', '!=', $emailAccount->id)
                ->update(['is_default' => false]);
        }

        $emailAccount->update($validated);

        return response()->json([
            'message' => 'Email account updated successfully',
            'account' => $emailAccount->only(['id', 'name', 'email', 'is_default', 'is_active']),
        ]);
    }

    /**
     * Delete an email account.
     */
    public function destroy(EmailAccount $emailAccount)
    {
        $this->authorize('delete', $emailAccount);

        $emailAccount->delete();

        return response()->json([
            'message' => 'Email account deleted successfully',
        ]);
    }
}
