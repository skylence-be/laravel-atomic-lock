<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Concerns;

use Closure;
use Theater\AtomicLock\Actions\SearchWithLockAction;
use Theater\AtomicLock\Support\Config;

trait WithSearchLock
{
    protected function searchWithLock(string $lockName, Closure $searchCallback, ?int $ttl = null): mixed
    {
        $action = Config::getAction('search_with_lock', SearchWithLockAction::class);

        return $action->execute($lockName, $searchCallback, $ttl);
    }

    protected function searchForWork(string $resourceType, Closure $findAndReserve, ?int $ttl = null): mixed
    {
        return $this->searchWithLock(
            lockName: "search:{$resourceType}",
            searchCallback: $findAndReserve,
            ttl: $ttl,
        );
    }
}
