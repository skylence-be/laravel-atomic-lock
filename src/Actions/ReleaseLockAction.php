<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Actions;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Theater\AtomicLock\Events\LockReleased;
use Theater\AtomicLock\Support\Config;

class ReleaseLockAction
{
    public function execute(string $name, ?string $owner = null): bool
    {
        $lockName = $this->buildLockName($name);
        $lock = $this->getLock($lockName, owner: $owner);

        $released = $lock->release();

        LockReleased::dispatch($name, $released);

        return $released;
    }

    protected function buildLockName(string $name): string
    {
        $prefix = Config::getPrefix();

        return "{$prefix}:{$name}";
    }

    protected function getLock(string $name, ?string $owner): Lock
    {
        $store = Config::getCacheStore();
        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->lock($name, 0, $owner);
    }
}
