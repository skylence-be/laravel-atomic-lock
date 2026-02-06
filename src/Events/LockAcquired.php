<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Theater\AtomicLock\Support\LockResult;

class LockAcquired
{
    use Dispatchable;

    public function __construct(
        public readonly string $name,
        public readonly LockResult $result,
    ) {}
}
