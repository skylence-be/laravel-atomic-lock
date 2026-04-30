<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Support;

final readonly class LockStatus
{
    public function __construct(
        public string $name,
        public bool $isLocked,
        public ?string $owner = null,
    ) {}

    public function isAvailable(): bool
    {
        return !$this->isLocked;
    }
}
