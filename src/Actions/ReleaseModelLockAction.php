<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Actions;

use Illuminate\Contracts\Cache\Lock as CacheLock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Theater\AtomicLock\Events\ModelLockReleased;
use Theater\AtomicLock\Models\Lock;
use Theater\AtomicLock\Support\Config;

class ReleaseModelLockAction
{
    public function execute(Model $model, ?string $owner = null): bool
    {
        $lockName = $this->buildLockName($model);

        // Release cache lock
        $cacheLock = $this->getCacheLock($lockName, $owner);
        $released = $cacheLock->release();

        if ($released) {
            // Update database record
            $lock = Lock::query()
                ->forLockable($model)
                ->active()
                ->when($owner, fn ($q) => $q->where('owner', $owner))
                ->first();

            if ($lock) {
                $lock->update(['released_at' => now()]);
                ModelLockReleased::dispatch($model, $lock);
            }
        }

        return $released;
    }

    protected function buildLockName(Model $model): string
    {
        $prefix = Config::getPrefix();
        $type = $model->getMorphClass();
        $id = $model->getKey();

        return "{$prefix}:model:{$type}:{$id}";
    }

    protected function getCacheLock(string $name, ?string $owner): CacheLock
    {
        $store = Config::getCacheStore();
        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->lock($name, 0, $owner);
    }
}
