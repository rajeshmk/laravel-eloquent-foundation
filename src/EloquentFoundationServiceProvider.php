<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation;

use Illuminate\Support\ServiceProvider;
use Override;

class EloquentFoundationServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/eloquent-foundation.php',
            'eloquent-foundation'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/eloquent-foundation.php' => config_path('eloquent-foundation.php'),
        ], 'eloquent-foundation-config');
    }
}
