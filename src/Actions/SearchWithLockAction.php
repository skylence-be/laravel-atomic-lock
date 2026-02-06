<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Actions;

use Closure;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Theater\AtomicLock\Support\Config;

class SearchWithLockAction
{
    public function execute(string $lockName, Closure $searchCallback, ?int $ttl = null): mixed
    {
        $ttl = $ttl ?? Config::getDefaultTtl();
        $fullLockName = $this->buildLockName($lockName);

        $lock = $this->getLock($fullLockName, $ttl);

        $acquired = $lock->get();

        if (! $acquired) {
            return null;
        }

        try {
            // Execute search while holding the lock
            // The callback should find and reserve a resource
            return $searchCallback();
        } finally {
            // Always release after search, even if nothing found
            $lock->release();
        }
    }

    protected function buildLockName(string $name): string
    {
        $prefix = Config::getPrefix();

        return "{$prefix}:search:{$name}";
    }

    protected function getLock(string $name, int $ttl): Lock
    {
        $store = Config::getCacheStore();
        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->lock($name, $ttl);
    }
}
