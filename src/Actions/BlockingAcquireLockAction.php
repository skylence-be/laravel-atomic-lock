<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Actions;

use Closure;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Theater\AtomicLock\Events\LockAcquired;
use Theater\AtomicLock\Exceptions\LockException;
use Theater\AtomicLock\Support\Config;
use Theater\AtomicLock\Support\LockResult;

class BlockingAcquireLockAction
{
    public function execute(
        string $name,
        int $waitSeconds,
        ?int $ttl = null,
        ?string $owner = null,
        ?Closure $callback = null,
    ): LockResult {
        $ttl = $ttl ?? Config::getDefaultTtl();
        $lockName = $this->buildLockName($name);

        $lock = $this->getLock($lockName, $ttl, $owner);

        try {
            if ($callback !== null) {
                $lock->block($waitSeconds, $callback);

                $result = new LockResult(
                    acquired: true,
                    name: $name,
                    owner: $owner,
                    ttl: $ttl,
                );

                LockAcquired::dispatch($name, $result);

                return $result;
            }

            $lock->block($waitSeconds);

            $result = new LockResult(
                acquired: true,
                name: $name,
                owner: $owner,
                ttl: $ttl,
            );

            LockAcquired::dispatch($name, $result);

            return $result;
        } catch (LockTimeoutException) {
            throw LockException::timeout($name, $waitSeconds);
        }
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
