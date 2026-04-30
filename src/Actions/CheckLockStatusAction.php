<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Actions;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Skylence\AtomicLock\Events\LockStatusChecked;
use Skylence\AtomicLock\Support\Config;
use Skylence\AtomicLock\Support\LockStatus;

class CheckLockStatusAction
{
    public function execute(string $name): LockStatus
    {
        $lockName = $this->buildLockName($name);
        $lock = $this->getLock($lockName);

        $owner = $lock->owner();
        $isLocked = $owner !== null;

        $status = new LockStatus(
            name: $name,
            isLocked: $isLocked,
            owner: $owner,
        );

        LockStatusChecked::dispatch($name, $status);

        return $status;
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
