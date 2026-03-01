<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    protected $fillable = [
        'email_account_id',
        'name',
        'type',
        'remote_name',
        'unread_count',
        'total_count',
    ];

    protected $casts = [
        'unread_count' => 'integer',
        'total_count' => 'integer',
    ];

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }
}
