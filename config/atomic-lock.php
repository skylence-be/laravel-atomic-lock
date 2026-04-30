<?php

declare(strict_types=1);

use Skylence\AtomicLock\Actions\AcquireLockAction;
use Skylence\AtomicLock\Actions\AcquireModelLockAction;
use Skylence\AtomicLock\Actions\AcquireOwnedLockAction;
use Skylence\AtomicLock\Actions\BlockingAcquireLockAction;
use Skylence\AtomicLock\Actions\CheckLockStatusAction;
use Skylence\AtomicLock\Actions\ForceReleaseLockAction;
use Skylence\AtomicLock\Actions\RefreshLockAction;
use Skylence\AtomicLock\Actions\ReleaseLockAction;
use Skylence\AtomicLock\Actions\ReleaseModelLockAction;
use Skylence\AtomicLock\Actions\RestoreLockAction;
use Skylence\AtomicLock\Actions\SearchWithLockAction;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Lock TTL
    |--------------------------------------------------------------------------
    |
    | The default time-to-live in seconds for locks. If a lock is not manually
    | released, it will automatically expire after this duration.
    |
    */
    "default_ttl" => env("ATOMIC_LOCK_DEFAULT_TTL", 10),

    /*
    |--------------------------------------------------------------------------
    | Cache Store
    |--------------------------------------------------------------------------
    |
    | The cache store to use for locks. Leave null to use the default cache
    | store. Compatible drivers: database, file, redis, memcached.
    | Note: The 'array' driver only works within a single process.
    |
    */
    "cache_store" => env("ATOMIC_LOCK_CACHE_STORE"),

    /*
    |--------------------------------------------------------------------------
    | Lock Prefix
    |--------------------------------------------------------------------------
    |
    | A prefix applied to all lock names. This helps avoid conflicts with
    | other cache entries and makes locks easily identifiable.
    |
    */
    "prefix" => env("ATOMIC_LOCK_PREFIX", "atomic_lock"),

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    |
    | You can customize the action classes used for each operation. This allows
    | you to extend or replace the default behavior with your own implementations.
    |
    */
    "actions" => [
        // String-based locks
        "acquire" => AcquireLockAction::class,
        "blocking_acquire" => BlockingAcquireLockAction::class,
        "release" => ReleaseLockAction::class,
        "force_release" => ForceReleaseLockAction::class,
        "refresh" => RefreshLockAction::class,
        "check_status" => CheckLockStatusAction::class,

        // Owned locks (for transfer between processes)
        "acquire_owned" => AcquireOwnedLockAction::class,
        "restore" => RestoreLockAction::class,

        // Search lock pattern (for workers)
        "search_with_lock" => SearchWithLockAction::class,

        // Model-based locks (polymorphic)
        "acquire_model" => AcquireModelLockAction::class,
        "release_model" => ReleaseModelLockAction::class,
    ],
];
