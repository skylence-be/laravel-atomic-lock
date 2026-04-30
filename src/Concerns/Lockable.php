<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Skylence\AtomicLock\Actions\AcquireModelLockAction;
use Skylence\AtomicLock\Actions\ReleaseModelLockAction;
use Skylence\AtomicLock\Models\Lock;
use Skylence\AtomicLock\Support\Config;
use Skylence\AtomicLock\Support\LockResult;

trait Lockable
{
    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, "lockable");
    }

    public function activeLock(): ?Lock
    {
        return $this->locks()->active()->first();
    }

    public function isLocked(): bool
    {
        return $this->activeLock() !== null;
    }

    public function isNotLocked(): bool
    {
        return !$this->isLocked();
    }

    public function acquireLock(
        ?int $ttl = null,
        ?string $owner = null,
        ?string $reason = null,
    ): LockResult {
        $action = Config::getAction(
            "acquire_model",
            AcquireModelLockAction::class,
        );

        return $action->execute($this, $ttl, $owner, $reason);
    }

    public function releaseLock(?string $owner = null): bool
    {
        $action = Config::getAction(
            "release_model",
            ReleaseModelLockAction::class,
        );

        return $action->execute($this, $owner);
    }

    public function forceReleaseLock(): bool
    {
        $lock = $this->activeLock();

        if (!$lock) {
            return false;
        }

        $lock->update(["released_at" => now()]);

        return true;
    }

    public function lockHistory(): MorphMany
    {
        return $this->locks()->orderByDesc("acquired_at");
    }
}
