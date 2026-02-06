<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Lock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lockable_type',
        'lockable_id',
        'owner',
        'reason',
        'acquired_at',
        'released_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'acquired_at' => 'datetime',
            'released_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function lockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isActive(): bool
    {
        return $this->released_at === null && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isReleased(): bool
    {
        return $this->released_at !== null;
    }

    public function scopeActive($query)
    {
        return $query
            ->whereNull('released_at')
            ->where('expires_at', '>', now());
    }

    public function scopeForLockable($query, Model $model)
    {
        return $query
            ->where('lockable_type', $model->getMorphClass())
            ->where('lockable_id', $model->getKey());
    }
}
