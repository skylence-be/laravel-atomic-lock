<?php

declare(strict_types=1);

namespace Theater\AtomicLock\Exceptions;

use Exception;

class LockException extends Exception
{
    public static function couldNotAcquire(string $name): self
    {
        return new self("Could not acquire lock [{$name}]");
    }

    public static function couldNotRelease(string $name): self
    {
        return new self("Could not release lock [{$name}]");
    }

    public static function notOwned(string $name): self
    {
        return new self("Lock [{$name}] is not owned by this process");
    }

    public static function timeout(string $name, int $seconds): self
    {
        return new self("Could not acquire lock [{$name}] within {$seconds} seconds");
    }
}
