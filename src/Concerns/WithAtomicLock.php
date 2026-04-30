<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Concerns;

use Closure;
use Skylence\AtomicLock\Facades\AtomicLock;
use Skylence\AtomicLock\Support\LockResult;

trait WithAtomicLock
{
    protected function acquireLock(
        string $name,
        ?int $ttl = null,
        ?Closure $callback = null,
    ): LockResult {
        return AtomicLock::acquire($name, $ttl, callback: $callback);
    }

    protected function blockForLock(
        string $name,
        int $waitSeconds,
        ?int $ttl = null,
        ?Closure $callback = null,
    ): LockResult {
        return AtomicLock::block(
            $name,
            $waitSeconds,
            $ttl,
            callback: $callback,
        );
    }

    protected function releaseLock(string $name): bool
    {
        return AtomicLock::release($name);
    }

    protected function runWithLock(
        string $name,
        Closure $callback,
        ?int $ttl = null,
    ): mixed {
        $result = AtomicLock::acquire($name, $ttl, callback: $callback);

        if ($result->wasNotAcquired()) {
            return null;
        }

        return true;
    }

    protected function runWithBlockingLock(
        string $name,
        Closure $callback,
        int $waitSeconds = 10,
        ?int $ttl = null,
    ): mixed {
        AtomicLock::block($name, $waitSeconds, $ttl, callback: $callback);

        return true;
    }
}
