<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Actions;

use Illuminate\Contracts\Cache\Lock as CacheLock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Theater\AtomicLock\Events\ModelLockAcquired;
use Theater\AtomicLock\Models\Lock;
use Theater\AtomicLock\Support\Config;
use Theater\AtomicLock\Support\LockResult;

class AcquireModelLockAction
{
    public function execute(
        Model $model,
        ?int $ttl = null,
        ?string $owner = null,
        ?string $reason = null,
    ): LockResult {
        $ttl = $ttl ?? Config::getDefaultTtl();
        $lockName = $this->buildLockName($model);

        // Use cache lock for atomicity
        $cacheLock = $this->getCacheLock($lockName, $ttl, $owner);

        $acquired = $cacheLock->get();

        if ($acquired) {
            // Track in database for history/relationships
            $lock = Lock::create([
                'lockable_type' => $model->getMorphClass(),
                'lockable_id' => $model->getKey(),
                'owner' => $owner,
                'reason' => $reason,
                'acquired_at' => now(),
                'expires_at' => now()->addSeconds($ttl),
            ]);

            ModelLockAcquired::dispatch($model, $lock);
        }

        return new LockResult(
            acquired: $acquired,
            name: $lockName,
            owner: $owner,
            ttl: $ttl,
        );
    }

    protected function buildLockName(Model $model): string
    {
        $prefix = Config::getPrefix();
        $type = $model->getMorphClass();
        $id = $model->getKey();

        return "{$prefix}:model:{$type}:{$id}";
    }

    protected function getCacheLock(string $name, int $ttl, ?string $owner): CacheLock
    {
        $store = Config::getCacheStore();
        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->lock($name, $ttl, $owner);
    }
}
