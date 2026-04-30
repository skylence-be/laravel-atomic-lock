<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Support;

final readonly class LockResult
{
    public function __construct(
        public bool $acquired,
        public string $name,
        public ?string $owner = null,
        public ?int $ttl = null,
    ) {}

    public function wasAcquired(): bool
    {
        return $this->acquired;
    }

    public function wasNotAcquired(): bool
    {
        return !$this->acquired;
    }
}
