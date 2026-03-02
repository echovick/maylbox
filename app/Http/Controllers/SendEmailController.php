<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\EmailAccount;
use App\Models\Folder;
use App\Services\SmtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SendEmailController extends Controller
{
    public function __invoke(Request $request, SmtpService $smtp): JsonResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'integer', 'exists:email_accounts,id'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'to' => ['required', 'array', 'min:1'],
            'to.*.email' => ['required', 'email'],
            'to.*.name' => ['nullable', 'string'],
            'cc' => ['nullable', 'array'],
            'cc.*.email' => ['required', 'email'],
            'cc.*.name' => ['nullable', 'string'],
            'bcc' => ['nullable', 'array'],
            'bcc.*.email' => ['required', 'email'],
            'bcc.*.name' => ['nullable', 'string'],
            'subject' => ['required', 'string', 'max:998'],
            'body_html' => ['required', 'string'],
            'in_reply_to' => ['nullable', 'string'],
        ]);

        // Authorize: account belongs to current user
        $account = EmailAccount::findOrFail($validated['account_id']);

        if ($account->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$account->is_active) {
            return response()->json(['message' => 'This email account is not active.'], 422);
        }

        if (!$account->smtp_host || !$account->smtp_password) {
            return response()->json(['message' => 'SMTP credentials are not configured for this account.'], 422);
        }

        // Send
        try {
            $messageId = $smtp->send($account, $validated);
        } catch (TransportExceptionInterface $e) {
            logger()->error('SMTP send failed', [
                'account_id' => $account->id,
                'type' => $account->type,
                'smtp_host' => $account->smtp_host,
                'smtp_port' => $account->smtp_port,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => $this->friendlyError($e),
            ], 422);
        }

        // Store in sent folder
        $sentFolder = Folder::firstOrCreate(
            [
                'email_account_id' => $account->id,
                'type' => 'sent',
            ],
            [
                'name' => 'Sent',
                'remote_name' => 'Sent',
            ]
        );

        $bodyHtml = $validated['body_html'];
        $bodyText = strip_tags($bodyHtml);

        // Strip quoted text for snippet (reply/forward quoted blocks)
        $snippetHtml = preg_replace('/<div\s+style="border-left:\s*2px.*?<\/div>/is', '', $bodyHtml);
        $snippetHtml = preg_replace('/<div\s+style="border:\s*1px.*?<\/div>/is', '', $snippetHtml);
        $snippetText = trim(strip_tags($snippetHtml));

        $email = Email::create([
            'email_account_id' => $account->id,
            'folder_id' => $sentFolder->id,
            'uid' => Email::where('folder_id', $sentFolder->id)->max('uid') + 1,
            'message_id' => $messageId,
            'in_reply_to' => $validated['in_reply_to'] ?? null,
            'from_email' => $account->email,
            'from_name' => $validated['from_name'] ?? $account->name,
            'to' => $validated['to'],
            'cc' => $validated['cc'] ?? null,
            'bcc' => $validated['bcc'] ?? null,
            'subject' => $validated['subject'],
            'body_text' => $bodyText,
            'body_html' => $bodyHtml,
            'snippet' => Email::makeSnippet($snippetText ?: $bodyText),
            'date' => now(),
            'size' => strlen($validated['body_html']),
            'is_read' => true,
            'is_starred' => false,
            'is_draft' => false,
            'has_attachments' => false,
        ]);

        // Update folder counts
        $sentFolder->update([
            'total_count' => Email::where('folder_id', $sentFolder->id)->count(),
        ]);

        return response()->json([
            'message' => 'Email sent successfully.',
            'email' => $email,
        ], 201);
    }

    private function friendlyError(TransportExceptionInterface $e): string
    {
        $message = $e->getMessage();

        if (stripos($message, 'Authentication') !== false || stripos($message, 'credentials') !== false || stripos($message, '535') !== false) {
            return 'SMTP authentication failed. Please check your email password and try again.';
        }

        if (stripos($message, 'Connection refused') !== false || stripos($message, 'Connection timed out') !== false || stripos($message, 'Connection could not be established') !== false) {
            return 'Could not connect to the SMTP server. Please check your SMTP host and port settings.';
        }

        if (stripos($message, 'certificate') !== false || stripos($message, 'SSL') !== false || stripos($message, 'TLS') !== false) {
            return 'SSL/TLS error connecting to the SMTP server. Please check your encryption settings.';
        }

        return 'Failed to send email: ' . Str::limit($message, 120);
    }
}
