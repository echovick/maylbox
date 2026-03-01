<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    protected $fillable = [
        'email_account_id',
        'folder_id',
        'uid',
        'message_id',
        'in_reply_to',
        'from_email',
        'from_name',
        'to',
        'cc',
        'bcc',
        'reply_to',
        'subject',
        'body_text',
        'body_html',
        'snippet',
        'date',
        'size',
        'is_read',
        'is_starred',
        'is_draft',
        'has_attachments',
        'attachments_meta',
    ];

    protected $casts = [
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'reply_to' => 'array',
        'attachments_meta' => 'array',
        'date' => 'datetime',
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'is_draft' => 'boolean',
        'has_attachments' => 'boolean',
        'uid' => 'integer',
        'size' => 'integer',
    ];

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public static function makeSnippet(?string $bodyText): string
    {
        if (!$bodyText) {
            return '';
        }

        $text = preg_replace('/\s+/', ' ', trim($bodyText));

        return mb_substr($text, 0, 200);
    }
}
