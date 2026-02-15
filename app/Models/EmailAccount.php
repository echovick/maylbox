<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAccount extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'type',
        'provider',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'imap_host',
        'imap_port',
        'imap_encryption',
        'imap_username',
        'imap_password',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
        'is_default',
        'is_active',
        'sync_status',
        'last_synced_at',
        'sync_error',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
        'imap_password',
        'smtp_password',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get the user that owns the email account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Encrypt sensitive fields before saving.
     */
    public function setAccessTokenAttribute($value): void
    {
        $this->attributes['access_token'] = $value ? encrypt($value) : null;
    }

    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = $value ? encrypt($value) : null;
    }

    public function setImapPasswordAttribute($value): void
    {
        $this->attributes['imap_password'] = $value ? encrypt($value) : null;
    }

    public function setSmtpPasswordAttribute($value): void
    {
        $this->attributes['smtp_password'] = $value ? encrypt($value) : null;
    }

    /**
     * Decrypt sensitive fields when accessing.
     */
    public function getAccessTokenAttribute($value): ?string
    {
        return $value ? decrypt($value) : null;
    }

    public function getRefreshTokenAttribute($value): ?string
    {
        return $value ? decrypt($value) : null;
    }

    public function getImapPasswordAttribute($value): ?string
    {
        return $value ? decrypt($value) : null;
    }

    public function getSmtpPasswordAttribute($value): ?string
    {
        return $value ? decrypt($value) : null;
    }
}
