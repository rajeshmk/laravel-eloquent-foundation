<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

trait HasOwner
{
    public static function ownerColumn(): string
    {
        return Config::get('eloquent-foundation.columns.owner', 'owner_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('eloquent-foundation.user_model'),
            static::ownerColumn()
        );
    }

    protected static function bootHasOwner(): void
    {
        static::creating(function (Model $model): void {
            if (
                Auth::check()
                && ! App::runningInConsole()
                && ! $model->{static::ownerColumn()}
            ) {
                $model->{static::ownerColumn()} = Auth::id();
            }
        });
    }
}
