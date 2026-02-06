<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Events;

use Illuminate\Foundation\Events\Dispatchable;

class LockRefreshed
{
    use Dispatchable;

    public function __construct(
        public readonly string $name,
        public readonly bool $refreshed,
    ) {}
}
