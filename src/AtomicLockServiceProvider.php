<?php

declare(strict_types=1);

namespace Theater\AtomicLock;

use Illuminate\Support\ServiceProvider;
use Theater\AtomicLock\Actions\AcquireLockAction;
use Theater\AtomicLock\Actions\AcquireModelLockAction;
use Theater\AtomicLock\Actions\AcquireOwnedLockAction;
use Theater\AtomicLock\Actions\BlockingAcquireLockAction;
use Theater\AtomicLock\Actions\CheckLockStatusAction;
use Theater\AtomicLock\Actions\ForceReleaseLockAction;
use Theater\AtomicLock\Actions\RefreshLockAction;
use Theater\AtomicLock\Actions\ReleaseLockAction;
use Theater\AtomicLock\Actions\ReleaseModelLockAction;
use Theater\AtomicLock\Actions\RestoreLockAction;
use Theater\AtomicLock\Actions\SearchWithLockAction;

class AtomicLockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/atomic-lock.php',
            'atomic-lock'
        );

        $this->registerActions();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/atomic-lock.php' => config_path('atomic-lock.php'),
            ], 'atomic-lock-config');

            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'atomic-lock-migrations');
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
