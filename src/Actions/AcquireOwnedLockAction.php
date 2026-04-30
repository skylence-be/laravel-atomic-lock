<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Actions;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Skylence\AtomicLock\Events\LockAcquired;
use Skylence\AtomicLock\Support\Config;
use Skylence\AtomicLock\Support\OwnedLock;

class AcquireOwnedLockAction
{
    public function execute(string $name, ?int $ttl = null): ?OwnedLock
    {
        $ttl = $ttl ?? Config::getDefaultTtl();
        $lockName = $this->buildLockName($name);
        $owner = Str::uuid()->toString();

        $lock = $this->getLock($lockName, $ttl, $owner);

        $acquired = $lock->get();

        if (!$acquired) {
            return null;
        }

        $ownedLock = new OwnedLock(
            name: $name,
            owner: $owner,
            ttl: $ttl,
            lock: $lock,
        );

        LockAcquired::dispatch(
            $name,
            new \Skylence\AtomicLock\Support\LockResult(
                acquired: true,
                name: $name,
                owner: $owner,
                ttl: $ttl,
            ),
        );

        return $ownedLock;
    }

    protected function buildLockName(string $name): string
    {
        $prefix = Config::getPrefix();

        return "{$prefix}:{$name}";
    }

    protected function getLock(string $name, int $ttl, string $owner): Lock
    {
        $store = Config::getCacheStore();
        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->lock($name, $ttl, $owner);
    }
}
