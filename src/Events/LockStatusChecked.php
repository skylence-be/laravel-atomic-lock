<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Skylence\AtomicLock\Support\LockStatus;

class LockStatusChecked
{
    use Dispatchable;

    public function __construct(
        public readonly string $name,
        public readonly LockStatus $status,
    ) {}
}
