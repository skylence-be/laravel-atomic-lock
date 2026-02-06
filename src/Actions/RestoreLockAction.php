<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Actions;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Theater\AtomicLock\Support\Config;
use Theater\AtomicLock\Support\OwnedLock;

class RestoreLockAction
{
    public function execute(string $name, string $owner, ?int $ttl = null): OwnedLock
    {
        $ttl = $ttl ?? Config::getDefaultTtl();
        $lockName = $this->buildLockName($name);

        $lock = $this->getLock($lockName, $ttl, $owner);

        return new OwnedLock(
            name: $name,
            owner: $owner,
            ttl: $ttl,
            lock: $lock,
        );
    }

    public function fromSerialized(string $serialized): OwnedLock
    {
        $data = OwnedLock::deserialize($serialized);

        return $this->execute(
            name: $data['name'],
            owner: $data['owner'],
            ttl: $data['ttl'],
        );
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
