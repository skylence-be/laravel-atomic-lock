<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Theater\AtomicLock\Models\Lock;

class ModelLockReleased
{
    use Dispatchable;

    public function __construct(
        public readonly Model $model,
        public readonly Lock $lock,
    ) {}
}
