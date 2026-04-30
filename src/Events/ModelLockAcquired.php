<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Skylence\AtomicLock\Models\Lock;

class ModelLockAcquired
{
    use Dispatchable;

    public function __construct(
        public readonly Model $model,
        public readonly Lock $lock,
    ) {}
}
