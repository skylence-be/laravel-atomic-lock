<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Actions;

use Closure;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Theater\AtomicLock\Events\LockAcquired;
use Theater\AtomicLock\Support\Config;
use Theater\AtomicLock\Support\LockResult;

class AcquireLockAction
{
    public function execute(
        string $name,
        ?int $ttl = null,
        ?string $owner = null,
        ?Closure $callback = null,
    ): LockResult {
        $ttl = $ttl ?? Config::getDefaultTtl();
        $lockName = $this->buildLockName($name);

        $lock = $this->getLock($lockName, $ttl, $owner);

        if ($callback !== null) {
            $acquired = $lock->get($callback);

            $result = new LockResult(
                acquired: $acquired !== false,
                name: $name,
                owner: $owner,
                ttl: $ttl,
            );

            LockAcquired::dispatch($name, $result);

            return $result;
        }

        $acquired = $lock->get();

        $result = new LockResult(
            acquired: $acquired,
            name: $name,
            owner: $owner,
            ttl: $ttl,
        );

        LockAcquired::dispatch($name, $result);

        return $result;
    }

    protected function buildLockName(string $name): string
    {
        $prefix = Config::getPrefix();

        return "{$prefix}:{$name}";
    }

    protected function getLock(string $name, int $ttl, ?string $owner): Lock
    {
        $store = Config::getCacheStore();
        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->lock($name, $ttl, $owner);
    }
}
