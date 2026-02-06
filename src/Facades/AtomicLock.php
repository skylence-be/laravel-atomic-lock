<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Theater\AtomicLock\Actions\AcquireLockAction;
use Theater\AtomicLock\Actions\AcquireOwnedLockAction;
use Theater\AtomicLock\Actions\BlockingAcquireLockAction;
use Theater\AtomicLock\Actions\CheckLockStatusAction;
use Theater\AtomicLock\Actions\ForceReleaseLockAction;
use Theater\AtomicLock\Actions\RefreshLockAction;
use Theater\AtomicLock\Actions\ReleaseLockAction;
use Theater\AtomicLock\Actions\RestoreLockAction;
use Theater\AtomicLock\Actions\SearchWithLockAction;
use Theater\AtomicLock\Support\Config;
use Theater\AtomicLock\Support\LockResult;
use Theater\AtomicLock\Support\LockStatus;
use Theater\AtomicLock\Support\OwnedLock;

/**
 * @method static LockResult acquire(string $name, ?int $ttl = null, ?string $owner = null, ?Closure $callback = null)
 * @method static LockResult block(string $name, int $waitSeconds, ?int $ttl = null, ?string $owner = null, ?Closure $callback = null)
 * @method static bool release(string $name, ?string $owner = null)
 * @method static void forceRelease(string $name)
 * @method static bool refresh(string $name, ?int $ttl = null, ?string $owner = null)
 * @method static LockStatus status(string $name)
 */
class AtomicLock extends Facade
{
    public static function acquire(
        string $name,
        ?int $ttl = null,
        ?string $owner = null,
        ?Closure $callback = null,
    ): LockResult {
        $action = Config::getAction('acquire', AcquireLockAction::class);

        return $action->execute($name, $ttl, $owner, $callback);
    }

    public static function block(
        string $name,
        int $waitSeconds,
        ?int $ttl = null,
        ?string $owner = null,
        ?Closure $callback = null,
    ): LockResult {
        $action = Config::getAction('blocking_acquire', BlockingAcquireLockAction::class);

        return $action->execute($name, $waitSeconds, $ttl, $owner, $callback);
    }

    public static function release(string $name, ?string $owner = null): bool
    {
        $action = Config::getAction('release', ReleaseLockAction::class);

        return $action->execute($name, $owner);
    }

    public static function forceRelease(string $name): void
    {
        $action = Config::getAction('force_release', ForceReleaseLockAction::class);

        $action->execute($name);
    }

    public static function refresh(string $name, ?int $ttl = null, ?string $owner = null): bool
    {
        $action = Config::getAction('refresh', RefreshLockAction::class);

        return $action->execute($name, $ttl, $owner);
    }

    public static function status(string $name): LockStatus
    {
        $action = Config::getAction('check_status', CheckLockStatusAction::class);

        return $action->execute($name);
    }

    public static function acquireOwned(string $name, ?int $ttl = null): ?OwnedLock
    {
        $action = Config::getAction('acquire_owned', AcquireOwnedLockAction::class);

        return $action->execute($name, $ttl);
    }

    public static function restore(string $name, string $owner, ?int $ttl = null): OwnedLock
    {
        $action = Config::getAction('restore', RestoreLockAction::class);

        return $action->execute($name, $owner, $ttl);
    }

    public static function restoreFromToken(string $serialized): OwnedLock
    {
        $action = Config::getAction('restore', RestoreLockAction::class);

        return $action->fromSerialized($serialized);
    }

    public static function searchWithLock(string $lockName, Closure $searchCallback, ?int $ttl = null): mixed
    {
        $action = Config::getAction('search_with_lock', SearchWithLockAction::class);

        return $action->execute($lockName, $searchCallback, $ttl);
    }
}
