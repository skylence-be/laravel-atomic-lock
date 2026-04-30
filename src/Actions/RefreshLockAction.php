<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Actions;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Skylence\AtomicLock\Events\LockRefreshed;
use Skylence\AtomicLock\Support\Config;

class RefreshLockAction
{
    public function execute(
        string $name,
        ?int $ttl = null,
        ?string $owner = null,
    ): bool {
        $ttl = $ttl ?? Config::getDefaultTtl();
        $lockName = $this->buildLockName($name);

        $lock = $this->getLock($lockName, $ttl, $owner);

        // Release and re-acquire to refresh
        $lock->release();
        $refreshed = $lock->get();

        LockRefreshed::dispatch($name, $refreshed);

        return $refreshed;
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
