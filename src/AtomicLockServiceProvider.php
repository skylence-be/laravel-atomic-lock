<?php

declare(strict_types=1);

namespace Skylence\AtomicLock;

use Illuminate\Support\ServiceProvider;
use Skylence\AtomicLock\Actions\AcquireLockAction;
use Skylence\AtomicLock\Actions\AcquireModelLockAction;
use Skylence\AtomicLock\Actions\AcquireOwnedLockAction;
use Skylence\AtomicLock\Actions\BlockingAcquireLockAction;
use Skylence\AtomicLock\Actions\CheckLockStatusAction;
use Skylence\AtomicLock\Actions\ForceReleaseLockAction;
use Skylence\AtomicLock\Actions\RefreshLockAction;
use Skylence\AtomicLock\Actions\ReleaseLockAction;
use Skylence\AtomicLock\Actions\ReleaseModelLockAction;
use Skylence\AtomicLock\Actions\RestoreLockAction;
use Skylence\AtomicLock\Actions\SearchWithLockAction;

class AtomicLockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . "/../config/atomic-lock.php",
            "atomic-lock",
        );

        $this->registerActions();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . "/../config/atomic-lock.php" => config_path(
                        "atomic-lock.php",
                    ),
                ],
                "atomic-lock-config",
            );

            $this->publishesMigrations(
                [
                    __DIR__ . "/../database/migrations" => database_path(
                        "migrations",
                    ),
                ],
                "atomic-lock-migrations",
            );
        }
    }

    protected function registerActions(): void
    {
        $this->app->bind(AcquireLockAction::class);
        $this->app->bind(AcquireModelLockAction::class);
        $this->app->bind(AcquireOwnedLockAction::class);
        $this->app->bind(BlockingAcquireLockAction::class);
        $this->app->bind(ReleaseLockAction::class);
        $this->app->bind(ReleaseModelLockAction::class);
        $this->app->bind(RestoreLockAction::class);
        $this->app->bind(ForceReleaseLockAction::class);
        $this->app->bind(RefreshLockAction::class);
        $this->app->bind(CheckLockStatusAction::class);
        $this->app->bind(SearchWithLockAction::class);
    }
}
