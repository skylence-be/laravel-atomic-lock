<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Actions;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Skylence\AtomicLock\Events\LockReleased;
use Skylence\AtomicLock\Support\Config;

class ForceReleaseLockAction
{
    public function execute(string $name): void
    {
        $lockName = $this->buildLockName($name);
        $lock = $this->getLock($lockName);

        $lock->forceRelease();

        LockReleased::dispatch($name, true);
    }

    protected function buildLockName(string $name): string
    {
        $prefix = Config::getPrefix();

        return "{$prefix}:{$name}";
    }

    protected function getLock(string $name): Lock
    {
        $store = Config::getCacheStore();
        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->lock($name, 0);
    }
}
