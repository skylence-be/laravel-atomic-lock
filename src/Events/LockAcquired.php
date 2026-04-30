<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Skylence\AtomicLock\Support\LockResult;

class LockAcquired
{
    use Dispatchable;

    public function __construct(
        public readonly string $name,
        public readonly LockResult $result,
    ) {}
}
