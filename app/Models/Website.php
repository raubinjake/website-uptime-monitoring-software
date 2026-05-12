<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'url',
        'is_active',
        'last_status',
        'last_status_code',
        'last_error',
        'last_checked_at',
        'last_alerted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
        'last_alerted_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function setUrlAttribute(string $value): void
    {
        $url = trim($value);

        $this->attributes['url'] = Str::startsWith($url, ['http://', 'https://'])
            ? rtrim($url, '/')
            : 'https://'.rtrim($url, '/');
    }
}
