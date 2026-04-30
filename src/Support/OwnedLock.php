<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Support;

use Illuminate\Contracts\Cache\Lock;

final class OwnedLock
{
    public function __construct(
        public readonly string $name,
        public readonly string $owner,
        public readonly int $ttl,
        private readonly Lock $lock,
    ) {}

    public function release(): bool
    {
        return $this->lock->release();
    }

    public function forceRelease(): void
    {
        $this->lock->forceRelease();
    }

    public function token(): string
    {
        return $this->owner;
    }

    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "owner" => $this->owner,
            "ttl" => $this->ttl,
        ];
    }

    public function serialize(): string
    {
        return base64_encode(json_encode($this->toArray()));
    }

    public static function deserialize(string $serialized): array
    {
        return json_decode(base64_decode($serialized), true);
    }
}
