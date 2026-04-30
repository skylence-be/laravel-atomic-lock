<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Concerns;

use Closure;
use Skylence\AtomicLock\Actions\SearchWithLockAction;
use Skylence\AtomicLock\Support\Config;

trait WithSearchLock
{
    protected function searchWithLock(
        string $lockName,
        Closure $searchCallback,
        ?int $ttl = null,
    ): mixed {
        $action = Config::getAction(
            "search_with_lock",
            SearchWithLockAction::class,
        );

        return $action->execute($lockName, $searchCallback, $ttl);
    }

    protected function searchForWork(
        string $resourceType,
        Closure $findAndReserve,
        ?int $ttl = null,
    ): mixed {
        return $this->searchWithLock(
            lockName: "search:{$resourceType}",
            searchCallback: $findAndReserve,
            ttl: $ttl,
        );
    }
}
