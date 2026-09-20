<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

trait HasUpdater
{
    public static function updaterColumn(): string
    {
        return Config::get('eloquent-foundation.columns.updater', 'updated_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('eloquent-foundation.user_model'),
            static::updaterColumn()
        );
    }

    protected static function bootHasUpdater(): void
    {
        static::updating(function (Model $model): void {
            if (Auth::check()) {
                $model->{static::updaterColumn()} = Auth::id();
            }
        });

        static::creating(function ($model): void {
            if (! Auth::check() || App::runningInConsole()) {
                return;
            }

            if (! Config::get('eloquent-foundation.sync_updater_on_create', false)) {
                return;
            }

            $model->{static::updaterColumn()} = Auth::id();
        });
    }
}
