<?php

declare(strict_types=1);

namespace Skylence\AtomicLock\Support;

use InvalidArgumentException;

final class Config
{
    public static function getDefaultTtl(): int
    {
        return config("atomic-lock.default_ttl", 10);
    }

    public static function getCacheStore(): ?string
    {
        return config("atomic-lock.cache_store");
    }

    public static function getPrefix(): string
    {
        return config("atomic-lock.prefix", "atomic_lock");
    }

    public static function getActionClass(
        string $actionName,
        string $actionBaseClass,
    ): string {
        $configuredClass = config("atomic-lock.actions.{$actionName}");

        if ($configuredClass === null) {
            return $actionBaseClass;
        }

        if (!is_a($configuredClass, $actionBaseClass, true)) {
            throw new InvalidArgumentException(
                "Configured action [{$configuredClass}] for [{$actionName}] must extend [{$actionBaseClass}]",
            );
        }

        return $configuredClass;
    }

    public static function getAction(
        string $actionName,
        string $actionBaseClass,
    ): object {
        $actionClass = self::getActionClass($actionName, $actionBaseClass);

        return app($actionClass);
    }
}
